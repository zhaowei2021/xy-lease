require.config({
    paths: {
		'xylease_vue': '../addons/xylease/libs/vue/vue.min',
        'xylease_sortablejs': '../addons/xylease/js/Sortable.min',
        'xylease_vuedraggable': '../addons/xylease/libs/vue/vuedraggable.umd.min',
    },
    shim: {
        'xylease_vue': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        },
        'xylease_sortablejs': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        },
        'xylease_vuedraggable': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        },
    }
});