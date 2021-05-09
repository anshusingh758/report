<?php
include_once (dirname(__FILE__).'/../constants.php');

class Redis
{
  public static function get($key) {
    $return = array();
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, SAAS_REDIS.'/get?key='.$key);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_HEADER, false);
    $result = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($result,'ARRAY');

    if (isset($result['data'])) {
      $return = $result['data'];
    }

    $_SESSION['redis'] = $return;

    return $return;
  }

  public static function getFromSession($key) {
    $return = array();
    if (isset($_SESSION['redis'][$key])) {
      $return = array($key => $_SESSION['redis'][$key]);
    } else {
      $return = self::get($key);
    }

    return $return;
  }

  public static function removeKeyInSession($key) {
    if (isset($_SESSION['redis'][$key])) {
      unset($_SESSION['redis'][$key]);
    }
  }

  public static function set($key, $value, $ttl = null) {
    $postData = array();
    $postData['key'] = $key;
    $postData['value'] = json_encode($value);

    if ($ttl != NULL) {
      $postData['ttl'] = $ttl;
    }

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, SAAS_REDIS.'/set');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($postData));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    curl_close($curl);
  }

  public static function delete($key) {
    $return = array();
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, SAAS_REDIS.'/delete?key='.$key);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_HEADER, false);
    $result = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($result,'ARRAY');

    if (isset($result['data'])) {
      $return = $result['data'];
    }

    return $return;
  }

}
