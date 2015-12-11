<?php

namespace Acme\DataBundle\Model\Utility;

use Symfony\Component\HttpFoundation\Request;

class SearchUtility {

	public static function getFromBing($searchKey, $zipCode = 0) {

    $result = array();

    $url = 'http://dev.virtualearth.net/REST/v1/Locations/%s?key=AsFjVllhObLq9WqYO7QEWmSn6Pidkx9uZ_krUhyEkL1flBdC0c_7L5i-asibmjpu';
    if($zipCode)
      $url = "http://dev.virtualearth.net/REST/v1/Locations?CountryRegion=US&postalCode=%s&key=AsFjVllhObLq9WqYO7QEWmSn6Pidkx9uZ_krUhyEkL1flBdC0c_7L5i-asibmjpu";

    $geocode = json_decode(file_get_contents(sprintf($url, str_ireplace(' ', '%20', $searchKey))));

    if(isset($geocode->resourceSets[0]->resources['0'])) {
      $result['latitude'] = $geocode->resourceSets[0]->resources['0']->point->coordinates[0];
      $result['longitude'] = $geocode->resourceSets[0]->resources['0']->point->coordinates[1];
    }

    return $result;
  }

  public static function getFromGoogle($searchKey, $coords = false) {

    $result = array();

    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false&key=AIzaSyA8BRQEzvYM8S8BfeWETL1dxNBaUC9C8YA";

    $geocode = json_decode(file_get_contents(sprintf($url, str_ireplace(' ', '%20', $searchKey))));

    if($geocode->status == "OK") {

      if($coords) {
        for($i=0;$i<count($geocode->results[0]->address_components);$i++) {
          if($geocode->results[0]->address_components[$i]->types[0] == 'postal_code')
            $result['zipCode'] = $geocode->results[0]->address_components[$i]->long_name;
          if($geocode->results[0]->address_components[$i]->types[0] == 'locality')
            $result['city'] = $geocode->results[0]->address_components[$i]->long_name;
        }
      }
      else {
        $result['latitude'] = $geocode->results[0]->geometry->location->lat;
        $result['longitude'] = $geocode->results[0]->geometry->location->lng;
      }
    }

    return $result;
  }

}
