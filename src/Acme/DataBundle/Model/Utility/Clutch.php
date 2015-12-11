<?php

namespace Acme\DataBundle\Model\Utility;

use Symfony\Component\HttpFoundation\Request;

use Acme\DataBundle\Model\Utility\StringUtility;
use Acme\DataBundle\Entity\Users;

class Clutch {

	public static function getCustomerData($api, $data, $vehicle = 0) {

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'search');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    //get results from Clutch
    $customerData = array();
    if($result) {
      $response = json_decode($result, true);

      if($response['success']) {
        if(!empty($response['cards'])) {
          if($vehicle) {
            $customerData['brandDemographics'] = isset($response['cards'][0]['brandDemographics']) ? json_decode($response['cards'][0]['brandDemographics'], true): array();
            $customerData['mailings'] = isset($response['cards'][0]['mailings']) ? $response['cards'][0]['mailings'] : array();
          }
          else {
            //first search - after cust_ cards with keytag set
            for($i=0;$i<count($response['cards']);$i++) {
              if(strpos($response['cards'][$i]['cardNumber'], 'cust_') !== FALSE && isset($response['cards'][$i]['customCardNumber']) && $response['cards'][$i]['customCardNumber']) {
                $customerData['cardNumber'] = $response['cards'][$i]['cardNumber'];
                $customerData['customCardNumber'] = isset($response['cards'][$i]['customCardNumber']) ? $response['cards'][$i]['customCardNumber'] : '';
                $customerData['balance'] = !empty($response['cards'][$i]['balances']) ? (isset($response['cards'][$i]['balances'][0]['amount']) ? $response['cards'][$i]['balances'][0]['amount'] : 0) : 0;
                $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                break;
              }
            }

            //second search - after mkey_ cards with keytag set
            if(empty($customerData)) {
              for($i=0;$i<count($response['cards']);$i++) {
                if(strpos($response['cards'][$i]['cardNumber'], 'mkey_') !== FALSE && isset($response['cards'][$i]['customCardNumber']) && $response['cards'][$i]['customCardNumber']) {
                  $customerData['cardNumber'] = $response['cards'][$i]['cardNumber'];
                  $customerData['customCardNumber'] = isset($response['cards'][$i]['customCardNumber']) ? $response['cards'][$i]['customCardNumber'] : '';
                  $customerData['balance'] = !empty($response['cards'][$i]['balances']) ? (isset($response['cards'][$i]['balances'][0]['amount']) ? $response['cards'][$i]['balances'][0]['amount'] : 0) : 0;
                  $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                  break;
                }
              }
            }

            //third search - only after cust_ cards
            if(empty($customerData)) {
              for($i=0;$i<count($response['cards']);$i++) {
                if(strpos($response['cards'][$i]['cardNumber'], 'cust_') !== FALSE) {
                  $customerData['cardNumber'] = $response['cards'][$i]['cardNumber'];
                  $customerData['customCardNumber'] = isset($response['cards'][$i]['customCardNumber']) ? $response['cards'][$i]['customCardNumber'] : '';
                  $customerData['balance'] = !empty($response['cards'][$i]['balances']) ? (isset($response['cards'][$i]['balances'][0]['amount']) ? $response['cards'][$i]['balances'][0]['amount'] : 0) : 0;
                  $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                  break;
                }
              }
            }

            //fourth search - only after mkey_ cards
            if(empty($customerData)) {
              for($i=0;$i<count($response['cards']);$i++) {
                if(strpos($response['cards'][$i]['cardNumber'], 'mkey_') !== FALSE) {
                  $customerData['cardNumber'] = $response['cards'][$i]['cardNumber'];
                  $customerData['customCardNumber'] = isset($response['cards'][$i]['customCardNumber']) ? $response['cards'][$i]['customCardNumber'] : '';
                  $customerData['balance'] = !empty($response['cards'][$i]['balances']) ? (isset($response['cards'][$i]['balances'][0]['amount']) ? $response['cards'][$i]['balances'][0]['amount'] : 0) : 0;
                  $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                  break;
                }
              }
            }

          }
        }
      }
    }

