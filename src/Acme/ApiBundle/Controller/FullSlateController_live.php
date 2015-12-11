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
use Acme\DataBundle\Entity\Appointments;
use Acme\DataBundle\Entity\AppointmentsHasServices;
use Acme\DataBundle\Entity\Services;
use Acme\DataBundle\Entity\StoresHasServices;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Utility\FullSlate;
use Acme\DataBundle\Model\Utility\StringUtility;


class FullSlateController extends ApiController implements ClassResourceInterface {

  /**
   * Get store services from Full Slate used to get store services in Full Slate.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get store services from Full Slate",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when request failed/parameters are invalid.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/store/fullslate/services/{id}/")
   *
   */
  public function getStoreServicesFromFullSlateAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    try {

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($id);
      //store not found
      if(!$entity)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      if(!$entity->getHasFullSlate())
        return ApiResponse::setResponse('Store doesn\'t have Full Slate.', Codes::HTTP_NOT_FOUND);

      //call Full Slate API
      $result = FullSlate::getFullSlateServices($id);

      //no response from Full Slate
      if(!$result)
        return ApiResponse::setResponse('Full Slate API error', Codes::HTTP_BAD_REQUEST);

      //Full Slate data
      $services = json_decode($result, true);

      //error message from Full Slate
      if(isset($services['failure']) && $services['failure'] == 1) {
        //update store, no more has FullSlate
        $entity->setHasFullSlate(0);
        $em->persist($entity);
        $em->flush();

        return ApiResponse::setResponse('Store doesn\'t have Full Slate.', Codes::HTTP_NOT_FOUND);
      }

      //get store coupons
      $optin = $entity->getOptin() ? 1 : 0;
      $coupons = $em->getRepository('AcmeDataBundle:Coupons')->getCoupons($entity->getId(), '', $optin);

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
        $coupons = StringUtility::arrayOrderBy($coupons, 'type', SORT_ASC, 'position', SORT_ASC);
      }

      //parse data
      $entities = array();
      for($i=0;$i<count($services);$i++) {
        $couponsFS = array();
        $icon = $this->container->parameters['project']['cdn_front_resources_url'] . 'images/generic-service.jpg';

        //check service
        $checkServiceIcon = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($services[$i]['name']);
        $serviceName = strtolower(preg_replace('/\s+/', '', $services[$i]['name']));
        switch($serviceName) {
          case 'oilchange':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Oil Change');
          break;
          case 'airconditioning':
          case 'a/c':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('A/C');
          break;
          case 'exhaust-muffler/pipes':
          case 'exhaust-catalyticconverter':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Exhaust & Mufflers');
          break;
          case 'brakes':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Brakes');
          break;
          case 'tires':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Tires & Wheels');
          break;
          case 'battery':
          case 'batteries':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Batteries');
          break;
          case 'shocks/struts':
            $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle('Steering & Suspension');
          break;
          default:
            $checkService = 0;
        }

        //service icon
        if($checkServiceIcon && $checkServiceIcon->getIcon())
          $icon = $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $checkServiceIcon->getIcon();

        if($coupons && $checkService) {
          //check coupon service
          for($j=0;$j<count($coupons);$j++) {
            //check coupon and service
            $entityCoupon = $em->getRepository('AcmeDataBundle:Coupons')->findOneByImage($coupons[$j]['image']);
            $checkCouponHasService = $em->getRepository('AcmeDataBundle:CouponsHasServices')->findBy(array('coupons' => $entityCoupon, 'services' => $checkService));
            if($checkCouponHasService) {
              //build images urls
              $extension = StringUtility::getFileInfo($coupons[$j]['image'], 'extension');
              $fileName = StringUtility::getFileInfo($coupons[$j]['image'], 'filename');

              //check if barcode exists
              $barcodeImg = $coupons[$j]['barcode'] ? $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/barcode_' . $coupons[$j]['barcode'] . '.png' : '';

              //add to coupons
              $couponsFS[] = array(
                'title' => $coupons[$j]['title'],
                'barCode' => $coupons[$j]['barcode'],
                'imageBarcode' => $barcodeImg,
                'imageSmall' => $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $coupons[$j]['image'],
                'imageMedium' => $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension,
                'imageLarge' => $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension
              );
            }
          }
        }

        $entities[$i]['id'] = $services[$i]['id'];
        $entities[$i]['name'] = $services[$i]['name'];
        $entities[$i]['icon'] = $icon;
        //return only first 2 coupons
        $entities[$i]['coupons'] = array_slice($couponsFS, 0, 2);
      }

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get store openings hours from Full Slate.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get store opening hours from Full Slate",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     parameters={
   *         {"name"="services", "dataType"="string", "required"=true, "description"="services ids separated by comma"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when request failed/parameters are invalid.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/store/fullslate/openings/{id}/")
   *
   */
  public function getStoreOpeningsFromFullSlateAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'openings');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($id);
      //store not found
      if(!$entity)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      if(!$entity->getHasFullSlate())
        return ApiResponse::setResponse('Store doesn\'t have Full Slate.', Codes::HTTP_NOT_FOUND);

      //get timezone from DB
      $timezone = $entity->getTimezone() ? $entity->getTimezone() : '';
      if(!strlen($timezone)) {
        //get timezone and updated in DB
        $timezone = 'PDT';
        $res = FullSlate::getFullSlateOpenings($id, trim($request->get('services')), true);
        if($res) {
          $tmz = json_decode($res, true);
          if($tmz['success']) {
            $timezone = $tmz['tz'];
            //update in DB
            $entity->setTimezone($timezone);
            $em->flush();
          }
        }
      }

