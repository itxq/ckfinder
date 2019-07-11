CkFinder3.4.5 for PHP 优化版 （添加又拍云存储）
===============

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.1-8892BF.svg)](http://www.php.net/)
[![Latest Stable Version](https://poser.pugx.org/itxq/ckfinder/version)](https://packagist.org/packages/itxq/ckfinder)
[![Total Downloads](https://poser.pugx.org/itxq/ckfinder/downloads)](https://packagist.org/packages/itxq/ckfinder)
[![Latest Unstable Version](https://poser.pugx.org/itxq/ckfinder/v/unstable)](//packagist.org/packages/itxq/ckfinder)
[![License](https://poser.pugx.org/itxq/ckfinder/license)](https://packagist.org/packages/itxq/ckfinder)
[![composer.lock available](https://poser.pugx.org/itxq/ckfinder/composerlock)](https://packagist.org/packages/itxq/ckfinder)
  
### 开源地址：

[【GitHub:】https://github.com/itxq/ckfinder](https://github.com/itxq/ckfinder)

[【码云:】https://gitee.com/itxq/ckfinder](https://github.com/itxq/ckfinder)

### 扩展安装：

+ 方法一：composer命令 `composer require itxq/ckfinder`

+ 方法二：直接下载压缩包，然后进入项目中执行 composer命令 `composer update` 来生成自动加载文件

### 引用扩展：

+ 当你的项目不支持composer自动加载时，可以使用以下方式来引用该扩展包

```
// 引入扩展（具体路径请根据你的目录结构自行修改）
require_once __DIR__ . '/vendor/autoload.php';
```

### 使用示例：

```php
use itxq\ckfinder\CkFinder;

try {
    CkFinder::ins()
        // 配置缓存目录
        ->setConfig('runtime_path', __DIR__ . '/../runtime')
        // 授权信息
        ->setConfig('licenseName', 'licenseName')
        ->setConfig('licenseKey', 'licenseKey')
        // 是否自动重命名（用于过滤用户提交包含中文以及特殊字符，中文会自动转为拼音）
        ->setConfig('auto_rename', ['folder' => true, 'file' => true])
        // 设置PrivateDirKey （可用于区分不同用户的缓存目录，建议使用用户ID）
        ->setPrivateDirKey('')
        // 添加一个又拍云存储空间（添加多个存储空间时，name不可重复）
        ->addBackend('my_upy', CkFinder::ADAPTER_UPY, [
            // 又拍云操作员相关设置
            'service'  => 'service',
            'operator' => 'operator',
            'password' => 'password',
            // 以下根路径和URL前缀需根据自己项目进行调整
            'root'     => 'my_upy/',
            'baseUrl'  => 'http://test.upy.com/my_upy'
        ])
        // 为又拍云存储空间添加一个资源目录（可添加多个）
        ->addResource('云端存储', '01', 'my_upy')
        // 添加一个本地存储空间（添加多个存储空间时，name不可重复）
        ->addBackend('my_local', CkFinder::ADAPTER_LOCAL, [
            // 以下根路径和URL前缀需根据自己项目进行调整
            'root'    => __DIR__ . '/uploads/my_local',
            'baseUrl' => '/uploads/my_local'
        ])
        // 为本地存储空间添加一个资源目录（可添加多个）
        ->addResource('本地存储', '01', 'my_local')
        ->run();
} catch (\Exception$exception) {
    var_dump($exception->getMessage());
}

```

### 修改文件：

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/CreateFolder.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/RenameFolder.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/RenameFile.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/FileUpload.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Filesystem/File/File.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Filesystem/File/UploadedFile.php
