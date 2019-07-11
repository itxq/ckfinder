<?php
/**
 *  ==================================================================
 *        文 件 名: UpyAdapter.php
 *        概    要: 又拍云储存
 *        作    者: IT小强
 *        创建时间: 2018-12-23 17:27
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\ckfinder\tools;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use League\Flysystem\Util;
use Upyun\Upyun;

/**
 * 又拍云储存
 * Class UpyAdapter
 * @package CKSource\CKFinder\Plugin\UploadAdapter
 */
class UpyAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    /**
     * @var array - 配置信息
     */
    protected $config = [];

    /**
     * @var Upyun
     */
    protected $client;

    /**
     * @var string - 又拍云远程路径前缀
     */
    protected $pathPrefix = '/';

    /**
     * 又拍云储存
     * UpyAdapter 构造函数.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->config     = $config;
        $this->pathPrefix = $this->getSubValue('root', $config, '');
        $serviceName      = $this->getSubValue('service', $config, '');
        $operatorName     = $this->getSubValue('operator', $config, '');
        $operatorPassword = $this->getSubValue('password', $config, '');
        $serviceConfig    = new \Upyun\Config($serviceName, $operatorName, $operatorPassword);
        $this->client     = new Upyun($serviceConfig);
    }

    /**
     * 拉取文件信息
     * @param string $directory - 目录
     * @param bool   $recursive
     * @return array
     * @throws \Exception
     */
    public function listContents($directory = '', $recursive = false): array
    {
        $directory = $this->applyPathPrefix($directory);
        if ($directory !== '') {
            $directory = rtrim($directory, '/') . '/';
        }
        $read     = $this->readContent($directory, 'dir');
        $contents = [];
        if (is_array($read) && count($read) >= 1) {
            foreach ($read as $k => $v) {
                $contents[] = $this->getInfoByReadContent($v, $directory);
            }
        }
        return $recursive ? $contents : Util::emulateDirectories($contents);
    }

    /**
     * 写入
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path, $contents);
    }

    /**
     * 写入
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     * @return array|bool|false
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->upload($path, $resource);
    }

    /**
     * 更新
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     */
    public function update($path, $contents, Config $config)
    {
        return $this->upload($path, $contents);
    }

    /**
     * 更新
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     * @return array|bool|false
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->upload($path, $resource);
    }

    /**
     * 重命名
     * @param string $path    - 原始路径
     * @param string $newpath - 新路径
     * @return bool
     * @throws \Exception
     */
    public function rename($path, $newpath): bool
    {
        $copy = $this->copy($path, $newpath);
        if ($copy) {
            return $this->delete($path);
        }
        return false;
    }

    /**
     * 复制
     * @param string $path
     * @param string $newpath
     * @return bool
     * @throws \Exception
     */
    public function copy($path, $newpath): bool
    {
        $t_path = $this->applyPathPrefix($path);
        $info   = $this->info($t_path);
        if ($info['type'] === 'file') {
            try {
                $up = $this->upload($newpath, $this->readContent($t_path, 'file'));
            } catch (\Exception $exception) {
                $up = false;
            }
            return $up !== false;
        }
        $list = $this->listContents($path, true);
        foreach ($list as $k => $v) {
            $_path = $v['path'];
            if (strpos($_path, $path) === 0) {
                $_path = substr($_path, strlen($path) + 1);
            }
            if ($this->copy($path . '/' . $_path, $newpath . '/' . $_path) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除目录
     * @param string $dirname - 目录路径
     * @return bool
     * @throws \Exception
     */
    public function deleteDir($dirname): bool
    {
        return $this->delete($dirname);
    }

    /**
     * 创建目录
     * @param string $dirname - 目录路径
     * @param Config $config
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        try {
            $path = $this->applyPathPrefix($dirname);
            if ($this->client->createDir($path)) {
                return ['path' => $dirname, 'type' => 'dir'];
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 删除操作
     * @param string $path - 远程路径
     * @return bool
     */
    public function delete($path): bool
    {
        $t_path = $this->applyPathPrefix($path);
        try {
            $info = $this->info($t_path);
            if ($info['type'] === 'file') {
                return $this->client->delete($t_path, true);
            }
            $list = $this->listContents($path, true);
            foreach ($list as $k => $v) {
                $_path = $v['path'];
                if (strpos($_path, $path) === 0) {
                    $_path = substr($_path, strlen($path) + 1);
                }
                if ($this->delete($path . '/' . $_path) === false) {
                    return false;
                }
            }
            return $this->client->delete($t_path, true);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 判断文件是否存在于又拍云存储
     * @param string $path
     * @return array|bool|null
     */
    public function has($path)
    {
        try {
            $path = $this->applyPathPrefix($path);
            return $this->client->has($path);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 读取文件
     * @param string $path
     * @return array
     */
    public function read($path): array
    {
        $path             = $this->applyPathPrefix($path);
        $info             = $this->info($path);
        $info['contents'] = $this->readContent($path, 'file');
        return $info;
    }

    /**
     * 读取文件
     * @param string $path
     * @return array|false
     * @throws \Exception
     */
    public function readStream($path)
    {
        $path            = $this->applyPathPrefix($path);
        $info            = $this->info($path);
        $info['content'] = $this->readContent($path, 'file');
        $info['stream']  = $info['content'];
        $tempName        = tempnam('', 'tmp');
        if (!$tempName || !file_put_contents($tempName, $info['content'])) {
            return $info;
        }
        $info['stream'] = fopen($tempName, 'rb');
        return $info;
    }

    /**
     * 获取云存储文件/目录的基本信息
     * @param string $path
     * @return array|false
     */
    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);
        return $this->info($path);
    }

    /**
     * 获取文件 size
     * @param string $path
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * 获取文件 mimetype
     * @param string $path
     * @return array|false
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * 获取文件 timestamp
     * @param string $path
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * 建立标准化输出数组
     * @param string $path
     * @param int    $timestamp
     * @param mixed  $content
     * @return array
     */
    protected function normalize($path, $timestamp, $content = null): array
    {
        $data = [
            'path'      => $path,
            'timestamp' => (int)$timestamp,
            'dirname'   => Util::dirname($path),
            'type'      => 'file',
        ];
        if (is_string($content)) {
            $data['contents'] = $content;
        }
        return $data;
    }

    /**
     * 读取云存储文件/目录内容
     * @param string   $path   - 路径
     * @param string   $type   - file|dir 为空时自动判断
     * @param null|mixed 文件内容写入本地文件流。例如 `$saveHandler = fopen('/local/file', 'w')`。
     *                         当设置该参数时，将以文件流的方式，直接将又拍云中的文件写入本地的文件流，或其他可以写入的流
     * @param array    $params 可选参数，读取目录内容时，
     *                         需要设置三个参数：`X-List-Iter` 分页开始位置（第一页不需要设置），`X-List-Limit` 获取的文件数量（默认 100，最大10000），`X-List-Order` 结果以时间正序或者倒序
     * @param int|null $start  分页开始位置（不需要设置）
     * @param array    $list   返回的数据集（不需要设置）
     * @return bool|mixed
     */
    protected function readContent($path, $type = '', $saveHandler = null, $params = [], $start = null, $list = [])
    {
        try {
            $params['X-List-Limit'] = 10000;
            $params['X-List-Iter']  = $start;
            $allowType              = ['file', 'dir'];
            if (empty($type) || !in_array($type, $allowType, true)) {
                $info = $this->info($path);
                $type = $this->getSubValue('type', $info, false);
            }
            if ($type === 'file') {
                return $this->client->read($path, $saveHandler, $params);
            }
            if ($type === 'dir') {
                $data    = $this->client->read($path, $saveHandler, $params);
                $isEnd   = (bool)$this->getSubValue('is_end', $data, true);
                $curList = $this->getSubValue('files', $data, []);
                $list    = array_merge($list, $curList);
                if ($isEnd === true) {
                    return $list;
                }
                $start = $this->getSubValue('iter', $data, null);
                return $this->readContent($path, $type, $saveHandler, $params, $start, $list);
            }
            return false;
        } catch (\Exception$exception) {
            return $list;
        }
    }

    /**
     * 获取云存储文件/目录的基本信息
     * @param string $path 云存储的文件路径
     * @return array 返回标准化的数组
     */
    protected function info(string $path): array
    {
        $info = $this->client->info($path);
        $type = str_replace('*', '', $info['x-upyun-file-type']);
        if ($type !== 'file') {
            return ['type' => 'dir', 'path' => $this->removePathPrefix(rtrim($path, '/'))];
        }
        $noPrePath = $this->removePathPrefix($path);
        return [
            'path'      => $noPrePath,
            'timestamp' => (int)$info['x-upyun-file-date'],
            'dirname'   => Util::dirname($noPrePath),
            'mimetype'  => $this->client->getMimetype($path),
            'size'      => $info['x-upyun-file-size'],
            'type'      => 'file',
        ];
    }

    /**
     * 根据读取的文件、目录内容，返回标准化数组
     * @param array  $item
     * @param string $directory
     * @return array
     */
    protected function getInfoByReadContent(array $item, string $directory): array
    {
        $path      = $directory . $item['name'];
        $noPrePath = $this->removePathPrefix($path);
        if ($item['type'] !== 'N') {
            return [
                'type' => 'dir',
                'path' => $this->removePathPrefix(rtrim($path, '/'))
            ];
        }
        return [
            'path'      => $noPrePath,
            'timestamp' => (int)$item['time'],
            'dirname'   => Util::dirname($noPrePath),
            'mimetype'  => Util::guessMimeType($path, null),
            'size'      => $item['size'],
            'type'      => 'file',
        ];
    }

    /**
     * 文件上传云存储
     * @param string          $path     Path
     * @param string|resource $contents Either a string or a stream.
     * @return array|bool
     */
    protected function upload(string $path, $contents)
    {
        try {
            $path = $this->applyPathPrefix($path);
            $this->client->write($path, $contents, [], false);
            return $this->normalize($path, time(), $contents);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 获取数组、对象下标对应值，不存在时返回指定的默认值
     * @param string|integer $name    - 下标（键名）
     * @param array|object   $data    - 原始数组/对象
     * @param mixed          $default - 指定默认值
     * @return mixed
     */
    protected function getSubValue(string $name, $data, $default = '')
    {
        if (is_object($data)) {
            $value = $data->$name ?? $default;
        } else if (is_array($data)) {
            $value = $data[$name] ?? $default;
        } else {
            $value = $default;
        }
        return $value;
    }
}
