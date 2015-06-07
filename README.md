Yii2 Export
===========
Excel, Word, PDF

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist hscstudio/yii2-export "*"
```

or add

```
"hscstudio/yii2-export": "*"
```

to the require section of your `composer.json` file.

Add in vendor\composer\autoload_namespaces.php
	'PHPExcel' => array($vendorDir . '/phpoffice/phpexcel/Classes'),

Add in vendor\composer\autoload_files.php
    $vendorDir . '/hscstudio/yii2-export/libraries/tbs/tbs_class.php',
    $vendorDir . '/hscstudio/yii2-export/libraries/tbs/plugins/tbs_plugin_opentbs.php',
    $vendorDir . '/hscstudio/yii2-export/libraries/mpdf60/mpdf.php',
	
Add in vendor\composer\autoload_psr4.php
	'hscstudio\\export\\' => array($vendorDir . '/hscstudio/yii2-export'),

Add in vendor\yiisoft\extensions.php	
  array (
    'name' => 'hscstudio/yii2-export',
    'version' => '1.0',
    'alias' => 
    array (
      '@hscstudio/export' => $vendorDir . '/hscstudio/yii2-export',
    ),
  ),
Usage
-----

Once the extension is installed, simply use it in your code by  :

