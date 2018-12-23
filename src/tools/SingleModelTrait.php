<?php
/**
 *  ==================================================================
 *        文 件 名: SingleModelTrait.php
 *        概    要: 单例设计
 *        作    者: IT小强
 *        创建时间: 2018-12-23 16:04:55
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\ckfinder\tools;


/**
 * 单例设计模式 Trait
 * Trait SingleModelTrait
 * @package itxq\ckfinder\tools
 */
trait SingleModelTrait
{
    /**
     * @var array - 实例
     */
    protected static $instances = [];
    
    /**
     * @var array - 配置信息
     */
    protected $config = [];
    
    /**
     * @var string|array - 反馈信息
     */
    protected $message = '';
    
    /**
     * SingleModelTrait 构造函数. 禁止直接实例化该类
     * @param array|mixed $config - 配置信息
     */
    protected function __construct($config = []) {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * 单利模式 - 返回本类对象
     * @param array $config - 配置信息
     * @param bool $force - 是否强制重新实例化
     * @return static
     */
    public static function ins($config = [], $force = false) {
        $className = get_called_class();
        if (!isset(self::$instances[$className]) || !self::$instances[$className] instanceof $className || $force === true) {
            $instance = new $className($config);
            self::$instances[$className] = $instance;
        }
        return self::$instances[$className];
    }
    
    /**
     * 获取反馈信息
     * @return string|array
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * 克隆防止继承
     */
    final private function __clone() {
        // fixme  禁止克隆
    }
}