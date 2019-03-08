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
    protected function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * @title 初始化加载
     * @author IT小强
     * @createTime 2019-03-05 20:56:55
     */
    protected function initialize(): void
    {
    }
    
    /**
     * @title 单利模式 - 返回本类对象
     * @author IT小强
     * @createTime 2019-03-05 20:40:34
     * @param array $config - 配置信息
     * @param bool $force - 是否强制重新实例化
     * @return static
     */
    public static function ins(array $config = [], bool $force = false)
    {
        $className = static::class;
        if ($force === true || !isset(self::$instances[$className]) || !self::$instances[$className] instanceof $className) {
            $instance = new $className($config);
            self::$instances[$className] = $instance;
        }
        return self::$instances[$className];
    }
    
    /**
     * @tile 设置配置
     * @author IT小强
     * @createTime 2019-03-05 20:37:37
     * @param string|array $key 配置项名称
     * @param mixed $value 配置项值
     * @return static
     */
    public function setConfig($key, $value)
    {
        if (is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $value;
        }
        return self::$instances[static::class];
    }
    
    /**
     * @title 获取配置
     * @author IT小强
     * @createTime 2019-03-05 20:55:59
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
    
    /**
     * @title 获取反馈信息
     * @author IT小强
     * @createTime 2019-03-05 20:39:55
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @title 克隆防止继承
     * @author IT小强
     * @createTime 2019-03-05 20:39:41
     */
    final private function __clone()
    {
    
    }
}
