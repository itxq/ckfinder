<?php
/**
 *  ==================================================================
 *        文 件 名: AutoRename.php
 *        概    要: 自动重命名（用于过滤汉字以及其他特殊符号）
 *        作    者: IT小强
 *        创建时间: 2018-12-23 16:06:22
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\ckfinder\tools;

use CKSource\CKFinder\CKFinder;

/**
 * 自动重命名（用于过滤汉字以及其他特殊符号）
 * Class AutoRename
 * @package itxq\ckfinder\tools
 */
class AutoRename
{
    use SingleModelTrait;
    
    public const FILE = 'file';
    
    public const FOLDER = 'folder';
    
    /**
     * @var - CKFinder 配置
     */
    protected $defaultConfig = ['folder' => false, 'file' => false];
    
    /**
     * 获取 CKFinder 配置
     * @param CKFinder $app
     * @return $this
     */
    public function config(CKFinder $app): AutoRename
    {
        $config = $app['config']->get('auto_rename');
        $this->iniConfig($config);
        return $this;
    }
    
    /**
     * 自动重命名重命名
     * @param string $name - 原名称
     * @param string $extension - 扩展名
     * @return mixed|string
     */
    public function autoRename($name, $extension = '')
    {
        if (empty($extension) && $this->config[self::FOLDER] === false) {
            return $name;
        }
        if (!empty($extension) && $this->config[self::FILE] === false) {
            return $name;
        }
        $name = PinYin::ins()->turn($name, false, '', 'utf-8');
        // uuid 用于名称为空时
        $name = empty($name) ? $this->uuid() : strtolower($name);
        if ($name === '.' . $extension) {
            $name = $this->uuid() . (empty($extension) ? '' : $name);
        }
        return $name;
    }
    
    /**
     * 生成uuid
     * @return string
     */
    private function uuid(): string
    {
        $charId = md5(uniqid(mt_rand(), true));
        $uuid = substr($charId, 0, 8)
            . substr($charId, 8, 4)
            . substr($charId, 12, 4)
            . substr($charId, 16, 4)
            . substr($charId, 20, 12);
        return strtolower($uuid);
    }
    
    /**
     * 初始化配置信息
     * @param array $config - CKFinder 配置
     */
    private function iniConfig(array $config): void
    {
        $this->config = $this->defaultConfig;
        if (is_array($config) && isset($config['file'], $config['folder'])) {
            $this->config['file'] = (bool)$config['file'];
            $this->config['folder'] = (bool)$config['folder'];
        } else if ($config === true || $config === false) {
            $this->config['file'] = $config;
            $this->config['folder'] = $config;
        }
    }
}
