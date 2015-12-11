<?php

namespace Acme\DataBundle\Model\Utility;

class StringUtility {

  //generate a random string
	public static function generateRandomString($length = 8) {

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
  }

  //generate slug
  public static function generateSlug($string, $space = "-") {

    if(function_exists('iconv')) {
      $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }
    $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
    $string = strtolower($string);
    $string = str_replace(" ", $space, $string);

    return $string;
  }

  //change array keys to lowercase
  public static function changeArrayKeyCase($array, $case) {
    $array = array_change_key_case($array, $case);
    foreach($array as $key => $value) {
      if(is_array($value)) {
        $array[$key] = self::changeArrayKeyCase($value, $case);
      }
    }

    return $array;
  }

  //format phone numbers
  public static function formatPhoneNumber($telephone, $onlyNumbers = false) {
    if($onlyNumbers) {
      $pattern = '/[^0-9]/';
      $replace = '';
    }
    else {
      $pattern = '/^([0-9]{3})(\/|\-)([0-9]{3})-?([0-9]{4})?/i';
      $replace = '($1) $3-$4';
    }

    $telephone = preg_replace($pattern, $replace, $telephone);

    return $telephone;
  }

  //get photo index
  public static function getPhotoIndex($name) {
    $explode = explode(".", $name);
    $idx = substr($explode[0], -1);

    return $idx;
  }

  //get file info
  public static function getFileInfo($fileName, $part) {
    $pathParts = pathinfo($fileName);

    return $pathParts[$part];
  }

  //order an array by field
  public static function arrayOrderBy() {
    $args = func_get_args();
    $data = array_shift($args);

    foreach($args as $n => $field) {
      if(is_string($field)) {
        $tmp = array();
        foreach ($data as $key => $row)
          $tmp[$key] = $row[$field];
        $args[$n] = $tmp;
      }
    }

    $args[] = &$data;

    call_user_func_array('array_multisort', $args);

    return array_pop($args);
  }

  //search in array
  public static function searchInArray($array, $fieldToSearch, $valueToSearch, $fieldToReturn) {
    foreach($array as $data) {
      if($data[$fieldToSearch] == $valueToSearch)
        return $data[$fieldToReturn];
    }
  }

  //use bitly API to short an url
  public static function shortenUrl($url) {
    //create the URL
    $bitly = 'http://api.bit.ly/shorten?version=2.0.1&longUrl=' . urlencode($url) . '&login=gtascu&apiKey=R_f600a9794591f99bf08848c3e3d1b5ad&format=json';

    $response = file_get_contents($bitly);
    $json = json_decode($response, true);

    return $json['results'][$url]['shortUrl'];
  }

}
