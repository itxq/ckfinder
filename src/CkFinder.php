<?php
/**
 *  ==================================================================
 *        文 件 名: CkFinder.php
 *        概    要: CkFinder文件管理器
 *        作    者: IT小强
 *        创建时间: 2018-12-21 19:58:52
 *        修改时间: 2018-12-23 00:28:50
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\ckfinder;

/**
 * CkFinder文件管理器
 * Class CkFinder
 * @package itxq\ckfinder
 */
class CkFinder
{
    use CkFinderTrait;
    
    /**
     * 常量 公开存储空间名称
     */
    const PUBLIC_BACKEND = 'public';
    
    /**
     * 常量 私密存储空间名称
     */
    const PRIVATE_BACKEND = 'private';
    
    /**
     * @var string - 默认根目录
     */
    protected $rootDir = __DIR__ . '/../uploads';
    
    /**
     * @var string - 默认URL根路径
     */
    protected $baseUrl = '/';
    
    /**
     * SingleModelTrait 构造函数. 禁止直接实例化该类
     * @param array|mixed $config - 配置信息
     */
    protected function __construct($config = []) {
        $constName = '__CK_AUTOLOAD__';
        if (!defined($constName)) {
            define($constName, 1);
            require __DIR__ . '/../core/autoload.php';
        }
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * 外部调用接口
     * @param array $publicConfig - 公开存储空间设置
     * @param array|false $privateConfig - 私密存储空间设置(false表示不启用私密存储空间)
     */
    public function run($publicConfig = [], $privateConfig = []) {
        $ckFinder = new \CKSource\CKFinder\CKFinder(array_merge($this->config($publicConfig, $privateConfig), $this->config));
        $ckFinder->run();
        exit();
    }
    
    /**
     * 初始化默认配置并整合用户自定义配置
     * @param array $publicConfig - 公开存储空间设置
     * @param array|false $privateConfig - 私密存储空间设置(false表示不启用私密存储空间)
     * @return array
     */
    protected function config($publicConfig = [], $privateConfig = []) {
        $sysTempDir = sys_get_temp_dir();
        $config = include __DIR__ . '/../config/config.php';
        $config['privateDir']['backend'] = isset($this->config['backend']) ? $this->config['backend'] : self::PUBLIC_BACKEND;
        $config['tempDirectory'] = !is_writable($sysTempDir) ? __DIR__ : $sysTempDir;
        
        // 存储空间设置
        if (isset($this->config['backends'])) {
            return $config;
        }
        $publicBackend = array_merge([
            'name'               => self::PUBLIC_BACKEND,
            'adapter'            => 'local',
            'baseUrl'            => $this->baseUrl,
            'root'               => $this->rootDir,
            'chmodFiles'         => 0777,
            'chmodFolders'       => 0755,
            'filesystemEncoding' => 'UTF-8'
        ], (array)$publicConfig);
        $config['backends'] = [$publicBackend];
        if ($privateConfig !== false) {
            $privateBackend = array_merge([
                'name'               => self::PRIVATE_BACKEND,
                'adapter'            => 'local',
                'baseUrl'            => $this->baseUrl,
                'root'               => $this->rootDir . '/../' . self::PRIVATE_BACKEND . '_uploads',
                'chmodFiles'         => 0777,
                'chmodFolders'       => 0755,
                'filesystemEncoding' => 'UTF-8'
            ], (array)$privateConfig);
            $config['backends'][] = $privateBackend;
        }
        
        // 资源目录设置
        if (isset($this->config['resourceTypes'])) {
            return $config;
        }
        $publicResourceType = [
            'name'              => '公开',
            'directory'         => self::PUBLIC_BACKEND . '_files',
            'maxSize'           => 0,
            'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
            'deniedExtensions'  => '',
            'backend'           => self::PUBLIC_BACKEND
        ];
        $config['resourceTypes'] = [$publicResourceType];
        if ($privateConfig !== false) {
            $config['resourceTypes'][] = [
                'name'              => '私密',
                'directory'         => self::PRIVATE_BACKEND . '_files',
                'maxSize'           => 0,
                'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                'deniedExtensions'  => '',
                'backend'           => self::PRIVATE_BACKEND
            ];
        }
        return $config;
    }
}

/**
 * 单例设计模式 Trait
 * Trait CkFinderTrait
 * @package itxq\ckfinder
 */
trait CkFinderTrait
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
     * CkFinderTrait 构造函数. 禁止直接实例化该类
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
    public static function ins($config = [], $force = true) {
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