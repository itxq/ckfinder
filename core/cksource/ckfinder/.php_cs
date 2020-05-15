<?php

$header = <<<'EOF'
CKFinder
========
https://ckeditor.com/ckfinder/
Copyright (c) 2007-2020, CKSource - Frederico Knabben. All rights reserved.

The software, this file and its contents are subject to the CKFinder
License. Please read the license.txt file before using, installing, copying,
modifying or distribute this file or part of its contents. The contents of
this file is part of the Source Code of CKFinder.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)

    // The class in this file does not match the file name, don't fix this
    ->notPath('tests/CKSource/CKFinder/Tests/Plugin/DummyPlugin.php')

    // Don't fix the JS output in this file
    ->notPath('src/CKSource/CKFinder/Command/QuickUpload.php')
    ->notPath('ckfinder.php')
    ->notPath('config.php')
    ->notPath('config.template.php')
;

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP56Migration' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'list_syntax' => ['syntax' => 'long'],
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;

return $config;
