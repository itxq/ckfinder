# CkFinder3.4.5 for PHP 优化版

### 已修改的核心文件

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/CreateFolder.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/RenameFolder.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/RenameFile.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Command/FileUpload.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Filesystem/File/File.php

+ core/cksource/ckfinder/src/CKSource/CKFinder/Filesystem/File/UploadedFile.php

### 实现composer加载

```
composer require itxq/ckfinder
```

### 使用示例

```php

require __DIR__ . '/../vendor/autoload.php';
try {
    \itxq\ckfinder\CkFinder::ins([
        'auto_rename' => ['folder' => false, 'file' => true],
        'licenseName' => '',
        'licenseKey'  => ''
    ])->run(
        ['root' => __DIR__ . '/public', 'baseUrl' => '/public'],
        ['root' => __DIR__ . '/private', 'baseUrl' => '/private']
    );
} catch (\Exception$exception) {
    // var_dump($exception->getMessage());
}
```