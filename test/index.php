<?php

include(__DIR__ . '/../src/Helper.php');

use coordtransform\Helper as CoordinateHelper;

$gcj02 = [113.425221,22.507924];
list($lng, $lat) = $gcj02;

$wgs84 = CoordinateHelper::gcj02towgs84($lng, $lat);
$bd09 = CoordinateHelper::gcj02tobd09($lng, $lat);

var_dump(compact('gcj02', 'wgs84', 'bd09'));
