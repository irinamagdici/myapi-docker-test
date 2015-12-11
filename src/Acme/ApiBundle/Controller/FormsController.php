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
use Acme\DataBundle\Entity\FleetServicesForm;
use Acme\DataBundle\Entity\RealEstateForm;
use Acme\DataBundle\Entity\Feedback;
use Acme\DataBundle\Entity\NewsletterSubscribers;
use Acme\DataBundle\Entity\PipelineSubscribers;
use Acme\DataBundle\Entity\CarCareClubForm;
use Acme\DataBundle\Entity\CarCareClubVehicles;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\StoresStatus;


class FormsController extends ApiController implements ClassResourceInterface {

  /**
   * Add Fleet services service used to save form data for Fleet Services page.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add/Email Fleet services form data",
   *     parameters={
   *         {"name"="organizationName", "dataType"="string", "required"=true, "description"="organization name"},
   *         {"name"="contactFullName", "dataType"="string", "required"=true, "description"="contact full name"},
   *         {"name"="contactPhone", "dataType"="string", "required"=true, "description"="contact phone"},
   *         {"name"="contactEmail", "dataType"="string", "required"=true, "description"="contact email"},
   *         {"name"="address", "dataType"="string", "required"=true, "description"="address"},
   *         {"name"="city", "dataType"="string", "required"=true, "description"="city"},
   *         {"name"="state", "dataType"="string", "required"=true, "description"="state"},
   *         {"name"="zipCode", "dataType"="string", "required"=true, "description"="zip code"},
   *         {"name"="totalVehicles", "dataType"="string", "required"=false, "description"="total vehicles in fleet"},
   *         {"name"="avgNumber", "dataType"="string", "required"=false, "description"="average number of vechicles serviced monthly"},
   *         {"name"="comments", "dataType"="string", "required"=false, "description"="comments"},
   *         {"name"="scheduleMaintenance", "dataType"="integer", "required"=false, "description"="0 | 1"},
   *         {"name"="purchaseOrderSystem", "dataType"="integer", "required"=false, "description"="0 | 1"},
   *         {"name"="centralizedBilling", "dataType"="integer", "required"=false, "description"="0 | 1"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/fleetservices/")
   *
   */
  public function addFleetServicesAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'fleetServices');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //create fleet service in DB
      $entity = new FleetServicesForm();

      $entity->setOrganizationName(trim($request->get('organizationName')));
      $entity->setContactFullName(trim($request->get('contactFullName')));
      $entity->setContactPhone(trim($request->get('contactPhone')));
      $entity->setContactEmail(trim($request->get('contactEmail')));
      $entity->setAddress(trim($request->get('address')));
      $entity->setCity(trim($request->get('city')));
      $entity->setState(trim($request->get('state')));
      $entity->setZipCode(trim($request->get('zipCode')));
      if(trim($request->get('totalVehicles')))
        $entity->setTotalVehicles(trim($request->get('totalVehicles')));
      if(trim($request->get('avgNumber')))
        $entity->setAvgNumber(trim($request->get('avgNumber')));
      if(trim($request->get('comments')))
        $entity->setComments(trim($request->get('comments')));
      if(trim($request->get('scheduleMaintenance')))
        $entity->setScheduleMaintenance(trim($request->get('scheduleMaintenance')));
      if(trim($request->get('purchaseOrderSystem')))
        $entity->setPurchaseOrderSystem(trim($request->get('purchaseOrderSystem')));
      if(trim($request->get('centralizedBilling')))
        $entity->setCentralizedBilling(trim($request->get('centralizedBilling')));

      $em->persist($entity);
      $em->flush();

      //send email with form data
      $this->get('emailNotificationBundle.email')->sendFleetServiceEmail($entity);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add Newsletter service used to save subscribers email addresses for newsletter.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add newsletter subscribers",
   *     parameters={
   *         {"name"="email", "dataType"="string", "required"=true, "description"="subscriber email"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         409="Returned when email address already exists.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/newsletter/")
   *
   */
  public function addNewsletterAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'newsletter');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check email in DB
      $checkEmail = $em->getRepository('AcmeDataBundle:NewsletterSubscribers')->findOneByEmail(trim($request->get('email')));
      if($checkEmail)
        return ApiResponse::setResponse('You have already signed up for our emails. Look out for them!', Codes::HTTP_CONFLICT);

      //create newsletter subscription in DB
      $entity = new NewsletterSubscribers();
      $entity->setEmail(trim($request->get('email')));

      $em->persist($entity);
      $em->flush();

      //return response
      return ApiResponse::setResponse('You have successfully signed up for our emails.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add Car Care Club service used to save form data for Car Care Club page.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add/Email Car Care Club form data",
   *     parameters={
   *         {"name"="firstName", "dataType"="string", "required"=true, "description"="first name"},
   *         {"name"="lastName", "dataType"="string", "required"=true, "description"="last name"},
   *         {"name"="email", "dataType"="string", "required"=true, "description"="email address"},
   *         {"name"="verifyEmail", "dataType"="string", "required"=true, "description"="verify email address"},
   *         {"name"="address1", "dataType"="string", "required"=false, "description"="address 1"},
   *         {"name"="address2", "dataType"="string", "required"=false, "description"="address 2"},
   *         {"name"="city", "dataType"="string", "required"=false, "description"="city"},
   *         {"name"="state", "dataType"="string", "required"=false, "description"="state"},
   *         {"name"="zipCode", "dataType"="string", "required"=false, "description"="zip code"},
   *         {"name"="phone", "dataType"="string", "required"=false, "description"="phone number"},
   *         {"name"="meinekeCustomer", "dataType"="integer", "required"=true, "description"="0 | 1"},
   *         {"name"="visitState", "dataType"="string", "required"=true, "description"="visit state Meineke"},
   *         {"name"="vehicles", "dataType"="string", "required"=true, "description"="json with vehicle(s) details"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/carcareclub/")
   *
   */
  public function addCarCareClubAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'carClub');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    if(strcmp(trim($request->get('email')), trim($request->get('verifyEmail'))) !== 0)
      return ApiResponse::setResponse('Email and Verify Email do not match.', Codes::HTTP_CONFLICT);

    if(json_decode(trim($request->get('vehicles'))) === NULL)
      return ApiResponse::setResponse('Invalid vehicle(s) json.', Codes::HTTP_BAD_REQUEST);

    $vehiclesArr = json_decode(trim($request->get('vehicles')), true);
    for($i=0;$i<count($vehiclesArr);$i++) {
      if(!isset($vehiclesArr[$i]['vehicleYear']) || !strlen($vehiclesArr[$i]['vehicleYear']))
        return ApiResponse::setResponse('Vehicle Year ' . $i . ' is required.', Codes::HTTP_BAD_REQUEST);
      if(!isset($vehiclesArr[$i]['vehicleMake']) || !strlen($vehiclesArr[$i]['vehicleMake']))
        return ApiResponse::setResponse('Vehicle Make ' . $i . ' is required.', Codes::HTTP_BAD_REQUEST);
      if(!isset($vehiclesArr[$i]['vehicleModel']) || !strlen($vehiclesArr[$i]['vehicleModel']))
        return ApiResponse::setResponse('Vehicle Model ' . $i . ' is required.', Codes::HTTP_BAD_REQUEST);
      if(!isset($vehiclesArr[$i]['mileAge']) || !strlen($vehiclesArr[$i]['mileAge']))
        return ApiResponse::setResponse('Mileage ' . $i . ' is required.', Codes::HTTP_BAD_REQUEST);
    }

    try {

      //create car care club form in DB
      $entity = new CarCareClubForm();

      $entity->setFirstName(trim($request->get('firstName')));
      $entity->setLastName(trim($request->get('lastName')));
      $entity->setEmail(trim($request->get('email')));
      if(trim($request->get('address1')))
        $entity->setAddress1(trim($request->get('address1')));
      if(trim($request->get('address2')))
        $entity->setAddress2(trim($request->get('address2')));
      if(trim($request->get('city')))
        $entity->setCity(trim($request->get('city')));
      if(trim($request->get('state')))
        $entity->setState(trim($request->get('state')));
      if(trim($request->get('zipCode')))
        $entity->setZipCode(trim($request->get('zipCode')));
      if(trim($request->get('phone')))
        $entity->setPhone(trim($request->get('phone')));
      $entity->setMeinekeCustomer(trim($request->get('meinekeCustomer')));
      $entity->setStateVisitMeineke(trim($request->get('visitState')));

      $em->persist($entity);
      $em->flush();

      for($i=0;$i<count($vehiclesArr);$i++) {

        $entityVehicle = new CarCareClubVehicles();

        $entityVehicle->setVehicleYear($vehiclesArr[$i]['vehicleYear']);
        $entityVehicle->setVehicleMake($vehiclesArr[$i]['vehicleMake']);
        $entityVehicle->setVehicleModel($vehiclesArr[$i]['vehicleModel']);
        $entityVehicle->setVehicleMileage($vehiclesArr[$i]['mileAge']);

        $entityVehicle->setCarCareClubForm($entity);

        $em->persist($entityVehicle);
        $em->flush();
      }

      //send email with form data
      $this->get('emailNotificationBundle.email')->sendCarCareClubEmail($entity, $vehiclesArr);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add Real Estate service used to save form data for Real Estate page.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add/Email Real Estate form data",
   *     parameters={
   *         {"name"="address", "dataType"="string", "required"=true, "description"="location address"},
   *         {"name"="city", "dataType"="string", "required"=true, "description"="location city"},
   *         {"name"="state", "dataType"="string", "required"=true, "description"="location state"},
   *         {"name"="country", "dataType"="string", "required"=true, "description"="location country"},
   *         {"name"="dateMonth", "dataType"="string", "required"=false, "description"="date available month"},
   *         {"name"="dateDay", "dataType"="string", "required"=false, "description"="date available day"},
   *         {"name"="dateYear", "dataType"="string", "required"=false, "description"="date available year"},
   *         {"name"="dealType", "dataType"="string", "required"=false, "description"="deal type"},
   *         {"name"="buildingSize", "dataType"="string", "required"=false, "description"="building size (sq. ft.)"},
   *         {"name"="buildingDepth", "dataType"="string", "required"=false, "description"="building depth (in ft.)"},
   *         {"name"="salePrice", "dataType"="string", "required"=false, "description"="sale price"},
   *         {"name"="landSizeSqFt", "dataType"="string", "required"=false, "description"="land size (sq. ft.)"},
   *         {"name"="zonedAuto", "dataType"="string", "required"=true, "description"="zoned for auto"},
   *         {"name"="buildingLength", "dataType"="string", "required"=false, "description"="building length (in ft.)"},
   *         {"name"="landSize", "dataType"="string", "required"=false, "description"="land size (in ft.)"},
   *         {"name"="leaseRate", "dataType"="string", "required"=false, "description"="lease rate"},
   *         {"name"="propertyTaxes", "dataType"="string", "required"=false, "description"="property taxes"},
   *         {"name"="contactFirstName", "dataType"="string", "required"=true, "description"="contact first name"},
   *         {"name"="contactLastName", "dataType"="string", "required"=true, "description"="contact last name"},
   *         {"name"="contactEmail", "dataType"="string", "required"=true, "description"="contact email"},
   *         {"name"="contactPhone", "dataType"="string", "required"=false, "description"="contact phone"},
   *         {"name"="contactAddress", "dataType"="string", "required"=false, "description"="contact address"},
   *         {"name"="comments", "dataType"="string", "required"=false, "description"="comments"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/realestate/")
   *
   */
  public function addRealEstateAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'realEstate');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //create real estate in DB
      $entity = new RealEstateForm();

      $entity->setAddress(trim($request->get('address')));
      $entity->setCity(trim($request->get('city')));
      $entity->setState(trim($request->get('state')));
      $entity->setCountry(trim($request->get('country')));

      if(trim($request->get('dateMonth')) && trim($request->get('dateDay')) && trim($request->get('dateYear'))) {
        $entity->setDateAvailable(new \DateTime(trim($request->get('dateYear')) . "-" . trim($request->get('dateMonth')) . "-" . trim($request->get('dateDay'))));
      }

      if(trim($request->get('dealType')))
        $entity->setDealType(trim($request->get('dealType')));
      if(trim($request->get('buildingSize')))
        $entity->setBuildingSize(trim($request->get('buildingSize')));
      if(trim($request->get('buildingDepth')))
        $entity->setBuildingDepth(trim($request->get('buildingDepth')));
      if(trim($request->get('salePrice')))
        $entity->setSalePrice(trim($request->get('salePrice')));
      if(trim($request->get('landSizeSqFt')))
        $entity->setLandSizeSqFt(trim($request->get('landSizeSqFt')));

      $entity->setZonedAuto(trim($request->get('zonedAuto')));

      if(trim($request->get('buildingLength')))
        $entity->setBuildingLength(trim($request->get('buildingLength')));
      if(trim($request->get('landSize')))
        $entity->setLandSize(trim($request->get('landSize')));
      if(trim($request->get('leaseRate')))
        $entity->setLeaseRate(trim($request->get('leaseRate')));
      if(trim($request->get('propertyTaxes')))
        $entity->setPropertyTaxes(trim($request->get('propertyTaxes')));

      $entity->setContactFirstName(trim($request->get('contactFirstName')));
      $entity->setContactLastName(trim($request->get('contactLastName')));
      $entity->setContactEmail(trim($request->get('contactEmail')));

      if(trim($request->get('contactAddress')))
        $entity->setContactAddress(trim($request->get('contactAddress')));
      if(trim($request->get('contactPhone')))
        $entity->setContactPhone(trim($request->get('contactPhone')));
      if(trim($request->get('comments')))
        $entity->setComments(trim($request->get('comments')));

      $em->persist($entity);
      $em->flush();

      //send email with form data
      $this->get('emailNotificationBundle.email')->sendRealEstateEmail($entity);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Send PDF with coupons service used to send email with link to PDF with coupons.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Send email/sms with PDF with coupons",
   *     parameters={
   *         {"name"="document", "dataType"="string", "required"=true, "description"="document name"},
   *         {"name"="email", "dataType"="string", "required"=false, "description"="email address"},
   *         {"name"="mobile", "dataType"="string", "required"=false, "description"="mobile phone number"},
   *         {"name"="carrier", "dataType"="string", "required"=false, "description"="carrier"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/sendcouponspdf/")
   *
   */
  public function sendEmailCouponsPDFAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'sendPDF');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //params
      $params = array(
        'document' => trim($request->get('document')),
        'email' => trim($request->get('email')),
        'mobile' => trim($request->get('mobile')),
        'carrier' => trim($request->get('carrier'))
      );

      //send email/sms with pdf link
      $this->get('emailNotificationBundle.email')->sendCouponsPDF($params);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Send reward service used to send email with image reward.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Send email with image reward",
   *     parameters={
   *         {"name"="id", "dataType"="string", "required"=true, "description"="reward id"},
   *         {"name"="email", "dataType"="string", "required"=false, "description"="email address"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when reward is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/sendrewardemail/")
   *
   */
  public function sendEmailRewardAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'sendReward');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      $reward = $em->getRepository('AcmeDataBundle:Rewards')->findOneById($id);
      if(!$reward)
        return ApiResponse::setResponse('Reward not found.', Codes::HTTP_NOT_FOUND);

      //params
      $params = array(
        'image' => $reward->getImage(),
        'email' => trim($request->get('email'))
      );

      //send email/sms with pdf link
      $this->get('emailNotificationBundle.email')->sendReward($params);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add Pipeline service used to save subscribers email addresses for coming soon stores.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add pipeline stores subscribers",
   *     parameters={
   *         {"name"="email", "dataType"="string", "required"=true, "description"="subscriber email"},
   *         {"name"="storeId", "dataType"="string", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when store is not found.",
   *         409="Returned when email address already exists.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/pipeline/")
   *
   */
  public function addPipelineAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'pipeline');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check store in DB
      $checkStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      if(!$checkStore)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      if($checkStore->getLocationStatus() != StoresStatus::PIPELINE)
        return ApiResponse::setResponse('Store is invalid.', Codes::HTTP_BAD_REQUEST);

      //check email in DB
      $checkEmail = $em->getRepository('AcmeDataBundle:PipelineSubscribers')->findOneBy(array('email' => trim($request->get('email')), 'stores' => $checkStore));
      if($checkEmail)
        return ApiResponse::setResponse('You are already registered.', Codes::HTTP_CONFLICT);

      //create newsletter subscription in DB
      $entity = new PipelineSubscribers();
      $entity->setEmail(trim($request->get('email')));
      $entity->setStores($checkStore);

      $em->persist($entity);
      $em->flush();

      //return response
      return ApiResponse::setResponse('Email address successfully registered. You will receive a message when the shop will open.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add Feedback service used to save form data for feedback page.
   *
   * @ApiDoc(
   *     section="General Forms",
   *     resource=true,
   *     description="Add/Email Feedback form data",
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"},
   *         {"name"="email", "dataType"="string", "required"=false, "description"="contact email"},
   *         {"name"="firstName", "dataType"="string", "required"=false, "description"="contact first name"},
   *         {"name"="lastName", "dataType"="string", "required"=false, "description"="contact last name"},
   *         {"name"="phone", "dataType"="string", "required"=false, "description"="contact phone"},
   *         {"name"="rating", "dataType"="integer", "required"=true, "description"="rating (from 1 to 10)"},
   *         {"name"="feedbackText", "dataType"="string", "required"=false, "description"="feedback comments"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/feedback/")
   *
   */
  public function addFeedbackAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'feedback');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));

      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //create feedback in DB
      $entity = new Feedback();

      $entity->setStores($store);
      if(trim($request->get('email')))
        $entity->setEmail(trim($request->get('email')));
      if(trim($request->get('firstName')))
        $entity->setFirstName(trim($request->get('firstName')));
      if(trim($request->get('lastName')))
        $entity->setLastName(trim($request->get('lastName')));
      if(trim($request->get('phone')))
        $entity->setPhone(trim($request->get('phone')));
      $entity->setRating(trim($request->get('rating')));
      if(trim($request->get('feedbackText')))
        $entity->setFeedbackText(trim($request->get('feedbackText')));

      $em->persist($entity);
      $em->flush();

      //if rating is negative send email with form data
      if(trim($request->get('rating')) <= 4)
        $this->get('emailNotificationBundle.email')->sendFeedbackEmail($entity);

      //return response
      return ApiResponse::setResponse('Thank you. Your message was sent successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
