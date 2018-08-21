# coordtransform 的 PHP 版本

> 移植自：https://github.com/wandergis/coordtransform

## 安装

```
composer require billy-poon/coordtransform
```

## 使用

```
<?php

include(__DIR__ . '/vendor/autoload.php');

use coordtransform\Helper as CoordinateHelper;

$gcj02 = [113.425221,22.507924];
list($lng, $lat) = $gcj02;

$wgs84 = CoordinateHelper::gcj02towgs84($lng, $lat);
$bd09 = CoordinateHelper::gcj02tobd09($lng, $lat);

var_dump(compact('gcj02', 'wgs84', 'bd09'));
```

> 更多关于背景和 API 的说明请参考：https://github.com/wandergis/coordtransform
