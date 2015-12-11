<?php

namespace Acme\DataBundle\Model\Utility;

use Symfony\Component\HttpFoundation\Request;

class FullSlate {

	public static function getFullSlateServices($id) {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://meineke'. $id . '.fullslate.com/api/services'); //live API
    //curl_setopt($curl, CURLOPT_URL, 'http://xivictestaccount.stage.fullslate.com/api/services'); //staging API
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

  public static function getFullSlateOpenings($id, $services, $sampling = false) {

    $before = date("Ymd", strtotime('+1 month'));

    //build services query for Full Slate
    $allServices = explode(",", $services);
    if(count($allServices) === 1) {
      $data = "service=" . urlencode($allServices[0]);
    }
    else {
      $data = '';
      foreach($allServices as $value) {
        $data .= "services[]=" . urlencode($value) . "&";
      }
      $data = rtrim($data, "& ");
    }

    $curl = curl_init();
    if($sampling) {
      curl_setopt($curl, CURLOPT_URL, 'http://meineke'. $id . '.fullslate.com/api/openings?' . $data); //live API
      //curl_setopt($curl, CURLOPT_URL, 'http://xivictestaccount.stage.fullslate.com/api/openings?' . $data); //staging API
    }
    else {
      curl_setopt($curl, CURLOPT_URL, 'http://meineke'. $id . '.fullslate.com/api/openings?' . $data . '&before=' . $before); //live API
      //curl_setopt($curl, CURLOPT_URL, 'http://xivictestaccount.stage.fullslate.com/api/openings?' . $data . '&before=' . $before); //staging API
    }
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

  public static function saveFullSlateAppointment(Request $request, $timezone = 'PDT') {

    $time = date("Y-m-d H:i", strtotime(trim($request->get('dateTime'))) - 3600); //only for LIVE!!!!

    $date = new \DateTime($time, new \DateTimeZone($timezone));
    $date->setTimezone(new \DateTimeZone('UTC'));

    $postValues = array(
      "at"                   => $date->format('Ymd') . 'T' . $date->format('His') . 'Z',
      "first_name"           => trim($request->get('firstName')),
      "last_name"            => trim($request->get('lastName')),
      "email"                => trim($request->get('email')),
      "phone_number"         => trim($request->get('phone')),
      "custom-Vehicle Make"  => trim($request->get('vehicleMake')),
      "custom-Vehicle Model" => trim($request->get('vehicleModel')),
      "custom-Vehicle Year"  => trim($request->get('vehicleYear')),
      "notes"                => trim($request->get('comments'))
    );

    //build post string
    $postString = "";
    foreach($postValues as $key => $value) {
      $postString .= "$key=" . urlencode($value) . "&";
    }

    //build services query for Full Slate
    $services = explode(",", trim($request->get('services')));
    if(count($services) === 1) {
      $postString .= "service=" . urlencode($services[0]);
    }
    else {
      foreach($services as $value) {
        $postString .= "services[]=" . urlencode($value) . "&";
      }
      $postString = rtrim($postString, "& ");
    }

    if(trim($request->get('vehicleDropoff')))
      $postString .= "&custom-Will you be dropping your vehicle off for service?=on";
    if(trim($request->get('waitForCar')))
      $postString .= "&custom-Will you be waiting while your car is serviced?=on";
    if(trim($request->get('textReminderSMS')))
      $postString .= "&sms_reminder_optin=on";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://meineke'. trim($request->get('storeId')) . '.fullslate.com/api/bookings?app=5GYxapaNRDjKgvNjWwvUyfIZlicpVmSB9Gz0TVMAJvSB2jNp1R&'); //live API
    //curl_setopt($curl, CURLOPT_URL, 'https://xivictestaccount.stage.fullslate.com/api/bookings?app=nTUbUllXSQhamYJO52fe9FQssrYU1KhZlMz9K8VPR6ssZow945&'); //staging API
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

}
