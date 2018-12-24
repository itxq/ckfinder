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

use itxq\ckfinder\tools\SingleModelTrait;

/**
 * CkFinder文件管理器
 * Class CkFinder
 * @package itxq\ckfinder
 */
class CkFinder
{
    use SingleModelTrait;
    
    /**
     * 常量 公开存储空间名称
     */
    const PUBLIC_BACKEND = 'public';
    
    /**
     * 常量 私密存储空间名称
     */
    const PRIVATE_BACKEND = 'private';
    
    /**
     * 本地存储空间
     */
    const ADAPTER_LOCAL = 'local';
    
    /**
     * 又拍云存储空间
     */
    const ADAPTER_UPY = 'upy';
    
    /**
     * @var string - 默认根目录
     */
    protected $rootDir = __DIR__ . '/../uploads';
    
    /**
     * @var string - 默认URL根路径
     */
    protected $baseUrl = '';
    
    /**
     * CkFinder 构造函数. 禁止直接实例化该类
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
     * 添加资源目录
     * @param string $name - 显示名称
     * @param string $directory - 目录路径
     * @param string $backend - 所属存储空间
     * @param array $config - 更多配置
     * @return CkFinder
     */
    public function addResource($name, $directory, $backend, $config = []) {
        $config['name'] = $name;
        $config['directory'] = $directory;
        $config['backend'] = $backend;
        $this->config['resourceTypes'][] = $config;
        return $this;
    }
    
    /**
     * 添加存储空间
     * @param string $name - 名称
     * @param string $adapter - 存储空间类型（local-本地存储；upy-又拍云存储）
     * @param $config - 其他配置
     * @return CkFinder
     */
    public function addBackend($name, $adapter = self::ADAPTER_LOCAL, $config = []) {
        $defaultConfig = ['chmodFiles' => 0777, 'chmodFolders' => 0755, 'filesystemEncoding' => 'UTF-8'];
        $config['name'] = $name;
        $config['adapter'] = $adapter;
        $this->config['backends'][] = array_merge($defaultConfig, $config);
        return $this;
    }
    
    /**
     * 设置PrivateDirKey
     * @param string $key - （可用于区分不同用户的缓存目录，建议使用用户ID）
     * @return CkFinder
     */
    public function setPrivateDirKey($key) {
        $this->config['private_dir_key'] = $key;
        return $this;
    }
    
    /**
     * 添加配置
     * @param string $name - 配置项名称
     * @param mixed $value - 配置项的值
     * @return CkFinder
     */
    public function setConfig($name, $value) {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        } else {
            $this->config[$name] = $value;
        }
        return $this;
    }
    
    /**
     * 外部调用接口
     */
    public function run() {
        $this->config();
        $ckFinder = new \CKSource\CKFinder\CKFinder($this->config);
        $ckFinder->run();
        exit();
    }
    
    /**
     * 配置整合
     */
    protected function config() {
        $sysTempDir = sys_get_temp_dir();
        $config = include __DIR__ . '/../config/config.php';
        $this->config = array_merge($config, $this->config);
        $this->config['tempDirectory'] = !is_writable($sysTempDir) ? __DIR__ : $sysTempDir;
        // 存储空间设置
        if (!isset($this->config['backends'])) {
            $publicBackend = [
                'name'               => self::PUBLIC_BACKEND,
                'adapter'            => 'local',
                'baseUrl'            => $this->baseUrl . '/' . self::PUBLIC_BACKEND,
                'root'               => $this->rootDir . '/' . self::PUBLIC_BACKEND . '/',
                'chmodFiles'         => 0777,
                'chmodFolders'       => 0755,
                'filesystemEncoding' => 'UTF-8'
            ];
            $privateBackend = [
                'name'               => self::PRIVATE_BACKEND,
                'adapter'            => 'local',
                'baseUrl'            => $this->baseUrl . '/' . self::PRIVATE_BACKEND,
                'root'               => $this->rootDir . '/' . self::PRIVATE_BACKEND . '/',
                'chmodFiles'         => 0777,
                'chmodFolders'       => 0755,
                'filesystemEncoding' => 'UTF-8'
            ];
            $this->config['backends'] = [$publicBackend, $privateBackend];
        }
        // 资源目录设置
        if (!isset($this->config['resourceTypes'])) {
            $publicResourceType = [
                'name'              => '公开',
                'directory'         => 'files',
                'maxSize'           => 0,
                'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                'deniedExtensions'  => '',
                'backend'           => self::PUBLIC_BACKEND
            ];
            $privateResourceType = [
                'name'              => '私密',
                'directory'         => 'files',
                'maxSize'           => 0,
                'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                'deniedExtensions'  => '',
                'backend'           => self::PRIVATE_BACKEND
            ];
            $this->config['resourceTypes'] = [$publicResourceType, $privateResourceType];
        }
        // 设置PrivateDir
        $this->setPrivateDir();
    }
    
    /**
     * 设置PrivateDir
     */
    protected function setPrivateDir() {
        $root = realpath(__DIR__ . '/../runtime') . DIRECTORY_SEPARATOR;
        if (isset($this->config['private_dir_key']) && !empty($this->config['private_dir_key'])) {
            $root .= $this->config['private_dir_key'] . DIRECTORY_SEPARATOR;
        }
        $this->config['backends'][] = [
            'name'               => 'ckfinder_cache',
            'adapter'            => 'local',
            'baseUrl'            => '',
            'root'               => $root,
            'chmodFiles'         => 0777,
            'chmodFolders'       => 0755,
            'filesystemEncoding' => 'UTF-8'
        ];
        $this->config['privateDir'] = [
            'backend' => 'ckfinder_cache',
            'tags'    => '.ckfinder/tags',
            'logs'    => '.ckfinder/logs',
            'cache'   => '.ckfinder/cache',
            'thumbs'  => '.ckfinder/cache/thumbs',
        ];
    }
}