    return $customerData;
  }

  public static function getCustomerDataForRegister($api, $data) {

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'search');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    //get results from Clutch
    $customerData = array();
    if($result) {
      $response = json_decode($result, true);

      if($response['success']) {

        if(!empty($response['cards'])) {
          for($i=0;$i<count($response['cards']);$i++) {
            if(strpos($response['cards'][$i]['cardNumber'], 'cust_') !== FALSE && isset($response['cards'][$i]['customCardNumber']) && $response['cards'][$i]['customCardNumber']) {
              if(!empty($response['cards'][$i]['customer'])) {
                $customerData['firstName'] = isset($response['cards'][$i]['customer']['firstName']) ? $response['cards'][$i]['customer']['firstName'] : '';
                $customerData['lastName'] = isset($response['cards'][$i]['customer']['lastName']) ? $response['cards'][$i]['customer']['lastName'] : '';
                $customerData['email'] = isset($response['cards'][$i]['customer']['email']) ? $response['cards'][$i]['customer']['email'] : '';
                $customerData['phone'] = isset($response['cards'][$i]['customer']['phone']) ? str_replace("+1", "", $response['cards'][$i]['customer']['phone']) : '';
                $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();
              }

              break;
            }
          }

          //second search - after mkey_ cards with keytag set
          if(empty($customerData)) {
            for($i=0;$i<count($response['cards']);$i++) {
              if(strpos($response['cards'][$i]['cardNumber'], 'mkey_') !== FALSE && isset($response['cards'][$i]['customCardNumber']) && $response['cards'][$i]['customCardNumber']) {
                $customerData['firstName'] = isset($response['cards'][$i]['customer']['firstName']) ? $response['cards'][$i]['customer']['firstName'] : '';
                $customerData['lastName'] = isset($response['cards'][$i]['customer']['lastName']) ? $response['cards'][$i]['customer']['lastName'] : '';
                $customerData['email'] = isset($response['cards'][$i]['customer']['email']) ? $response['cards'][$i]['customer']['email'] : '';
                $customerData['phone'] = isset($response['cards'][$i]['customer']['phone']) ? str_replace("+1", "", $response['cards'][$i]['customer']['phone']) : '';
                $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                break;
              }
            }
          }

          //third search - only after cust_ cards
          if(empty($customerData)) {
            for($i=0;$i<count($response['cards']);$i++) {
              if(strpos($response['cards'][$i]['cardNumber'], 'cust_') !== FALSE) {
                $customerData['firstName'] = isset($response['cards'][$i]['customer']['firstName']) ? $response['cards'][$i]['customer']['firstName'] : '';
                $customerData['lastName'] = isset($response['cards'][$i]['customer']['lastName']) ? $response['cards'][$i]['customer']['lastName'] : '';
                $customerData['email'] = isset($response['cards'][$i]['customer']['email']) ? $response['cards'][$i]['customer']['email'] : '';
                $customerData['phone'] = isset($response['cards'][$i]['customer']['phone']) ? str_replace("+1", "", $response['cards'][$i]['customer']['phone']) : '';
                $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                break;
              }
            }
          }

          //fourth search - only after mkey_ cards
          if(empty($customerData)) {
            for($i=0;$i<count($response['cards']);$i++) {
              if(strpos($response['cards'][$i]['cardNumber'], 'mkey_') !== FALSE) {
                $customerData['firstName'] = isset($response['cards'][$i]['customer']['firstName']) ? $response['cards'][$i]['customer']['firstName'] : '';
                $customerData['lastName'] = isset($response['cards'][$i]['customer']['lastName']) ? $response['cards'][$i]['customer']['lastName'] : '';
                $customerData['email'] = isset($response['cards'][$i]['customer']['email']) ? $response['cards'][$i]['customer']['email'] : '';
                $customerData['phone'] = isset($response['cards'][$i]['customer']['phone']) ? str_replace("+1", "", $response['cards'][$i]['customer']['phone']) : '';
                $customerData['brandDemographics'] = isset($response['cards'][$i]['brandDemographics']) ? json_decode($response['cards'][$i]['brandDemographics'], true): array();

                break;
              }
            }
          }
          
        }

      }
    }

    return $customerData;
  }

  public static function getCustomerCardHistory($api, $data) {

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'cardHistory');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    //get results from Clutch
    $customerHistory = array();
    if($result) {
      $response = json_decode($result, true);

      if($response['success']) {
        $customerHistory = $response['transactions'];
      }
    }

    return $customerHistory;
  }

  public static function getCustomerCardTransaction($api, $data) {

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'checkoutLookup');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    //get results from Clutch
    $customerTransaction = array();
    if($result) {
      $response = json_decode($result, true);

      if($response['success']) {
        if(!empty($response['skus'])) {
          for($i=0;$i<count($response['skus']);$i++) {
            $customerTransaction[$i]['amount'] = isset($response['balanceMutations'][$i]['amount']) ? $response['balanceMutations'][$i]['amount'] : 0;
            $customerTransaction[$i]['sku'] = $response['skus'][$i]['sku'];
            $customerTransaction[$i]['locationId'] = isset($response['locationId']) ? $response['locationId'] : '';
          }
        }
      }
    }

    return $customerTransaction;
  }

  public static function getCustomerLastLocation($api, $data) {

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'checkoutLookup');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    //get results from Clutch
    $lastStore = '';
    if($result) {
      $response = json_decode($result, true);

      if($response['success']) {
        $lastStore = isset($response['locationId']) ? $response['locationId'] : '';
      }
    }

    return $lastStore;
  }

  public static function getCustomerInfo($api, $email, $phoneNumber) {

    //format phone number
    $phone = '+1' . StringUtility::formatPhoneNumber($phoneNumber, true);

    $returnFields = array(
      'balances' => true,
      'customer' => true,
      'alternateCustomer' => true,
      'giverCustomer' => true,
      'isEnrolled' => true,
      'customData' => true,
      'customCardNumber' => true,
      'brandDemographics' => true
    );

    //first data array
    $data = array(
      'filters' => array(
        'email' => $email,
        'phone' => $phone
      ),
      'returnFields' => $returnFields
    );
    $customerData = self::getCustomerData($api, $data);
    if(!empty($customerData))
      return $customerData;

    //second data array
    $data = array(
      'filters' => array(
        'email' => $email
      ),
      'returnFields' => $returnFields
    );
    $customerData = self::getCustomerData($api, $data);
    if(!empty($customerData))
      return $customerData;

    //third data array
    $data = array(
      'filters' => array(
        'phone' => $phone
      ),
      'returnFields' => $returnFields
    );
    $customerData = self::getCustomerData($api, $data);

    return $customerData;
  }

  public static function getCustomerInfoForRegister($api, $customCardNumber = "", $phoneNumber = "") {

    $returnFields = array(
      'balances' => true,
      'customer' => true,
      'alternateCustomer' => true,
      'giverCustomer' => true,
      'isEnrolled' => true,
      'customData' => true,
      'customCardNumber' => true,
      'brandDemographics' => true
    );

    //search for custom card number
    if($customCardNumber) {
      $data = array(
        'filters' => array(
          'customCardNumber' => $customCardNumber
        ),
        'returnFields' => $returnFields
      );
    }

    //search for phone
    if($phoneNumber) {

      //format phone number
      $phone = '+1' . StringUtility::formatPhoneNumber($phoneNumber, true);

      $data = array(
        'filters' => array(
          'phone' => $phone
        ),
        'returnFields' => $returnFields
      );
    }

    $customerData = self::getCustomerDataForRegister($api, $data);

    return $customerData;

  }

  public static function getHistoryTransaction($api, $cardNumber, $period = '') {

    //data array
    if($period) {
      $data = array(
        'cardNumber' => $cardNumber,
        'beginDate' => date("Y-m-d H:i:s", strtotime('-' . $period . 'days'))
      );
    }
    else {
      $data = array(
        'cardNumber' => $cardNumber
      );
    }

    $customerData = self::getCustomerCardHistory($api, $data);

    return $customerData;

  }

  public static function getTransactionDetails($api, $transactionId) {

    //data array
    $data = array(
      'checkoutTransactionId' => $transactionId
    );
    $customerData = self::getCustomerCardTransaction($api, $data);

    return $customerData;

  }

  public static function getTransactionDetailsForLastLocation($api, $transactionId) {

    //data array
    $data = array(
      'checkoutTransactionId' => $transactionId
    );
    $customerData = self::getCustomerLastLocation($api, $data);

    return $customerData;

  }

  public static function setCustomerInfo($api, Users $user) {

    $data = array(
      'cardNumber' => $user->getCardNumber(),
      'primaryCustomer' => array(
        'firstName' => $user->getFirstName(),
        'lastName' => $user->getLastName(),
        'phone' => '+1' . StringUtility::formatPhoneNumber($user->getPhone(), true)
      )
    );

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'updateAccount');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    if($result) {
      $response = json_decode($result, true);
      if($response['success']) {
        return true;
      }
    }

    return false;

  }

  public static function allocateCard($api) {

    $data = array(
      'cardSetId' => 'MKEY2015'
    );

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'allocate');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    if($result) {
      $response = json_decode($result, true);
      if($response['success']) {
        return $response['cardNumber'];
      }
    }

    return false;

  }

  public static function allocateCustomerInfo($api, $userData) {

    $data = array(
      'cardNumber' => $userData['cardNumber'],
      'countAsEnrollment'=> $userData['countAsEnrollment'],
      'primaryCustomer' => array(
        'firstName' => $userData['firstName'],
        'lastName' => $userData['lastName'],
        'phone' => '+1' . StringUtility::formatPhoneNumber($userData['phone'], true),
        'email' => $userData['email']
      )
    );

    //convert data to json
    $jsonData = json_encode($data);

    //start communication with Clutch API
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api['api_url'] . $api['api_service'] . 'updateAccount');
    curl_setopt($curl, CURLOPT_PORT, $api['api_port']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData),
      'Authorization: Basic ' . base64_encode($api['api_key'] . ':' . $api['api_secret']),
      'Brand: ' . $api['brand'],
      'Location: ' . $api['location'],
      'Terminal: ' . $api['terminal']
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    $result = curl_exec($curl);

    curl_close($curl);

    if($result) {
      $response = json_decode($result, true);
      if($response['success']) {
        return true;
      }
    }

    return false;

  }

  public static function getVehicleInfo($api, $cardNumber) {

    $returnFields = array(
      'balances' => true,
      'customer' => true,
      'alternateCustomer' => true,
      'giverCustomer' => true,
      'isEnrolled' => true,
      'customData' => true,
      'customCardNumber' => true,
      'brandDemographics' => true,
      'mailings' => true
    );

    $data = array(
      'filters' => array(
        'cardNumber' => $cardNumber
      ),
      'returnFields' => $returnFields
    );
    $customerData = self::getCustomerData($api, $data, 1);

    return $customerData;

  }

}
