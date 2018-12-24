<?php

// +----------------------------------------------------------------------
// | 默认配置
// +----------------------------------------------------------------------

return [
    'debug'                    => false,
    'authentication'           => function () {
        return true;
    },
    'images'                   => [
        'maxWidth'  => 20000,
        'maxHeight' => 20000,
        'quality'   => 100,
        'sizes'     => [
            'small'  => ['width' => 480, 'height' => 320, 'quality' => 80],
            'medium' => ['width' => 600, 'height' => 480, 'quality' => 80],
            'large'  => ['width' => 800, 'height' => 600, 'quality' => 80]
        ]
    ],
    'cache'                    => [
        'imagePreview' => 24 * 3600,
        'thumbnails'   => 24 * 3600 * 365,
        'proxyCommand' => 0
    ],
    'sessionWriteClose'        => true,
    'csrfProtection'           => true,
    'headers'                  => [],
    'pluginsDirectory'         => __DIR__ . '/../plugins',
    'plugins'                  => ['Upy'],
    'overwriteOnUpload'        => false,
    'checkDoubleExtension'     => true,
    'disallowUnsafeCharacters' => false,
    'secureImageUploads'       => true,
    'checkSizeAfterScaling'    => true,
    'htmlExtensions'           => ['html', 'htm', 'xml', 'js'],
    'hideFolders'              => ['.*', 'CVS', '__thumbs'],
    'hideFiles'                => ['.*'],
    'forceAscii'               => false,
    'xSendfile'                => false,
    'roleSessionVar'           => 'CKFinder_UserRole',
    'accessControl'            => [
        [
            'role'         => '*',
            'resourceType' => '*',
            'folder'       => '/',
            
            'FOLDER_VIEW'   => true,
            'FOLDER_CREATE' => true,
            'FOLDER_RENAME' => true,
            'FOLDER_DELETE' => true,
            
            'FILE_VIEW'   => true,
            'FILE_CREATE' => true,
            'FILE_RENAME' => true,
            'FILE_DELETE' => true,
            
            'IMAGE_RESIZE'        => true,
            'IMAGE_RESIZE_CUSTOM' => true
        ]
    ],
    'defaultResourceTypes'     => ''
];
