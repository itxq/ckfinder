CKFinder.define(['jquery'], function (jQuery) {
    'use strict';

    return {
        init: function (finder) {
           finder.on('toolbar:reset:Main', function (evt) {
                evt.data.toolbar.push({
                    name: 'clear_cache',
                    label: '清理缓存',
                    priority: 0,
                    icon: 'ckf-file-delete',
                    action: clearCache
                });
            });


            finder.on('contextMenu', function (evt) {
                evt.data.groups.add({name: 'default'});
            });

            finder.on('contextMenu:file:default', onContextMenuGroup);
            finder.on('contextMenu:folder:default', onContextMenuGroup);

            function onContextMenuGroup(evt) {
                evt.data.items.add({
                    name: 'clear_cache',
                    label: '清理缓存',
                    icon: 'ckf-file-delete',
                    isActive: true,
                    action: clearCache
                });
            }

            function clearCache() {
                localStorage.removeItem('ckf.settings');
                finder.request('dialog:info', {
                    msg: '缓存清理成功'
                });
            }
        }
    };
});