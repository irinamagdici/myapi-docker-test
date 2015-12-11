<?php

namespace Acme\DataBundle\Model\Utility;

use Acme\DataBundle\Entity\Stores;
use Acme\DataBundle\Entity\Users;
use Acme\DataBundle\Model\Utility\StringUtility;

class EntitiesUtility {

  public static function getUserData(Users $user, $coupons) {

    $userData = array(
      'id' => $user->getId(),
      'username' => $user->getUsername(),
      'firstName' => $user->getFirstName(),
      'lastName' => $user->getLastName(),
      'phone' => $user->getPhone(),
      'token' => $user->getPassword(),
      'role' => $user->getRoles(),
      'cardNumber' => $user->getCardNumber() ? $user->getCardNumber() : '',
      'customCardNumber' => $user->getCustomCardNumber() ? $user->getCustomCardNumber() : '',
      'loyaltyPointsBalance' => $user->getLoyaltyPointsBalance() ? $user->getLoyaltyPointsBalance() : 0,
      'myMeineke' => $user->getMyStore() ? self::getMyMeinekeDetails($user->getMyStore(), $coupons) : ''
    );

    return $userData;
  }

  public static function getMyMeinekeDetails(Stores $store, $coupons) {

    $storeData = array(
      'storeId' => $store->getStoreId(),
      'streetAddress1' => $store->getStreetAddress1(),
      'streetAddress2' => $store->getStreetAddress2(),
      'locationCity' => $store->getLocationCity(),
      'locationCitySlug' => StringUtility::generateSlug($store->getLocationCity()),
      'locationState' => $store->getLocationState(),
      'locationPostalCode' => $store->getLocationPostalCode(),
      'phone' => $store->getPhone(),
      'rawPhone' => $store->getRawPhone(),
      'semCamPhone' => $store->getSemCamPhone(),
      'rawSemCamPhone' => $store->getRawSemCamPhone(),
      'hoursWeekdayOpen' => $store->getHoursWeekdayOpen(),
      'hoursWeekdayClose' => $store->getHoursWeekdayClose(),
      'hoursSaturdayOpen' => $store->getHoursSaturdayOpen(),
      'hoursSaturdayClose' => $store->getHoursSaturdayClose(),
      'hoursSundayOpen' => $store->getHoursSundayOpen(),
      'hoursSundayClose' => $store->getHoursSundayClose(),
      'starRating' => $store->getStarRating(),
      'latitude' => $store->getLat(),
      'longitude' => $store->getLng(),
      'militaryDiscount' => $store->getMilitaryDiscount(),
      'seniorDiscount' => $store->getSeniorDiscount(),
      'locationStatus' => $store->getLocationStatus(),
      'openStatus' => self::getStoreStatus($store),
      'myMeinekeCoupons' => $coupons
    );

    return $storeData;
  }

	public static function getStoreStatus(Stores $store) {

    $timezone = $store->getTimezone() ? $store->getTimezone() : 'PDT'; //store timezone
    $date = new \DateTime();
    $date->setTimezone(new \DateTimeZone($timezone)); //timezone from DB

    //get day of the week and store timetable from DB
    $day = $date->format('w');
    switch($day) {
      case 0:
        $hoursOpen = $store->getHoursSundayOpen() && $store->getHoursSundayOpen() != 'NotOpen' ? $store->getHoursSundayOpen() : '';
        $hoursClose = $store->getHoursSundayClose() && $store->getHoursSundayClose() != 'NotOpen' ? $store->getHoursSundayClose() : '';
      break;
      case 6:
        $hoursOpen = $store->getHoursSaturdayOpen() && $store->getHoursSaturdayOpen() != 'NotOpen' ? $store->getHoursSaturdayOpen() : '';
        $hoursClose = $store->getHoursSaturdayClose() && $store->getHoursSaturdayClose() != 'NotOpen' ? $store->getHoursSaturdayClose() : '';
      break;
      default:
        $hoursOpen = $store->getHoursWeekdayOpen() && $store->getHoursWeekdayOpen() != 'NotOpen' ? $store->getHoursWeekdayOpen() : '';
        $hoursClose = $store->getHoursWeekdayClose() && $store->getHoursWeekdayClose() != 'NotOpen' ? $store->getHoursWeekdayClose() : '';
    }

    //determine status
    if(!$hoursOpen || !$hoursClose)
      $status = 'closed now';
    else {
      $actualTime = strtotime($date->format('Y-m-d H:i:s'));
      $openTime = strtotime($date->format('Y-m-d ') . $hoursOpen);
      $closeTime = strtotime($date->format('Y-m-d ') . $hoursClose);

      $status = $actualTime >= $openTime && $actualTime <= $closeTime ? 'open now' : 'closed now';
    }

    return $status;
  }

