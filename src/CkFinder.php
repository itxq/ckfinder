<?php
/**
 *  ==================================================================
 *        文 件 名: CkFinder.php
 *        概    要: CkFinder文件管理器
 *        作    者: IT小强
 *        创建时间: 2018-12-21 19:58:52
 *        修改时间: 2018-12-21 19:58:52
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
    
    protected $backend = 'uploads';
    
    protected $rootDir = __DIR__ . '/../uploads';
    
    protected $baseUrl = '';
    
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
     * 调用接口
     * @param string $backend - 存储空间
     * @param string $rootDir
     * @param string $baseUrl
     */
    public function run($backend = '', $rootDir = '', $baseUrl = '') {
        if (!empty($backend)) {
            $this->backend = $backend;
        }
        if (!empty($baseUrl)) {
            $this->baseUrl = $baseUrl;
        }
        $rootDir = $this->checkDir($rootDir);
        if (is_dir($rootDir)) {
            $this->rootDir = $rootDir;
        }
        $ckFinder = new \CKSource\CKFinder\CKFinder(array_merge($this->defaultConfig(), $this->config));
        $ckFinder->run();
        exit();
    }
    
    /**
     * 获取默认配置
     * @return mixed
     */
    protected function defaultConfig() {
        $sysTempDir = sys_get_temp_dir();
        $config = include __DIR__ . '/../config/config.php';
        $config['privateDir']['backend'] = $this->backend;
        $config['tempDirectory'] = !is_writable($sysTempDir) ? __DIR__ : $sysTempDir;
        $config['backends'] = [
            ['name'               => $this->backend,
             'adapter'            => 'local',
             'baseUrl'            => $this->baseUrl,
             'root'               => $this->rootDir,
             'chmodFiles'         => 0777,
             'chmodFolders'       => 0755,
             'filesystemEncoding' => 'UTF-8'
            ]
        ];
        $config['resourceTypes'] = [
            'file' => [
                'name'              => '资源', // Single quotes not allowed.
                'directory'         => 'files',
                'maxSize'           => 0,
                'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                'deniedExtensions'  => '',
                'backend'           => $this->backend
            ],
            'img'  => [
                'name'              => '图像',
                'directory'         => 'images',
                'maxSize'           => 0,
                'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
                'deniedExtensions'  => '',
                'backend'           => $this->backend
            ]
        ];
        return $config;
    }
    
    /**
     * 检查目录，不存在则创建
     * @param $dir - 路径
     * @return bool|string - 返回路径、创建失败会返回false
     */
    protected function checkDir($dir) {
        if (empty($dir)) {
            return false;
        }
        if (is_dir($dir)) {
            return realpath($dir) . DIRECTORY_SEPARATOR;
        }
        if (!mkdir($dir, 0777, true)) {
            return false;
        }
        return realpath($dir) . DIRECTORY_SEPARATOR;
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