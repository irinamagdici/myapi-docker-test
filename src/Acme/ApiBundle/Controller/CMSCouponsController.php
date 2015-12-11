<?php

namespace Acme\ApiBundle\Controller;

/**********************************************************************************************************************************
Request Types
**********************************************************************************************************************************/
use Symfony\Component\HttpFoundation\Request;

/**********************************************************************************************************************************
FOS REST
**********************************************************************************************************************************/
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;

/**********************************************************************************************************************************
API DOCS
**********************************************************************************************************************************/
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Acme\DataBundle\Entity\Stores;
use Acme\DataBundle\Entity\Services;
use Acme\DataBundle\Entity\StoresHasServices;
use Acme\DataBundle\Entity\Coupons;
use Acme\DataBundle\Entity\CouponsHasServices;
use Acme\DataBundle\Entity\StoresHasCoupons;
use Acme\DataBundle\Entity\DmaHasCoupons;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Constants\StoresStatus;
use Acme\DataBundle\Model\Constants\CouponsCategory;
use Acme\DataBundle\Model\Constants\CouponsStatus;
use Acme\DataBundle\Model\Utility\StringUtility;
use Acme\DataBundle\Model\Utility\EntitiesUtility;
use Acme\DataBundle\Model\Utility\FullSlate;
use Acme\DataBundle\Model\Utility\Barcode39;
use Acme\StorageBundle\Model\S3;


class CMSCouponsController extends ApiController implements ClassResourceInterface {