  public static function getAllStates() {

    return array(
      'AL' => 'Alabama',
      'AK' => 'Alaska',
      'AZ' => 'Arizona',
      'AR' => 'Arkansas',
      'CA' => 'California',
      'CO' => 'Colorado',
      'CT' => 'Connecticut',
      'DE' => 'Delaware',
      'DC' => 'District of Columbia',
      'FL' => 'Florida',
      'GA' => 'Georgia',
      'HI' => 'Hawaii',
      'ID' => 'Idaho',
      'IL' => 'Illinois',
      'IN' => 'Indiana',
      'IA' => 'Iowa',
      'KS' => 'Kansas',
      'KY' => 'Kentucky',
      'LA' => 'Louisiana',
      'ME' => 'Maine',
      'MD' => 'Maryland',
      'MA' => 'Massachusetts',
      'MI' => 'Michigan',
      'MN' => 'Minnesota',
      'MS' => 'Mississippi',
      'MO' => 'Missouri',
      'MT' => 'Montana',
      'NE' => 'Nebraska',
      'NV' => 'Nevada',
      'NH' => 'New Hampshire',
      'NJ' => 'New Jersey',
      'NM' => 'New Mexico',
      'NY' => 'New York',
      'NC' => 'North Carolina',
      'ND' => 'North Dakota',
      'OH' => 'Ohio',
      'OK' => 'Oklahoma',
      'OR' => 'Oregon',
      'PA' => 'Pennsylvania',
      'RI' => 'Rhode Island',
      'SC' => 'South Carolina',
      'SD' => 'South Dakota',
      'TN' => 'Tennessee',
      'TX' => 'Texas',
      'UT' => 'Utah',
      'VT' => 'Vermont',
      'VA' => 'Virginia',
      'WA' => 'Washington',
      'WV' => 'West Virginia',
      'WI' => 'Wisconsin',
      'WY' => 'Wyoming'
    );
  }

  public static function getCSVServices() {

    return array(
      'absbrakeservice',
      'acsservice',
      'batteries',
      'belts',
      'brakefluidflush',
      'clutches',
      'coolingsystemservice',
      'fuelinjection',
      'oilchangeservice',
      'sehablaespanol',
      'shuttleservice',
      'smogcheck',
      'stateinspection',
      'tirerotation',
      'tires',
      'trailerhitches',
      'transsmissionfluidservice',
      'tuneups',
      'wheelalignment',
      'wheelbalancing',
      'wiperblades',
      'minorenginerepair',
      'motorvehicleinspections',
      'ontariosafetyinspection',
      'enginediagnostics',
      'brakes',
      'exhaust',
      'ridecontrol',
      'frontendrepair',
      'emissionstesting',
      'computerdiagnostic',
      'exhaustsystemsmufflers',
      'schedulemaintenance',
      'electrical',
      'shocksstruts',
      'steeringsuspension',
      'maintenanceinspection',
      'performanceexhaust',
      'cvjoints',
      'drivelineservice',
      'transmissionservices',
      'heatingsystemrepair',
      'tiresales',
      'towingservice',
      'fleetservices'
    );
  }

}
