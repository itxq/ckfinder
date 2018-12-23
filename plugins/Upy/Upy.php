<?php
/**
 *  ==================================================================
 *        文 件 名: Upy.php
 *        概    要: 又拍云存储插件
 *        作    者: IT小强
 *        创建时间: 2018-12-23 17:38:57
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace CKSource\CKFinder\Plugin\Upy;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Plugin\PluginInterface;
use itxq\ckfinder\tools\UpyAdapter;

/**
 * 又拍云存储插件
 * Class Upy
 * @package CKSource\CKFinder\Plugin\Upy
 */
class Upy implements PluginInterface
{
    /**
     * Injects the DI container to the plugin.
     * @param CKFinder $app
     * @return bool
     */
    public function setContainer(CKFinder $app) {
        $backendFactory = $app->getBackendFactory();
        // 注册又拍云储存空间
        $backendFactory->registerAdapter('upy', function ($backendConfig) use ($backendFactory) {
            $adapter = new UpyAdapter($backendConfig);
            return $backendFactory->createBackend($backendConfig, $adapter);
        });
        return true;
    }
    
    /**
     * 默认配置
     * @return array
     */
    public function getDefaultConfig() {
        return [];
    }
}