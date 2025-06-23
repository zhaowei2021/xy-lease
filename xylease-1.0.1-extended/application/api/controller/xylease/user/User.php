<?php

namespace app\api\controller\xylease\user;
use app\api\model\xylease\Config;
use addons\xylease\library\Decrypt\weixin\wxBizDataCrypt;
use app\common\controller\Api;
use app\api\model\xylease\user\User as UserModel;
use fast\Random;
use fast\Http;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['phone'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        parent::_initialize();
		$this->auth->setAllowFields(['id','username','nickname','mobile','avatar','level','xylease_parent_user_id','xylease_consume','xylease_recharge','gender','birthday','bio','money','score','successions','maxsuccessions','prevtime','logintime','loginip','jointime','status']);
    }
	

	/**
     * 退出登录
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }
	
    
    /**
     * 手机号授权登录
     */
    public function phone()
    {
        //设置过滤方法
		$this->request->filter(['strip_tags']);
		if ($this->request->isPost()) {

			$post = $this->request->post();
			$platform = request()->header('platform');
		    if (!isset($post['iv'])) {
		        $this->error(__('获取手机号异常'));
		    }
		    // 获取配置
		    $config = Config::getValueByName('wxminiprogram');

			if(empty($config['app_id']) || empty($config['secret'])){
				$this->error(__('请到后台配置中心平台配置中配置微信小程序AppID和AppSecret'));
			}

	        $params = [
			    'appid'    => $config['app_id'],
			    'secret'   => $config['secret'],
			    'js_code'  => $post['code'],
			    'grant_type' => 'authorization_code'
			];

			$result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET');
			$json = (array)json_decode($result['msg'], true);

			if(!isset($json['openid'])){
				$this->error(__('小程序AppID或AppSecret配置错误'));
			}

			// 手机号解码
			$encryptedData = request()->post('encryptedData','','trim');
			$decrypt = new wxBizDataCrypt($params['appid'], $json['session_key']);
			$decrypt->decryptData($encryptedData, $post['iv'], $data);
			$data = (array)json_decode($data, true);

			
			$ret = \think\Db::transaction(function () use ($json,$platform,$data) {
				$isInfo = 0;

				// 判断third是否存在,存在快速登录
				$third = model('app\api\model\xylease\Third')->get(['platform' => $platform, 'openid' => $json['openid']]);
				
				if ($third && $third['user_id'] != 0) {
					//如果已经有账号则直接登录
					$isInfo = 2;
					$third->save([
						'openid' => $json['openid'],
						'logintime' => time(),
					]);
					// 开始登录
					$user = UserModel::where(['id'=>$third['user_id']])->find();
					if(!empty($user) && $user->status != 'normal'){
						$this->error(__('账号已被禁用'));
					}
					if(!empty($user) && $user['mobile'] == ''){
						$user->mobile = $data['phoneNumber'];
						$user->save();
					}
					$this->auth->direct($third['user_id']);
				} else {
					
					// 开始登录
					$mobile = $data['phoneNumber'];
					$user = \app\common\model\User::getByMobile($mobile);
					if ($user) {
						if ($user->status != 'normal') {
							$this->error(__('账号已被禁用'));
						}
						//如果已经有账号则直接登录
						$isInfo = 2;
						$this->auth->direct($user->id);
					} else {
						$isInfo = 1;
						$this->auth->register('U'.Random::alnum(6), Random::alnum(), '', $mobile, [
							'nickname' => 'U-'.Random::nozero(6), 
							'avatar' => Config::getValueByName('xylease')['useravatar']?Config::getValueByName('xylease')['useravatar']:''
						]);

						//注册后事件
						$user = $this->auth->getUser();
						\think\Hook::listen('xylease_register_after',$user);
					
					}

					// 新增$third
					$third = model('app\api\model\xylease\Third');
					$third->platform  = $platform;
					$third->openid  = $json['openid'];
					$third->session_key  = $json['session_key'];
					$third->logintime  = time();
					$third->user_id  = $this->auth->id;
					$third->save();
					
				}
				return $isInfo;
				
			});	
			
		    if ($ret) {
    			$this->success(__('Logged in successful'), self::userInfo($ret));
    		} else {
    			$this->error($this->auth->getError());
    		}
		}
		$this->error(__('非法请求'));
    }

	/**
	 * 刷新用户信息
	 */
	public function refresh()
	{
		//设置过滤方法
		$this->request->filter(['strip_tags']);
		if ($this->request->isPost()) {
			$this->success(__('刷新成功'), self::userInfo());
		}
		$this->error(__('非法请求'));
	}

	/**
     * 修改信息
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $nickname = $this->request->post('nickname');
        $avatar = $this->request->post('avatar');
		$mobile = $this->request->post('mobile');
		
		if(!empty($mobile) && $mobile != $user['mobile']){
			$userE = UserModel::where(['mobile'=>$mobile])->find();
			if(!empty($userE)){
				$this->error(__('手机号已存在'));
			}
			$user->mobile = $mobile;
		}
        
        $user->nickname = $nickname;
		
        if (!empty($avatar)) {
            $user->avatar = $avatar;
        }
        $user->save();
        $this->success('修改成功',self::userInfo());
    }
	

	/**
	 * 登录后返回用户信息
	 */
	private function userInfo($isInfo = 0)
	{
		// 获取配置
		$userInfo = $this->auth->getUserinfo();
		$userInfo['is_info'] = $isInfo;
		$this->success('用户信息',[
			'userInfo' => $userInfo,
		]);
	}
	
}