  /**
   * Get CMS services used to get a list of all services for coupons drop down.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS coupons services",
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/services")
   *
   */
  public function getServicesCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {

      //get services from DB
      $entities = $em->getRepository('AcmeDataBundle:Services')->getCMSServices();

      //add harcoded General category
      array_push($entities, array('id' => 215, 'title' => 'General'));

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get coupons export service used to get a CSV file with coupons.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS coupons CSV file",
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/coupons/export")
   *
   */
  public function getCouponsExportCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //check permissions
      $user = $this->getAuthUser();
      if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
        return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

      //set pagination and sorting
      $this->setListingConfigurations($request, $page, $noRecords, $sortField, $sortType);

      //get coupons
      $entities = $em->getRepository('AcmeDataBundle:Coupons')->getCMSExportCoupons($sortField, $sortType);

      //coupons not found
      if(!$entities)
        return ApiResponse::setResponse($entities);

      //parse data
      for($i=0;$i<count($entities);$i++) {

        //update status
        $entities[$i]['status'] = $entities[$i]['status'] ? 'ACTIVE' : 'INACTIVE';

        //update endDate
        $entities[$i]['endDate'] = $entities[$i]['endDate'] ? $entities[$i]['endDate']->format('m/d/Y')  : 'NA';

        //update isDefault and position
        $entities[$i]['isDefault'] = $entities[$i]['isDefault'] ? 'Yes' : 'No';
        $entities[$i]['position'] = $entities[$i]['position'] ? $entities[$i]['position'] : '-';

        //get services
        $servicesArr = array();
        $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($entities[$i]['id']);
        if($services) {
          for($j=0;$j<count($services);$j++) {
            $servicesArr[] = $services[$j]->getServices()->getTitle();
          }
        }
        $entities[$i]['services'] = implode(", ", $servicesArr);

        unset($entities[$i]['id']);
      }

      //generate csv
      $csv = $this->get('storageBundle.export')->uploadCSVToAS3($entities);

      //return response
      return ApiResponse::setResponse($this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/documents/' . $csv);

    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get coupons service used to get all coupons for CMS Dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS coupons",
   *     filters={
   *         {"name"="page", "dataType"="integer", "default"="1"},
   *         {"name"="noRecords", "dataType"="integer", "default"="10"},
   *         {"name"="sortField", "dataType"="string", "pattern"="id|title|type|status|endDate", "default"="id"},
   *         {"name"="sortType", "dataType"="string", "pattern"="ASC|DESC", "default"="DESC"},
   *         {"name"="keyword", "dataType"="string", "pattern"="coupon title", "default"=""}
   *     },
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=false, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/coupons")
   *
   */
  public function getCouponsCMSAction(Request $request) {
    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {

      if(trim($request->get('storeId'))) {
        //get store
        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));

        //store not found
        if(!$store)
          return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

        //set keyword
        $keyword = trim($request->get('keyword')) ? trim($request->get('keyword')) : '';

        //get coupons
        $entities = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAllCoupons(false, $keyword, true);
        for($i=0;$i<count($entities);$i++) {
          //update status
          $entities[$i]['status'] = $entities[$i]['status'] ? 'ACTIVE' : 'INACTIVE';

          //is locked for PROMO
          $entities[$i]['locked'] = $entities[$i]['isLocked'];

          //update endDate
          $entities[$i]['endDate'] = $entities[$i]['endDate'] ? $entities[$i]['endDate']->format('m/d/Y')  : 'NA';

          //update isDefault and position
          $entities[$i]['isDefault'] = $entities[$i]['isDefault'] ? 'Yes' : 'No';
          $entities[$i]['position'] = $entities[$i]['position'] ? $entities[$i]['position'] : '-';

          //build images urls
          $extension = StringUtility::getFileInfo($entities[$i]['image'], 'extension');
          $fileName = StringUtility::getFileInfo($entities[$i]['image'], 'filename');
          $entities[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
          $entities[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
          $entities[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

          //get services
          $servicesArr = array();
          $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($entities[$i]['id']);
          if($services) {
            for($j=0;$j<count($services);$j++) {
              $servicesArr[] = $services[$j]->getServices()->getTitle();
            }
          }
          $entities[$i]['services'] = implode(", ", $servicesArr);

          //check if coupon is attached to store
          $checkStoreHasCoupon = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $entities[$i]['id']));
          if($checkStoreHasCoupon)
            $entities[$i]['isSelected'] = true;
          else
            $entities[$i]['isSelected'] = false;

          unset($entities[$i]['image']);
        }

        $finalData = $entities;
      }
      else {
        //set pagination and sorting
        $this->setListingConfigurations($request, $page, $noRecords, $sortField, $sortType);

        //set keyword
        $keyword = trim($request->get('keyword')) ? trim($request->get('keyword')) : '';

        //get coupons from DB
        $noTotal = $em->getRepository('AcmeDataBundle:Coupons')->getCMSCouponsCount($keyword);
        $entities = $em->getRepository('AcmeDataBundle:Coupons')->getCMSCoupons($page, $noRecords, $sortField, $sortType, $keyword);

        //parse data
        for($i=0;$i<count($entities);$i++) {

          //update status
          $entities[$i]['status'] = $entities[$i]['status'] ? 'ACTIVE' : 'INACTIVE';

          //is locked for PROMO
          $entities[$i]['locked'] = $entities[$i]['isLocked'];

          //update endDate
          $entities[$i]['endDate'] = $entities[$i]['endDate'] ? $entities[$i]['endDate']->format('m/d/Y')  : 'NA';

          //update isDefault and position
          $isDefault = $entities[$i]['isDefault'] ? 'Yes' : 'No';
          $position = '-';
          if($entities[$i]['isDefault'])
            $position = $entities[$i]['position'] ? $entities[$i]['position'] : '-';

          $entities[$i]['isDefault'] = $isDefault;
          $entities[$i]['position'] = $position;

          //build images urls
          $extension = StringUtility::getFileInfo($entities[$i]['image'], 'extension');
          $fileName = StringUtility::getFileInfo($entities[$i]['image'], 'filename');
          $entities[$i]['imageName'] = $fileName;
          $entities[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
          $entities[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
          $entities[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

          //get services
          $servicesArr = array();
          $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($entities[$i]['id']);
          if($services) {
            for($j=0;$j<count($services);$j++) {
              $servicesArr[] = $services[$j]->getServices()->getTitle();
            }
          }
          $entities[$i]['services'] = implode(", ", $servicesArr);

          unset($entities[$i]['image']);
        }

        $finalData = array('coupons' => $entities, 'noTotal' => $noTotal);
      }

      //return response
      return ApiResponse::setResponse($finalData);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get coupon details service used to get all info for a single coupon.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS coupon info",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="coupon id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/coupon/{id}")
   *
   */
  public function getCouponCMSAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($id);

      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //build data
      $entity['title'] = $coupon->getTitle();
      //get services
      $servicesArr = array();
      $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupon->getId());
      if($services) {
        for($i=0;$i<count($services);$i++) {
          $servicesArr[$i]['id'] = $services[$i]->getServices()->getId();
          $servicesArr[$i]['title'] = $services[$i]->getServices()->getTitle();
        }
      }
      $entity['services'] = $servicesArr;
      //build images urls
      $extension = StringUtility::getFileInfo($coupon->getImage(), 'extension');
      $fileName = StringUtility::getFileInfo($coupon->getImage(), 'filename');
      $entity['imageName'] = $coupon->getImage();
      $entity['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupon->getImage();
      $entity['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
      $entity['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

      $entity['category'] = $coupon->getCategory();

      if($entity['category'] === CouponsCategory::PROMO) {
        $entity['priority'] = $coupon->getOrderIdx() == 100 ? 'Last' : $coupon->getOrderIdx();
        $entity['isLocked'] = $coupon->getIsLocked() ? 1 : 0;
        //get all storesIds
        $stores = array();
        $storesIds = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByCoupons($coupon->getId());
        if($storesIds) {
          for($i=0;$i<count($storesIds);$i++) {
            $stores[] = $storesIds[$i]->getStores()->getStoreId();
          }
        }

        $entity['stores'] = implode(',', $stores);
      }

      if($entity['category'] === CouponsCategory::STORE) {
        $entity['isDefault'] = $coupon->getIsDefault() ? 1 : 0;
        $positions = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAllCoupons(true);
        if($coupon->getIsDefault()) {
          $entity['position'] = $coupon->getOrderIdx();
          $entity['allPositions'] = count($positions);
        }
        else {
          $entity['position'] = count($positions) + 1;
          $entity['allPositions'] = count($positions) + 1;
        }
      }

      if($entity['category'] === CouponsCategory::METRO) {
        //get metro ids for this coupon
        $metros = array();
        $metroIds = $em->getRepository('AcmeDataBundle:DmaHasCoupons')->findByCoupons($coupon->getId());
        if($metroIds) {
          for($i=0;$i<count($metroIds);$i++) {
            $metros[] = $metroIds[$i]->getDma()->getId();
          }
        }

        $entity['metros'] = $metros;
      }

      $entity['locked'] = $coupon->getIsLocked();
      $entity['barcode'] = $coupon->getBarcode();
      $entity['barcodeMail'] = $coupon->getBarcodeMail();
      $entity['barcodeEmail'] = $coupon->getBarcodeEmail();
      $entity['status'] = $coupon->getStatus() ? 'ACTIVE' : 'INACTIVE';
      $entity['endDate'] = $coupon->getEndDate() ? $coupon->getEndDate()->format('m/d/Y') : 'NA';

      //return response
      return ApiResponse::setResponse($entity);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get stores info service used to get all stores for a specific coupon.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS stores info for a specific coupon",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="coupon id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/coupons/stores/{id}")
   *
   */
  public function getCouponsStoresCMSAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($id);

      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //get stores from DB
      $stores = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByCoupons($id);
      $entities = array();

      //parse data
      if($stores) {
        for($i=0;$i<count($stores);$i++) {

          //store data
          $entities[$i]['storeId'] = $stores[$i]->getStores()->getStoreId();
          $entities[$i]['locationCity'] = $stores[$i]->getStores()->getLocationCity();
          $entities[$i]['locationState'] = $stores[$i]->getStores()->getLocationState();

        }
      }

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Set coupon status service used to edit coupon status.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Set CMS coupon status",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="coupon id"}
   *     },
   *     parameters={
   *         {"name"="status", "dataType"="string", "required"=true, "description"="coupon status, ACTIVE | INACTIVE"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/coupon/status/{id}")
   *
   */
  public function setCouponStatusCMSAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'couponStatus');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($id);

      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //set new status
      if(trim($request->get('status')) === CouponsStatus::ACTIVE) $status = 1;
      else if(trim($request->get('status')) === CouponsStatus::INACTIVE) $status = 0;
      $coupon->setStatus($status);

      $em->flush();

      return ApiResponse::setResponse('Status successfully updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Add coupons service used to add a new coupon for CMS Dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Add CMS coupons",
   *     parameters={
   *         {"name"="title", "dataType"="string", "required"=true, "description"="coupon title"},
   *         {"name"="services", "dataType"="string", "required"=false, "description"="coupon services, services ids separated by comma"},
   *         {"name"="imageMedium", "dataType"="string", "required"=true, "description"="coupon medium image name"},
   *         {"name"="imageLarge", "dataType"="string", "required"=true, "description"="coupon large image name"},
   *         {"name"="isBarcode", "dataType"="integer", "required"=false, "description"="if coupon bar code for web exists, 0 | 1"},
   *         {"name"="barcode", "dataType"="string", "required"=false, "description"="coupon bar code for web"},
   *         {"name"="isBarcodeMail", "dataType"="integer", "required"=false, "description"="if coupon bar code for mail exists, 0 | 1"},
   *         {"name"="barcodeMail", "dataType"="string", "required"=false, "description"="coupon bar code for mail"},
   *         {"name"="isBarcodeEmail", "dataType"="integer", "required"=false, "description"="if coupon bar code for email exists, 0 | 1"},
   *         {"name"="barcodeEmail", "dataType"="string", "required"=false, "description"="coupon bar code for email"},
   *         {"name"="category", "dataType"="string", "required"=true, "description"="coupon type: STORE | PROMO | METRO"},
   *         {"name"="status", "dataType"="string", "required"=true, "description"="coupon type: ACTIVE | INACTIVE"},
   *         {"name"="stores", "dataType"="string", "required"=false, "description"="stores ids separated by comma, required only for PROMO category"},
   *         {"name"="priority", "dataType"="string", "required"=false, "description"="coupon priority, required only for PROMO category"},
   *         {"name"="isDefault", "dataType"="integer", "required"=false, "description"="coupon set default or not, 0 | 1, required only for STORE category"},
   *         {"name"="metros", "dataType"="integer", "required"=false, "description"="metros ids separated by comma, required only for METRO category"},
   *         {"name"="endDate", "dataType"="integer", "required"=false, "description"="coupon endDate, required for PROMO category"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/coupons")
   *
   */
  public function addCouponsCMSAction(Request $request) {

    set_time_limit(0);

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'coupons');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    $em->getConnection()->beginTransaction();
    try {

      //create coupon
      $entity = new Coupons();

      $entity->setTitle(trim($request->get('title')));
      $entity->setImage(str_replace('-med', '', trim($request->get('imageMedium'))));
      if(trim($request->get('isBarcode'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcode')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcode')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcode')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $entity->setBarcode(trim($request->get('barcode')));
      } else {
        $entity->setBarcode(null);
      }
      if(trim($request->get('isBarcodeMail'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcodeMail')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcodeMail')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcodeMail')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $entity->setBarcodeMail(trim($request->get('barcodeMail')));
      } else {
        $entity->setBarcodeMail(null);
      }
      if(trim($request->get('isBarcodeEmail'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcodeEmail')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcodeEmail')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcodeEmail')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $entity->setBarcodeEmail(trim($request->get('barcodeEmail')));
      } else {
        $entity->setBarcodeEmail(null);
      }
      $entity->setCategory(trim($request->get('category')));
      $entity->setStatus(trim($request->get('status')) === CouponsStatus::ACTIVE ? 1 : 0);
      if(trim($request->get('endDate')))
        $entity->setEndDate(new \DateTime(trim($request->get('endDate'))));
      if(trim($request->get('isDefault')))
        $entity->setIsDefault(1);
      else
        $entity->setIsDefault(0);

      if(trim($request->get('locked')))
        $entity->setIsLocked(true);
      else
        $entity->setIsLocked(false);
      if(trim($request->get('isDefault'))) {
        //set orderIdx
        $maxPosition = $em->getRepository('AcmeDataBundle:Coupons')->getCMSMaxPosition();
        if($maxPosition)
          $entity->setOrderIdx($maxPosition + 1);
        else
          $entity->setOrderIdx(1);
      }

      if(trim($request->get('stores'))) {
        if(trim($request->get('priority')) === 'Last') {
          $entity->setOrderIdx(100);
        }
        else {
          $entity->setOrderIdx(trim($request->get('priority')));
        }
      }

      $em->persist($entity);
      $em->flush();

      //check services
      if(trim($request->get('services'))) {
        //get ids
        $servicesIds = explode(',', trim($request->get('services')));

        //create coupons has services
        for($i=0;$i<count($servicesIds);$i++) {
          $entityCouponsHasServices = new CouponsHasServices();
          $entityCouponsHasServices->setCoupons($entity);
          $entityCouponsHasServices->setServices($em->getRepository('AcmeDataBundle:Services')->findOneById($servicesIds[$i]));

          $em->persist($entityCouponsHasServices);
          $em->flush();
        }
      }

      //check stores
      if(trim($request->get('stores'))) {
        //get ids
        $storesIds = explode(',', trim($request->get('stores')));

        //create stores has coupons
        if(trim($request->get('category') == CouponsCategory::PROMO)) {
          if(trim($request->get('locked'))) {
            for($i=0;$i<count($storesIds);$i++) {
              $entityStoresHasCoupons = new StoresHasCoupons();
              $entityStoresHasCoupons->setCoupons($entity);
              $entityStoresHasCoupons->setStores($em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($storesIds[$i]));
              $entityStoresHasCoupons->setOrderIdx(trim($request->get('priority')) === 'Last' ? 100 : trim($request->get('priority')));
              $em->persist($entityStoresHasCoupons);
              $em->flush();
            }
          } else {
            for($i=0;$i<count($storesIds);$i++) {
              $storeIsOptIn = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($storesIds[$i]);
              if(!$storeIsOptIn->getOptin()==1) {
                $entityStoresHasCoupons = new StoresHasCoupons();
                $entityStoresHasCoupons->setCoupons($entity);
                $entityStoresHasCoupons->setStores($em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($storesIds[$i]));
                $entityStoresHasCoupons->setOrderIdx(trim($request->get('priority')) === 'Last' ? 100 : trim($request->get('priority')));
                $em->persist($entityStoresHasCoupons);
                $em->flush();
              }
            }
          }
        } else {
          for($i=0;$i<count($storesIds);$i++) {
            $entityStoresHasCoupons = new StoresHasCoupons();
            $entityStoresHasCoupons->setCoupons($entity);
            $entityStoresHasCoupons->setStores($em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($storesIds[$i]));
            $entityStoresHasCoupons->setOrderIdx(trim($request->get('priority')) === 'Last' ? 100 : trim($request->get('priority')));
            $em->persist($entityStoresHasCoupons);
            $em->flush();
          }
        }
      }

      //check metros
      if(trim($request->get('metros'))) {
        //get ids
        $metrosIds = explode(',', trim($request->get('metros')));

        //create dma has coupons
        for($i=0;$i<count($metrosIds);$i++) {
          $entityDmaHasCoupons = new DmaHasCoupons();
          $entityDmaHasCoupons->setDma($em->getRepository('AcmeDataBundle:Dma')->findOneById($metrosIds[$i]));
          $entityDmaHasCoupons->setCoupons($entity);
          $entityDmaHasCoupons->setOrderIdx($i+1);

          $em->persist($entityDmaHasCoupons);
          $em->flush();
        }
      }

      $em->getConnection()->commit();

      //return response
      return ApiResponse::setResponse('Coupon successfully added.');
    }
    catch(\Exception $e) {
      $em->getConnection()->rollback();
      $em->close();

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Edit coupon details service used to edit info for a single coupon.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Edit CMS coupon info",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="coupon id"}
   *     },
   *     parameters={
   *         {"name"="title", "dataType"="string", "required"=true, "description"="coupon title"},
   *         {"name"="services", "dataType"="string", "required"=false, "description"="coupon services, services ids separated by comma"},
   *         {"name"="imageMedium", "dataType"="string", "required"=true, "description"="coupon medium image name"},
   *         {"name"="imageLarge", "dataType"="string", "required"=true, "description"="coupon large image name"},
   *         {"name"="isBarcode", "dataType"="integer", "required"=false, "description"="if coupon bar code for web exists, 0 | 1"},
   *         {"name"="barcode", "dataType"="string", "required"=false, "description"="coupon bar code for web"},
   *         {"name"="isBarcodeMail", "dataType"="integer", "required"=false, "description"="if coupon bar code for mail exists, 0 | 1"},
   *         {"name"="barcodeMail", "dataType"="string", "required"=false, "description"="coupon bar code for mail"},
   *         {"name"="isBarcodeEmail", "dataType"="integer", "required"=false, "description"="if coupon bar code for email exists, 0 | 1"},
   *         {"name"="barcodeEmail", "dataType"="string", "required"=false, "description"="coupon bar code for email"},
   *         {"name"="category", "dataType"="string", "required"=true, "description"="coupon type: STORE | PROMO | METRO"},
   *         {"name"="status", "dataType"="string", "required"=true, "description"="coupon type: ACTIVE | INACTIVE"},
   *         {"name"="stores", "dataType"="string", "required"=false, "description"="stores ids separated by comma, required only for PROMO category"},
   *         {"name"="priority", "dataType"="string", "required"=false, "description"="coupon priority, required only for PROMO category"},
   *         {"name"="isDefault", "dataType"="integer", "required"=false, "description"="coupon set default or not, 0 | 1, required only for STORE category"},
   *         {"name"="position", "dataType"="integer", "required"=false, "description"="coupon position, required only for STORE category"},
   *         {"name"="metros", "dataType"="integer", "required"=false, "description"="metros ids separated by comma, required only for METRO category"},
   *         {"name"="endDate", "dataType"="integer", "required"=false, "description"="coupon endDate, required for PROMO category"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/coupon/{id}")
   *
   */
  public function setCouponCMSAction(Request $request, $id) {

    set_time_limit(0);

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'coupons');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    $em->getConnection()->beginTransaction();
    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($id);

      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //set new info
      $coupon->setTitle(trim($request->get('title')));
      $coupon->setImage(str_replace('-med', '', trim($request->get('imageMedium'))));
      if(trim($request->get('isBarcode'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcode')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcode')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcode')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $coupon->setBarcode(trim($request->get('barcode')));
      } else {
        $coupon->setBarcode(null);
      }
      if(trim($request->get('isBarcodeMail'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcodeMail')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcodeMail')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcodeMail')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $coupon->setBarcodeMail(trim($request->get('barcodeMail')));
      } else {
        $coupon->setBarcodeMail(null);
      }
      if(trim($request->get('isBarcodeEmail'))) {

        $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . trim($request->get('barcodeEmail')) . '.png';

        if(!file_exists($barcodeImg)) {
          //create barcode image and upload to CDN
          //set object
          $bc = new Barcode39(trim($request->get('barcodeEmail')));
          //set text size
          $bc->barcode_text_size = 5;
          //set barcode bar thickness (thick bars)
          $bc->barcode_bar_thick = 4;
          //set barcode bar thickness (thin bars)
          $bc->barcode_bar_thin = 2;
          //save barcode PNG file
          $bc->draw($barcodeImg);
          //upload to CDN
          //initiate S3
          $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
          $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . trim($request->get('barcodeEmail')) . '.png', S3::ACL_PUBLIC_READ);
        }

        $coupon->setBarcodeEmail(trim($request->get('barcodeEmail')));
      } else {
        $coupon->setBarcodeEmail(null);
      }

      $coupon->setCategory(trim($request->get('category')));
      $coupon->setStatus(trim($request->get('status')) === CouponsStatus::ACTIVE ? 1 : 0);
      if(trim($request->get('endDate')))
        $coupon->setEndDate(new \DateTime(trim($request->get('endDate'))));

      if(trim($request->get('locked')))
        $coupon->setIsLocked(true);
      else
        $coupon->setIsLocked(false);

      if(trim($request->get('isDefault'))) {
        $coupon->setIsDefault(1);
        //inter change positions
        $currentCoupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneBy(array('category' => CouponsCategory::STORE, 'isDefault' => 1, 'orderIdx' => trim($request->get('position'))));
        if($currentCoupon) {
          $maxPosition = $em->getRepository('AcmeDataBundle:Coupons')->getCMSMaxPosition();
          $currentCoupon->setOrderIdx($coupon->getOrderIdx() ? $coupon->getOrderIdx() : $maxPosition + 1);
          $em->persist($currentCoupon);
          $em->flush();
        }

        //set orderIdx
        $coupon->setOrderIdx(trim($request->get('position')));
      }
      else {
        $coupon->setIsDefault(0);
        //get all store default coupons after
        $defaultCoupons = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAfterDefaultCoupons(CouponsCategory::STORE, $coupon->getOrderIdx());
        if($defaultCoupons) {
          for($i=0;$i<count($defaultCoupons);$i++) {
            //update order
            $defaultCoupons[$i]->setOrderIdx($defaultCoupons[$i]->getOrderIdx() - 1);
            $em->persist($defaultCoupons[$i]);
            $em->flush();
          }
        }

      }

      if(trim($request->get('stores'))) {
        if(trim($request->get('priority')) === 'Last') {
          $coupon->setOrderIdx(100);
        }
        else {
          $coupon->setOrderIdx(trim($request->get('priority')));
        }
      }

      $em->persist($coupon);
      $em->flush();

      //check services
      if(trim($request->get('services'))) {
        //get ids
        $servicesIds = explode(',', trim($request->get('services')));

        //remove all if exists
        $couponsHasServices = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupon->getId());
        if($couponsHasServices) {
          foreach($couponsHasServices as $chs) {
            $em->remove($chs);
            $em->flush();
          }
        }

        //create coupons has services
        for($i=0;$i<count($servicesIds);$i++) {
          $entityCouponsHasServices = new CouponsHasServices();
          $entityCouponsHasServices->setCoupons($coupon);
          $entityCouponsHasServices->setServices($em->getRepository('AcmeDataBundle:Services')->findOneById($servicesIds[$i]));

          $em->persist($entityCouponsHasServices);
          $em->flush();
        }
      }

      //check stores
      if(trim($request->get('stores'))) {
        //get ids
        $storesIds = explode(',', trim($request->get('stores')));

        //remove all promos coupons if exists
        $storesHasCoupons = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByCoupons($coupon->getId());
        if($storesHasCoupons) {
          foreach($storesHasCoupons as $shc) {
            $em->remove($shc);
            $em->flush();
          }
        }

        //create stores has coupons
        for($i=0;$i<count($storesIds);$i++) {
          $entityStoresHasCoupons = new StoresHasCoupons();
          $entityStoresHasCoupons->setCoupons($coupon);
          $entityStoresHasCoupons->setStores($em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($storesIds[$i]));
          $entityStoresHasCoupons->setOrderIdx(trim($request->get('priority')) === 'Last' ? 100 : trim($request->get('priority')));

          $em->persist($entityStoresHasCoupons);
          $em->flush();
        }
      }

      //check metros
      if(trim($request->get('metros'))) {
        //get ids
        $metrosIds = explode(',', trim($request->get('metros')));

        //remove all if exists
        $dmaHasCoupons = $em->getRepository('AcmeDataBundle:DmaHasCoupons')->findByCoupons($coupon->getId());
        if($dmaHasCoupons) {
          foreach($dmaHasCoupons as $dhc) {
            $em->remove($dhc);
            $em->flush();
          }
        }

        //create dma has coupons
        for($i=0;$i<count($metrosIds);$i++) {
          $entityDmaHasCoupons = new DmaHasCoupons();
          $entityDmaHasCoupons->setDma($em->getRepository('AcmeDataBundle:Dma')->findOneById($metrosIds[$i]));
          $entityDmaHasCoupons->setCoupons($coupon);
          $entityDmaHasCoupons->setOrderIdx($i+1);

          $em->persist($entityDmaHasCoupons);
          $em->flush();
        }
      }

      $em->getConnection()->commit();

      //return response
      return ApiResponse::setResponse('Coupon successfully saved.');
    }
    catch(\Exception $e) {
      $em->getConnection()->rollback();
      $em->close();

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Delete coupon service used to delete a single coupon.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Delete CMS coupon",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="coupon id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Delete("/secured/coupon/{id}")
   *
   */
  public function deleteSingleCouponCMSAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($id);

      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //delete stores has coupons if coupon is inactive
      if($coupon->getStatus() == 0) {
        $storesHasCoupons = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByCoupons($id);
        if($storesHasCoupons) {
          foreach($storesHasCoupons as $shc) {
            $em->remove($shc);
            $em->flush();
          }
        }
      }
      else {
        $storesHasCoupons = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByCoupons($id);
        if($storesHasCoupons) {
          return ApiResponse::setResponse('Coupon can not be deleted.', Codes::HTTP_CONFLICT);
        }
      }

      //delete coupon services
      $couponsHasServices = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($id);
      if($couponsHasServices) {
        foreach($couponsHasServices as $chs) {
          $em->remove($chs);
          $em->flush();
        }
      }

      //delete coupon
      $em->remove($coupon);
      $em->flush();

      //return response
      return ApiResponse::setResponse('Coupon successfully deleted.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add coupons service used to add CSV with bunch of coupons for CMS Dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Add CMS CSV with coupons",
   *     parameters={
   *         {"name"="serializedData", "dataType"="string", "required"=true, "description"="all coupons data serialized"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/coupons/batch")
   *
   */
  public function addCouponsBatchCMSAction(Request $request) {

    set_time_limit(0);

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'serialized');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    //get data
    $serializedData = $request->get('serializedData');
    $unserializedData = unserialize(base64_decode($serializedData));

    //convert all keys to lowercase
    $finalData = StringUtility::changeArrayKeyCase($unserializedData, CASE_LOWER);

    try {

      for($i=0;$i<count($finalData);$i++) {
        //check if we have coupon in database (unique identifier -> image name)
        $checkCoupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneByImage($finalData[$i]['image']);
        if($checkCoupon) {
          $entity = $checkCoupon;
        }
        else {
          $entity = new Coupons();
        }

        //create/edit coupon
        $entity->setTitle($finalData[$i]['title']);
        $entity->setImage($finalData[$i]['image']);
        if($finalData[$i]['barcode']) {

          $barcodeImg = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . 'barcode_' . $finalData[$i]['barcode'] . '.png';

          if(!file_exists($barcodeImg)) {
            //create barcode image and upload to CDN
            //set object
            $bc = new Barcode39($finalData[$i]['barcode']);
            //set text size
            $bc->barcode_text_size = 5;
            //set barcode bar thickness (thick bars)
            $bc->barcode_bar_thick = 4;
            //set barcode bar thickness (thin bars)
            $bc->barcode_bar_thin = 2;
            //save barcode PNG file
            $bc->draw($barcodeImg);
            //upload to CDN
            //initiate S3
            $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);
            $s3->putObjectFile($barcodeImg, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['couponsDirectory'] . 'barcode_' . $finalData[$i]['barcode'] . '.png', S3::ACL_PUBLIC_READ);
          }

          $entity->setBarcode($finalData[$i]['barcode']);
        }
        $entity->setCategory(CouponsCategory::STORE);
        $entity->setStatus($finalData[$i]['status'] === CouponsStatus::ACTIVE ? 1 : 0);
        if($finalData[$i]['enddate'])
          $entity->setEndDate(new \DateTime($finalData[$i]['enddate']));
        if($finalData[$i]['isdefault'])
          $entity->setIsDefault(1);
        else
          $entity->setIsDefault(0);

        if($finalData[$i]['isdefault']) {
          //set orderIdx
          $entity->setOrderIdx($finalData[$i]['position']);
        }

        $em->persist($entity);
        $em->flush();

        //check services
        if($finalData[$i]['services']) {
          //get services name
          $servicesNames = explode('|', $finalData[$i]['services']);

          //remove all if exists
          $couponsHasServices = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($entity->getId());
          if($couponsHasServices) {
            foreach($couponsHasServices as $chs) {
              $em->remove($chs);
              $em->flush();
            }
          }

          //create coupons has services
          for($j=0;$j<count($servicesNames);$j++) {
            $entityCouponsHasServices = new CouponsHasServices();
            $entityCouponsHasServices->setCoupons($entity);

            $service = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($servicesNames[$j]);
            if($service) {
              $serviceEntity = $service;
            }
            else {
              $serviceEntity = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('General');
            }

            $entityCouponsHasServices->setServices($serviceEntity);

            $em->persist($entityCouponsHasServices);
            $em->flush();
          }
        }
        else {
          //remove all if exists
          $couponsHasServices = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($entity->getId());
          if($couponsHasServices) {
            foreach($couponsHasServices as $chs) {
              $em->remove($chs);
              $em->flush();
            }
          }
        }

      }

      //return response
      return ApiResponse::setResponse('Coupons data successfully added/updated.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Delete coupon from store service used to delete coupon from store in CMS dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Delete coupon CMS from a specific store",
   *     parameters={
   *         {"name"="couponId", "dataType"="integer", "required"=true, "description"="coupon id"},
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the store/coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Delete("/secured/coupon")
   *
   */
  public function deleteCouponCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'couponDelete');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById(trim($request->get('couponId')));
      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //check store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //check store has coupons
      $storeHasCoupon = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $coupon->getId()));

      //remove from DB
      if($storeHasCoupon) {
        $em->remove($storeHasCoupon);
        $em->flush();
      }

      //return response
      return ApiResponse::setResponse('Coupon successfully removed.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add coupon from store service used to add coupon for store in CMS dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Add coupon CMS for a specific store",
   *     parameters={
   *         {"name"="couponId", "dataType"="integer", "required"=true, "description"="coupon id"},
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the store/coupon is not found.",
   *         409="Returned when the coupon already exists for the store.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/coupon")
   *
   */
  public function addCouponCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'couponAdd');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //check coupon
      $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById(trim($request->get('couponId')));
      //coupon not found
      if(!$coupon)
        return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

      //check store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //check store has coupons
      $storeHasCoupon = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $coupon->getId()));
      if($storeHasCoupon)
        return ApiResponse::setResponse('Coupon already exists for this store.', Codes::HTTP_CONFLICT);

      $entity = new StoresHasCoupons();
      $entity->setStores($store);
      $entity->setCoupons($coupon);

      $maxPosition = $em->getRepository('AcmeDataBundle:Coupons')->getCMSStoreHasCouponMaxPosition($store->getId());
      if($maxPosition)
        $entity->setOrderIdx($maxPosition + 1);
      else
        $entity->setOrderIdx(1);

      $em->persist($entity);
      $em->flush();

      //return response
      return ApiResponse::setResponse('Coupon successfully added.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Reorder coupons service used to reorder coupons positions for store in CMS dashboard.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Reorder coupons CMS for a specific store",
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"},
   *         {"name"="couponIds", "dataType"="string", "required"=true, "description"="coupon ids separated by comma"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the store/coupon is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/coupons/reorder")
   *
   */
  public function reorderCouponsCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'couponsReorder');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //check store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      $couponIds = explode(",", trim($request->get('couponIds')));
      for($i=0;$i<count($couponIds);$i++) {
        //check coupon
        $coupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($couponIds[$i]);
        //coupon not found
        if(!$coupon)
          return ApiResponse::setResponse('Coupon not found.', Codes::HTTP_NOT_FOUND);

        //check store has coupons
        $storeHasCoupon = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $couponIds[$i]));
        if(!$storeHasCoupon)
          return ApiResponse::setResponse('Coupon not found for the store specified.', Codes::HTTP_NOT_FOUND);
      }

      //all checks are good, save data in DB
      for($i=0;$i<count($couponIds);$i++) {
        $storeHasCoupon = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $couponIds[$i]));

        $storeHasCoupon->setOrderIdx($i+1);

        $em->persist($storeHasCoupon);
        $em->flush();
      }

      //return response
      return ApiResponse::setResponse('Order successfully saved.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Stores metros used to get a CMS list of all Meineke location metros.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="List CMS metros",
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/stores/metros")
   *
   */
  public function getStoresMetrosCMSAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {

      $entities = $em->getRepository('AcmeDataBundle:Stores')->getMetros();

      //parse data
      $metros = array();
      for($i=0;$i<count($entities);$i++) {
        $metros[$i]['id'] = $entities[$i]['id'];
        $metros[$i]['metroName'] = $entities[$i]['metro'] . ' Metro';
        $metros[$i]['metroURL'] = $this->getFrontURL() . 'locations/' . strtolower($entities[$i]['state']) . '/' . $entities[$i]['metroSlug'] . '/';
      }

      //return response
      return ApiResponse::setResponse($metros);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get CMS store service used to get store details.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Get CMS store details",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/store/{id}")
   *
   */
  public function getCMSStoreAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Stores')->getStore($id);

      //store not found
      if(!$entity)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      $storeDetails = array();
      $storeDetails['optin'] = $entity[0]['optin'] ? $entity[0]['optin'] : false;

      if($entity[0]['optin']) {
        //get coupons for store
        $myCoupons = array();
        $coupons = $em->getRepository('AcmeDataBundle:Coupons')->getCMSMyCoupons($entity[0]['id'], CouponsCategory::STORE);
        if($coupons) {
          for($i=0;$i<count($coupons);$i++) {
            //set id and title
            $myCoupons[$i]['id'] = $coupons[$i]['id'];
            $myCoupons[$i]['title'] = $coupons[$i]['title'];
            //build images urls
            $extension = StringUtility::getFileInfo($coupons[$i]['image'], 'extension');
            $fileName = StringUtility::getFileInfo($coupons[$i]['image'], 'filename');
            $myCoupons[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$i]['image'];
            $myCoupons[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
            $myCoupons[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

            //update endDate
            $myCoupons[$i]['endDate'] = $coupons[$i]['endDate'] ? $coupons[$i]['endDate']->format('m/d/Y')  : 'NA';

            //get services
            $servicesArr = array();
            $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupons[$i]['id']);
            if($services) {
              for($j=0;$j<count($services);$j++) {
                $servicesArr[] = $services[$j]->getServices()->getTitle();
              }
            }
            $myCoupons[$i]['services'] = implode(", ", $servicesArr);
          }
        }

        $storeDetails['myCoupons'] = $myCoupons;
      }
      else {
        //get default coupons
        $defaultCoupons = array();
        $coupons = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAllCoupons(true, '', true);
        if($coupons) {
          for($i=0;$i<count($coupons);$i++) {
            //set id and title
            $defaultCoupons[$i]['id'] = $coupons[$i]['id'];
            $defaultCoupons[$i]['title'] = $coupons[$i]['title'];
            //build images urls
            $extension = StringUtility::getFileInfo($coupons[$i]['image'], 'extension');
            $fileName = StringUtility::getFileInfo($coupons[$i]['image'], 'filename');
            $defaultCoupons[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$i]['image'];
            $defaultCoupons[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
            $defaultCoupons[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

            //update endDate
            $defaultCoupons[$i]['endDate'] = $coupons[$i]['endDate'] ? $coupons[$i]['endDate']->format('m/d/Y')  : 'NA';

            //get services
            $servicesArr = array();
            $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupons[$i]['id']);
            if($services) {
              for($j=0;$j<count($services);$j++) {
                $servicesArr[] = $services[$j]->getServices()->getTitle();
              }
            }
            $defaultCoupons[$i]['services'] = implode(", ", $servicesArr);
          }
        }

        $storeDetails['myCoupons'] = $defaultCoupons;
      }

      //get corporate coupons (promo coupons)
      $promoCoupons = array();
      $coupons = $em->getRepository('AcmeDataBundle:Coupons')->getCMSMyCoupons($entity[0]['id'], CouponsCategory::PROMO);
      if($coupons) {
        for($i=0;$i<count($coupons);$i++) {
          //inconsistenta (isLocked=true dar daca este selectat cuponul de FZ si este unlocked nu va fi afisat))
          if ($coupons[$i]['isLocked'] == 1 && $entity[0]['optin']) {
            //set id and title
            $promoCoupons[$i]['id'] = $coupons[$i]['id'];
            $promoCoupons[$i]['title'] = $coupons[$i]['title'];
            //build images urls
            $extension = StringUtility::getFileInfo($coupons[$i]['image'], 'extension');
            $fileName = StringUtility::getFileInfo($coupons[$i]['image'], 'filename');
            $promoCoupons[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$i]['image'];
            $promoCoupons[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
            $promoCoupons[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

            //update endDate and position
            $promoCoupons[$i]['endDate'] = $coupons[$i]['endDate'] ? $coupons[$i]['endDate']->format('m/d/Y') : 'NA';
            $promoCoupons[$i]['position'] = $coupons[$i]['position'] == 100 ? 'Last' : $coupons[$i]['position'];

            //get services
            $servicesArr = array();
            $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupons[$i]['id']);
            if ($services) {
              for ($j = 0; $j < count($services); $j++) {
                $servicesArr[] = $services[$j]->getServices()->getTitle();
              }
            }
            $promoCoupons[$i]['services'] = implode(", ", $servicesArr);
          } elseif(!$entity[0]['optin']) {
            //set id and title
            $promoCoupons[$i]['id'] = $coupons[$i]['id'];
            $promoCoupons[$i]['title'] = $coupons[$i]['title'];
            //build images urls
            $extension = StringUtility::getFileInfo($coupons[$i]['image'], 'extension');
            $fileName = StringUtility::getFileInfo($coupons[$i]['image'], 'filename');
            $promoCoupons[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$i]['image'];
            $promoCoupons[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
            $promoCoupons[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

            //update endDate and position
            $promoCoupons[$i]['endDate'] = $coupons[$i]['endDate'] ? $coupons[$i]['endDate']->format('m/d/Y') : 'NA';
            $promoCoupons[$i]['position'] = $coupons[$i]['position'] == 100 ? 'Last' : $coupons[$i]['position'];

            //get services
            $servicesArr = array();
            $services = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findByCoupons($coupons[$i]['id']);
            if ($services) {
              for ($j = 0; $j < count($services); $j++) {
                $servicesArr[] = $services[$j]->getServices()->getTitle();
              }
            }
            $promoCoupons[$i]['services'] = implode(", ", $servicesArr);
          }
        }
      }

      $storeDetails['corporateCoupons'] = $promoCoupons;

      //return response
      return ApiResponse::setResponse($storeDetails);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Set CMS store optin/optout service used to edit store choice.
   *
   * @ApiDoc(
   *     section="CMS Coupons Dashboard",
   *     resource=true,
   *     description="Set CMS store choice",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     parameters={
   *         {"name"="optin", "dataType"="string", "required"=true, "description"="store choice, 0 | 1"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/store/optin/{id}")
   *
   */
  public function setStoreOptinCMSAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'storeChoice');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //check store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($id);

      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //set new optin
      $store->setOptin(trim($request->get('optin')));

      $em->flush();

      return ApiResponse::setResponse('Store choice successfully updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}