      //call Full Slate API
      $result = FullSlate::getFullSlateOpenings($id, trim($request->get('services')));

      //no response from Full Slate
      if(!$result)
        return ApiResponse::setResponse('Full Slate API error', Codes::HTTP_BAD_REQUEST);

      //Full Slate data
      $hours = json_decode($result, true);

      //error message from Full Slate
      if(isset($hours['failure']) && $hours['failure'] == 1)
        return ApiResponse::setResponse($hours['errorMessage'], Codes::HTTP_BAD_REQUEST);

      //parse data and compose date time based on store timezone
      $openings = $hours['openings'];
      $entities = array();
      for($i=0;$i<count($openings);$i++) {
        $date = new \DateTime($openings[$i], new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($timezone)); //timezone from DB

        //this update only for LIVE (!!! + 1hour)
        $entities['rawData'][$date->format('Y-m-d')][] = date('h:i A', strtotime($date->format('Y-m-d H:i')) + 3600);
        $entities['data'][$date->format('Y-m-d')] = date('l, m/d/Y', strtotime($date->format('Y-m-d H:i')) + 3600);
      }

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Make an appointment service used to create appointment in Full Slate.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Create an appointment",
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"},
   *         {"name"="userId", "dataType"="integer", "required"=false, "description"="user id"},
   *         {"name"="services", "dataType"="string", "required"=true, "description"="services ids separated by comma"},
   *         {"name"="servicesNames", "dataType"="string", "required"=true, "description"="services names separated by *"},
   *         {"name"="dateTime", "dataType"="datetime", "required"=true, "description"="date and time for appointment"},
   *         {"name"="firstName", "dataType"="string", "required"=true, "description"="first name"},
   *         {"name"="lastName", "dataType"="string", "required"=true, "description"="last name"},
   *         {"name"="email", "dataType"="string", "required"=true, "description"="email address"},
   *         {"name"="phone", "dataType"="string", "required"=true, "description"="phone number"},
   *         {"name"="vehicleMake", "dataType"="string", "required"=true, "description"="vechicle make"},
   *         {"name"="vehicleModel", "dataType"="string", "required"=true, "description"="vehicle model"},
   *         {"name"="vehicleYear", "dataType"="string", "required"=true, "description"="vehicle year"},
   *         {"name"="comments", "dataType"="text", "required"=false, "description"="comments for appointment"},
   *         {"name"="vehicleDropoff", "dataType"="integer", "required"=false, "description"="dropping your vehicle off for service"},
   *         {"name"="waitForCar", "dataType"="integer", "required"=false, "description"="waiting while your car is serviced"},
   *         {"name"="textReminderSMS", "dataType"="integer", "required"=false, "description"="text reminders via SMS"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when request failed/parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/store/fullslate/appointment/")
   *
   */
  public function addStoreAppointmentAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'appointment');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //get store details
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      if(!$store->getHasFullSlate())
        return ApiResponse::setResponse('Store doesn\'t have Full Slate.', Codes::HTTP_NOT_FOUND);

      //get timezone from DB
      $timezone = $store->getTimezone() ? $store->getTimezone() : 'PDT';

      //call Full Slate API
      $result = FullSlate::saveFullSlateAppointment($request, $timezone);

      //no response from Full Slate
      if(!$result)
        return ApiResponse::setResponse('Full Slate API error', Codes::HTTP_BAD_REQUEST);

      //Full Slate data
      $appointment = json_decode($result, true);

      //error message from Full Slate
      if(isset($appointment['failure']) && $appointment['failure'] == 1)
        return ApiResponse::setResponse($appointment['errorMessage'], Codes::HTTP_BAD_REQUEST);

      //appointment successfully saved in Full Slate, add also in DB
      if(isset($appointment['id'])) {
        //add appointment to DB
        $entity = new Appointments();

        $entity->setFullSlateId($appointment['id']);
        $entity->setFirstName(trim($request->get('firstName')));
        $entity->setLastName(trim($request->get('lastName')));
        $entity->setEmail(trim($request->get('email')));
        $entity->setVehicleMake(trim($request->get('vehicleMake')));
        $entity->setVehicleModel(trim($request->get('vehicleModel')));
        $entity->setVehicleYear(trim($request->get('vehicleYear')));
        $entity->setAppointmentDate(new \DateTime(date("Y-m-d H:i:s", strtotime(trim($request->get('dateTime'))))));
        $entity->setComments(trim($request->get('comments')));
        if(trim($request->get('vehicleDropoff')))
          $entity->setVehicleDropoff(trim($request->get('vehicleDropoff')));
        if(trim($request->get('waitForCar')))
          $entity->setWaitForCar(trim($request->get('waitForCar')));
        if(trim($request->get('textReminderSMS')))
          $entity->setTextReminderSMS(trim($request->get('textReminderSMS')));
        $entity->setPhone(trim($request->get('phone')));
        $entity->setStores($store);
        if(trim($request->get('userId'))) {
          $user = $em->getRepository('AcmeDataBundle:Users')->findOneById(trim($request->get('userId')));
          if($user)
            $entity->setUsers($user);
        }

        $em->persist($entity);
        $em->flush();

        //appointments has services
        $services = explode("*", trim($request->get('servicesNames')));
        for($i=0;$i<count($services);$i++) {
          $entityService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($services[$i]);
          if($entityService) {
            $entityAHS = new AppointmentsHasServices();

            $entityAHS->setAppointments($entity);
            $entityAHS->setServices($entityService);

            $em->persist($entityAHS);
            $em->flush();
          }
        }

        return ApiResponse::setResponse('Appointment successfully saved.');
      }

      return ApiResponse::setResponse('Full Slate API error', Codes::HTTP_BAD_REQUEST);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
