<?php

namespace coordtransform;

/**
 * ported from https://github.com/wandergis/coordtransform
 */
class Helper
{
  //定义一些常量
  const x_PI = 3.14159265358979324 * 3000.0 / 180.0;
  const PI = 3.1415926535897932384626;
  const a = 6378245.0;
  const ee = 0.00669342162296594323;

  /**
   * 百度坐标系 (BD-09) 与 火星坐标系 (GCJ-02)的转换
   * 即 百度 转 谷歌、高德
   * @param bd_lon
   * @param bd_lat
   * @returns {*[]}
   */
  public static function bd09togcj02($bd_lon, $bd_lat) {
    $x = $bd_lon - 0.0065;
    $y = $bd_lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::x_PI);
    $theta = atan2($y, $x) - 0.000003 * cos($x * self::x_PI);
    $gg_lng = $z * cos($theta);
    $gg_lat = $z * sin($theta);
    return [$gg_lng, $gg_lat];
  }

  /**
   * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换
   * 即谷歌、高德 转 百度
   * @param lng
   * @param lat
   * @returns {*[]}
   */
  public static function gcj02tobd09($lng, $lat) {
    $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * sin($lat * self::x_PI);
    $theta = atan2($lat, $lng) + 0.000003 * cos($lng * self::x_PI);
    $bd_lng = $z * cos($theta) + 0.0065;
    $bd_lat = $z * sin($theta) + 0.006;
    return [$bd_lng, $bd_lat];
  }

  /**
   * WGS84转GCj02
   * @param lng
   * @param lat
   * @returns {*[]}
   */
  public static function wgs84togcj02($lng, $lat) {
    if (self::out_of_china($lng, $lat)) {
      return [$lng, $lat];
    } else {
      $dlat = self::transformlat($lng - 105.0, $lat - 35.0);
      $dlng = self::transformlng($lng - 105.0, $lat - 35.0);
      $radlat = $lat / 180.0 * self::PI;
      $magic = sin($radlat);
      $magic = 1 - self::ee * $magic * $magic;
      $sqrtmagic = sqrt($magic);
      $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
      $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
      $mglat = $lat + $dlat;
      $mglng = $lng + $dlng;
      return [$mglng, $mglat];
    }
  }

  /**
   * GCJ02 转换为 WGS84
   * @param lng
   * @param lat
   * @returns {*[]}
   */
  public static function gcj02towgs84($lng, $lat) {
    if (self::out_of_china($lng, $lat)) {
      return [$lng, $lat];
    } else {
      $dlat = self::transformlat($lng - 105.0, $lat - 35.0);
      $dlng = self::transformlng($lng - 105.0, $lat - 35.0);
      $radlat = $lat / 180.0 * self::PI;
      $magic = sin($radlat);
      $magic = 1 - self::ee * $magic * $magic;
      $sqrtmagic = sqrt($magic);
      $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
      $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
      $mglat = $lat + $dlat;
      $mglng = $lng + $dlng;
      return [$lng * 2 - $mglng, $lat * 2 - $mglat];
    }
  }

  static function transformlat($lng, $lat) {
    $ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
    $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
    $ret += (20.0 * sin($lat * self::PI) + 40.0 * sin($lat / 3.0 * self::PI)) * 2.0 / 3.0;
    $ret += (160.0 * sin($lat / 12.0 * self::PI) + 320 * sin($lat * self::PI / 30.0)) * 2.0 / 3.0;
    return $ret;
  }

  static function transformlng($lng, $lat) {
    $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
    $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
    $ret += (20.0 * sin($lng * self::PI) + 40.0 * sin($lng / 3.0 * self::PI)) * 2.0 / 3.0;
    $ret += (150.0 * sin($lng / 12.0 * self::PI) + 300.0 * sin($lng / 30.0 * self::PI)) * 2.0 / 3.0;
    return $ret;
  }

  /**
   * 判断是否在国内，不在国内则不做偏移
   * @param lng
   * @param lat
   * @returns {boolean}
   */
  static function out_of_china($lng, $lat) {
    // 纬度3.86~53.55,经度73.66~135.05
    return !($lng > 73.66 && $lng < 135.05 && $lat > 3.86 && $lat < 53.55);
  }

  // http://www.cnblogs.com/ycsfwhh/archive/2010/12/20/1911232.html

  const EARTH_RADIUS = 6378137; // 地球半径（米）

  static function rad($d)
  {
    return $d * self::PI / 180.0;
  }

  public static function calculatedistance($lng1, $lat1, $lng2, $lat2)
  {
    $rlat1 = self::rad($lat1);
    $rlat2 = self::rad($lat2);
    $dlat = $rlat1 - $rlat2;
    $dlng = self::rad($lng1) - self::rad($lng2);

    $s = 2 * asin(sqrt(
      pow(sin($dlat / 2), 2) +
      cos($rlat1) * cos($rlat2) * pow(sin($dlng / 2), 2)
    ));
    $s *= self::EARTH_RADIUS;
    // $s = Math.Round($s * 10000) / 10000;
    return $s;
  }
}
