<?php

namespace Acme\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;

use Acme\DataBundle\Model\ValidationResult;
use Acme\DataBundle\Model\ValidationManager;
use Acme\DataBundle\Model\Constants\ValidationType;
use Acme\DataBundle\Model\Constants\CouponsStatus;
use Acme\DataBundle\Model\Constants\CouponsCategory;
use Acme\DataBundle\Entity\Stores;
use Acme\DataBundle\Model\Utility\StringUtility;


class ApiController extends FOSRestController {

/**********************************************************************************************************************************
Protected Methods
**********************************************************************************************************************************/

  protected function setListingConfigurations(Request $request, &$page, &$noRecords, &$sortField, &$sortType) {
    $page = intval($request->get('page')) ? intval($request->get('page')) - 1 : 0;
    $noRecords = intval($request->get('noRecords')) ? intval($request->get('noRecords')) : PHP_INT_MAX;
    $sortField = $request->get('sortField') ? $request->get('sortField') : 'id';
    $sortType = $request->get('sortType') ? $request->get('sortType') : 'DESC';
  }

  protected function getAuthUser() {
    $user = $this->get('security.context')->getToken()->getUser();

    return $user;
  }

  protected function validate($request, $validation) {
    $validationResult = new ValidationResult();
    try {

      switch($validation) {
        case 'cache':
          ValidationManager::validate('Cache Key', $request->get('key'), ValidationType::REQUIRED);
        break;
        case 'cacheCDN':
          ValidationManager::validate('Folder', $request->get('folder'), ValidationType::REQUIRED);
        break;
        case 'openings':
          ValidationManager::validate('services', $request->get('services'), ValidationType::REQUIRED);
        break;
        case 'appointment':
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::NUMBER);
          ValidationManager::validate('Services', $request->get('services'), ValidationType::REQUIRED);
          ValidationManager::validate('Services Names', $request->get('servicesNames'), ValidationType::REQUIRED);
          ValidationManager::validate('Date and Time', $request->get('dateTime'), ValidationType::REQUIRED);
          ValidationManager::validate('First Name', $request->get('firstName'), ValidationType::REQUIRED);
          ValidationManager::validate('Last Name', $request->get('lastName'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Phone', $request->get('phone'), ValidationType::REQUIRED);
          ValidationManager::validate('Vehicle Make', $request->get('vehicleMake'), ValidationType::REQUIRED);
          ValidationManager::validate('Vehicle Model', $request->get('vehicleModel'), ValidationType::REQUIRED);
          ValidationManager::validate('Vehicle Year', $request->get('vehicleYear'), ValidationType::REQUIRED);
        break;
        case 'login':
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Password', $request->get('password'), ValidationType::REQUIRED);
        break;
        case 'fbLogin':
          ValidationManager::validate('Facebook Id', $request->get('facebookId'), ValidationType::REQUIRED);
        break;
        case 'register':
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Phone number', $request->get('phone'), ValidationType::REQUIRED);
          ValidationManager::validate('Password', $request->get('password'), ValidationType::REQUIRED);
          ValidationManager::validate('Confirm Password', $request->get('confirmPassword'), ValidationType::REQUIRED);
        break;
        case 'activate':
          ValidationManager::validate('Token', $request->get('token'), ValidationType::REQUIRED);
        break;
        case 'forgotPassword':
        case 'resendActivation':
        case 'newsletter':
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
        break;
        case 'resetPassword':
          ValidationManager::validate('Timestamp', $request->get('timestamp'), ValidationType::REQUIRED);
          ValidationManager::validate('Token', $request->get('token'), ValidationType::REQUIRED);
        break;
        case 'changePassword':
          ValidationManager::validate('Current Password', $request->get('currentPassword'), ValidationType::REQUIRED);
          ValidationManager::validate('New Password', $request->get('newPassword'), ValidationType::REQUIRED);
          ValidationManager::validate('Confirm Password', $request->get('confirmPassword'), ValidationType::REQUIRED);
        break;
        case 'checkPassword':
          ValidationManager::validate('Current Password', $request->get('currentPassword'), ValidationType::REQUIRED);
        break;
        case 'profile':
          ValidationManager::validate('Phone number', $request->get('phone'), ValidationType::REQUIRED);
        break;
        case 'myMeineke':
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
        break;
        case 'serialized':
          ValidationManager::validate('Serialized Data', $request->get('serializedData'), ValidationType::REQUIRED);
        break;
        case 'search':
          ValidationManager::validate('Latitude', $request->get('latitude'), ValidationType::REQUIRED);
          ValidationManager::validate('Longitude', $request->get('longitude'), ValidationType::REQUIRED);
        break;
        case 'cities':
          ValidationManager::validate('State', $request->get('state'), ValidationType::REQUIRED);
        break;
        case 'couponStatus':
          ValidationManager::validate('Status', $request->get('status'), ValidationType::REQUIRED, array(CouponsStatus::ACTIVE, CouponsStatus::INACTIVE));
        break;
        case 'couponDelete':
        case 'couponAdd':
          ValidationManager::validate('Coupon Id', $request->get('couponId'), ValidationType::REQUIRED);
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
        break;
        case 'couponsReorder':
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
          ValidationManager::validate('Coupon Ids', $request->get('couponIds'), ValidationType::REQUIRED);
        break;
        case 'storeChoice':
          ValidationManager::validate('Optin', $request->get('optin'), ValidationType::REQUIRED, array(0, 1));
        break;
        case 'coupons':
          ValidationManager::validate('Title', $request->get('title'), ValidationType::REQUIRED);
          //ValidationManager::validate('Services', $request->get('services'), ValidationType::REQUIRED);
          ValidationManager::validate('Medium Size Image', $request->get('imageMedium'), ValidationType::REQUIRED);
          ValidationManager::validate('Large/Print Size Image', $request->get('imageLarge'), ValidationType::REQUIRED);
          ValidationManager::validate('Type', $request->get('category'), ValidationType::REQUIRED, array(CouponsCategory::STORE, CouponsCategory::PROMO, CouponsCategory::METRO));
          ValidationManager::validate('Status', $request->get('status'), ValidationType::REQUIRED, array(CouponsStatus::ACTIVE, CouponsStatus::INACTIVE));
          if($request->get('category') === CouponsCategory::PROMO) {
            //ValidationManager::validate('Stores', $request->get('stores'), ValidationType::REQUIRED);
            ValidationManager::validate('Priority', $request->get('priority'), ValidationType::REQUIRED);
            ValidationManager::validate('End Date', $request->get('endDate'), ValidationType::REQUIRED);
          }
          if($request->get('category') === CouponsCategory::STORE) {
            ValidationManager::validate('Is Default', $request->get('isDefault'), ValidationType::REQUIRED, array(0, 1));
            //ValidationManager::validate('Position', $request->get('position'), ValidationType::REQUIRED);
          }
          if($request->get('category') === CouponsCategory::METRO) {
            ValidationManager::validate('Metro Areas', $request->get('metros'), ValidationType::REQUIRED);
          }
          ValidationManager::validate('End Date', $request->get('endDate'), ValidationType::DATE_);
        break;
        case 'fleetServices':
          ValidationManager::validate('Organization Name', $request->get('organizationName'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Full Name', $request->get('contactFullName'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Phone', $request->get('contactPhone'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Email', $request->get('contactEmail'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Email', $request->get('contactEmail'), ValidationType::EMAIL);
          ValidationManager::validate('Address', $request->get('address'), ValidationType::REQUIRED);
          ValidationManager::validate('City', $request->get('city'), ValidationType::REQUIRED);
          ValidationManager::validate('State', $request->get('state'), ValidationType::REQUIRED);
          ValidationManager::validate('Zip Code', $request->get('zipCode'), ValidationType::REQUIRED);
        break;
        case 'realEstate':
          ValidationManager::validate('Location Address', $request->get('address'), ValidationType::REQUIRED);
          ValidationManager::validate('Location City', $request->get('city'), ValidationType::REQUIRED);
          ValidationManager::validate('Location State', $request->get('state'), ValidationType::REQUIRED);
          ValidationManager::validate('Location Country', $request->get('country'), ValidationType::REQUIRED);
          ValidationManager::validate('Zoned for Auto', $request->get('zonedAuto'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact First Name', $request->get('contactFirstName'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Last Name', $request->get('contactLastName'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Email', $request->get('contactEmail'), ValidationType::REQUIRED);
          ValidationManager::validate('Contact Email', $request->get('contactEmail'), ValidationType::EMAIL);
        break;
        case 'jobsSubmissions':
          //ValidationManager::validate('Job Id', $request->get('jobId'), ValidationType::REQUIRED);
          //ValidationManager::validate('Job Id', $request->get('jobId'), ValidationType::NUMBER);
          ValidationManager::validate('Location', $request->get('location'), ValidationType::REQUIRED);
          ValidationManager::validate('First Name', $request->get('firstName'), ValidationType::REQUIRED);
          ValidationManager::validate('Last Name', $request->get('lastName'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Phone', $request->get('phone'), ValidationType::REQUIRED);
        break;
        case 'carClub':
          ValidationManager::validate('First Name', $request->get('firstName'), ValidationType::REQUIRED);
          ValidationManager::validate('Last Name', $request->get('lastName'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Verify Email', $request->get('verifyEmail'), ValidationType::REQUIRED);
          ValidationManager::validate('Verify Email', $request->get('verifyEmail'), ValidationType::EMAIL);
          ValidationManager::validate('Current Meineke Customer', $request->get('meinekeCustomer'), ValidationType::REQUIRED);
          ValidationManager::validate('Visit Meineke State', $request->get('visitState'), ValidationType::REQUIRED);
          ValidationManager::validate('Vehicles', $request->get('vehicles'), ValidationType::REQUIRED);
        break;
        case 'sendPDF':
          ValidationManager::validate('Document', $request->get('document'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          if(strlen($request->get('mobile'))) {
            ValidationManager::validate('Carrier', $request->get('carrier'), ValidationType::REQUIRED);
          }
        break;
        case 'sendReward':
          ValidationManager::validate('Reward Id', $request->get('id'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
        break;
        case 'pipeline':
          ValidationManager::validate('Email', $request->get('email'), ValidationType::REQUIRED);
          ValidationManager::validate('Email', $request->get('email'), ValidationType::EMAIL);
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
        break;
        case 'scheduledService':
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
          ValidationManager::validate('Service Names', $request->get('serviceNames'), ValidationType::REQUIRED);
        break;
        case 'feedback':
          ValidationManager::validate('Store Id', $request->get('storeId'), ValidationType::REQUIRED);
          ValidationManager::validate('Rating', $request->get('rating'), ValidationType::REQUIRED, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
        break;
      }

    } catch (\Exception $e) {
      $validationResult->setError($e->getMessage());
    }

    return $validationResult;
  }

  protected function getFrontURL() {
    $project = $this->container->getParameter('project');

    return $project['front_url'];
  }

  protected function getMyMeinekeCoupons(Stores $store) {

    $em = $this->getDoctrine()->getManager();

    //get coupons by store id
    $optin = $store->getOptin() ? 1 : 0;
    $coupons = $em->getRepository('AcmeDataBundle:Coupons')->getCoupons($store->getId(), '', $optin);

    if(!$optin) {
      //get all default coupons and add them to the coupons result
      $couponsDefault = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAllCoupons(true, '', true);
      if($couponsDefault) {
        for($i=0;$i<count($couponsDefault);$i++) {
          array_push($coupons, array(
              'title' => $couponsDefault[$i]['title'],
              'image' => $couponsDefault[$i]['image'],
              'barcode' => $couponsDefault[$i]['barcode'],
              'type' => $couponsDefault[$i]['type'],
              'position' => $couponsDefault[$i]['position']
            ));
        }
      }

      //order coupons by position and type
      $coupons = StringUtility::arrayOrderBy($coupons, 'position', SORT_ASC, 'type', SORT_ASC);
    }

    //parse data
    $totalNo = count($coupons) > 4 ? 4 : count($coupons);
    $response = array();
    for($i=0;$i<$totalNo;$i++) {

      //title and barcode
      $response[$i]['title'] = $coupons[$i]['title'];
      $response[$i]['barcode'] = $coupons[$i]['barcode'] ? $coupons[$i]['barcode'] : '';

      //check if barcode exists
      $barcodeImg = $coupons[$i]['barcode'] ? $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/barcode_' . $coupons[$i]['barcode'] . '.png' : '';

      //build images urls
      $extension = StringUtility::getFileInfo($coupons[$i]['image'], 'extension');
      $fileName = StringUtility::getFileInfo($coupons[$i]['image'], 'filename');
      $response[$i]['imageBarcode'] = $barcodeImg;
      $response[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$i]['image'];
      $response[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
      $response[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;
    }

    return $response;

  }

}
