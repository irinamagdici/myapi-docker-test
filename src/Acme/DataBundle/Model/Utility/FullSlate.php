<?php

namespace Acme\DataBundle\Model\Utility;

use Symfony\Component\HttpFoundation\Request;

class FullSlate {

  public static function checkFullSlate($id, $fullslateUrl) {
    strpos($fullslateUrl, '{id}') ? $url = str_replace('{id}', $id, $fullslateUrl): $url = $fullslateUrl;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); //live page
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

  public static function getFullSlateServices($id , $fullslateUrl) {
    strpos($fullslateUrl, '{id}') ? $url = str_replace('{id}', $id, $fullslateUrl): $url = $fullslateUrl;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url.'/services'); //staging API
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

  public static function getFullSlateOpenings($id, $services, $sampling = false, $fullslateUrl) {
    strpos($fullslateUrl, '{id}') ? $url = str_replace('{id}', $id, $fullslateUrl): $url = $fullslateUrl;

    $before = date("Ymd", strtotime('+30 days'));

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
      curl_setopt($curl, CURLOPT_URL, $url.'/openings?' . $data);
    }
    else {
      curl_setopt($curl, CURLOPT_URL, $url.'/openings?' . $data . '&before=' . $before);
    }
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
  }

  public static function saveFullSlateAppointment(Request $request, $timezone = 'PDT', $fullslateUrl, $fullslateSecurityKey) {

    strpos($fullslateUrl, '{id}') ? $url = str_replace('{id}', $request->get('storeId'), $fullslateUrl): $url = $fullslateUrl;
    //$time = strtotime($request->get('dateTime')) + 3600; // Add 1 hour
    //$time = date('Y-m-d H:i:s', $time); // Back to string

    $date = new \DateTime(trim($request->get('dateTime')), new \DateTimeZone($timezone));
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
    curl_setopt($curl, CURLOPT_URL, $url.'/bookings?app='.$fullslateSecurityKey); //staging API
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
