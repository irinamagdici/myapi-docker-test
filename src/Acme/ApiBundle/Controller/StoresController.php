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
use Acme\DataBundle\Entity\StoresPhotos;
use Acme\DataBundle\Entity\UsCities;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Constants\StoresStatus;
use Acme\DataBundle\Model\Constants\CouponsCategory;
use Acme\DataBundle\Model\Utility\StringUtility;
use Acme\DataBundle\Model\Utility\EntitiesUtility;
use Acme\DataBundle\Model\Utility\SearchUtility;
use Acme\DataBundle\Model\Utility\FullSlate;


class StoresController extends ApiController implements ClassResourceInterface {

  /**
   * Get stores service used to get a list of all stores.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get stores",
   *     filters={
   *         {"name"="isFeatured", "dataType"="integer"},
   *         {"name"="isPipeline", "dataType"="integer"},
   *         {"name"="state", "dataType"="string"},
   *         {"name"="page", "dataType"="integer", "default"="1"},
   *         {"name"="noRecords", "dataType"="integer", "default"="10"},
   *         {"name"="sortField", "dataType"="string", "pattern"="id|storeId", "default"="id"},
   *         {"name"="sortType", "dataType"="string", "pattern"="ASC|DESC", "default"="ASC"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores/")
   *
   */
  public function getStoresAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //set pagination and sorting
      $this->setListingConfigurations($request, $page, $noRecords, $sortField, $sortType);

      $cacheKey = 'stores' . $page . $noRecords . $sortField . $sortType . trim($request->get('state')) . '-featured-' . trim($request->get('isFeatured')) . '-pipeline-' . trim($request->get('isPipeline'));
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get stores
      $noTotal = $em->getRepository('AcmeDataBundle:Stores')->getStoresCount(StoresStatus::CLOSED, trim($request->get('state')), trim($request->get('isFeatured')), trim($request->get('isPipeline')));
      $entities = $em->getRepository('AcmeDataBundle:Stores')->getStores($page, $noRecords, $sortField, $sortType, StoresStatus::CLOSED, trim($request->get('state')), trim($request->get('isFeatured')), trim($request->get('isPipeline')));

      //parse data
      for($i=0;$i<count($entities);$i++) {
        foreach($entities[$i] as $key => $value) {
          $entities[$i][$key] = $value ? $value : '';
        }

        //get store photos
        $photos = $em->getRepository('AcmeDataBundle:StoresPhotos')->getStorePhotos($entities[$i]['id']);
        $images = array();
        for($j=0;$j<count($photos);$j++) {
          $images[] = 'http://d2pg00xycmaa7f.cloudfront.net/wp-content/plugins/wpallimport/upload/images/' . $entities[$i]['storeId'] . '/' . $photos[$j]['name']; //TODO
        }
        $entities[$i]['photos'] = $images;

        //city slug
        $entities[$i]['locationCitySlug'] = StringUtility::generateSlug($entities[$i]['locationCity']);
      }
      $finalData = array('stores' => $entities, 'noTotal' => $noTotal);

      //save to cache
      $cache->save($cacheKey, $finalData);

      //return response
      return ApiResponse::setResponse($finalData);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Stores search service used to get a list of all stores based on geolocation.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Search stores",
   *     parameters={
   *         {"name"="latitude", "dataType"="string", "required"=false, "description"="latitude coordinate"},
   *         {"name"="longitude", "dataType"="string", "required"=false, "description"="longitude coordinate"},
   *         {"name"="state", "dataType"="string", "required"=false, "description"="state - 2 letters code"},
   *         {"name"="citySlug", "dataType"="string", "required"=false, "description"="city slug"},
   *         {"name"="metroSlug", "dataType"="string", "required"=false, "description"="metro slug"},
   *         {"name"="key", "dataType"="string", "required"=false, "description"="key for search (city, state or zip)"},
   *         {"name"="limit", "dataType"="integer", "required"=false, "description"="search results limit"},
   *         {"name"="page", "dataType"="integer", "required"=false, "description"="search results page"},
   *         {"name"="veterans", "dataType"="integer", "required"=false, "description"="veteran store, 0 | 1"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores/search/")
   *
   */
  public function getStoresSearchAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      $latitude = $longitude = $metroId = '';
      if(trim($request->get('latitude')) && trim($request->get('longitude'))) {
        //we have latitude and longitude in request
        $latitude = trim($request->get('latitude'));
        $longitude = trim($request->get('longitude'));
      }
      else if(trim($request->get('state')) && trim($request->get('citySlug'))) {
        //we have state and city slug in request
        $coordinates = $em->getRepository('AcmeDataBundle:UsCities')->findOneBy(array('citySlug' => trim($request->get('citySlug')), 'state' => trim($request->get('state'))));
        if($coordinates) {
          $latitude = $coordinates->getLat();
          $longitude = $coordinates->getLng();
        }
        else {
          //fallback, get from google and save in DB
          $googleSearch = SearchUtility::getFromGoogle(trim($request->get('citySlug')));
          if($googleSearch) {
            $latitude = $googleSearch['latitude'];
            $longitude = $googleSearch['longitude'];

            $str = $latitude . ',' . $longitude;
            $googleZipSearch = SearchUtility::getFromGoogle($str, true);
            if($googleZipSearch) {
              //add to DB
              $usCities = new UsCities();

              $usCities->setZip($googleZipSearch['zipCode']);
              $usCities->setCity($googleZipSearch['city']);
              $usCities->setCitySlug(trim($request->get('citySlug')));
              $usCities->setState(strtoupper(trim($request->get('state'))));
              $usCities->setLat($latitude);
              $usCities->setLng($longitude);

              $em->persist($usCities);
              $em->flush();
            }
          }
        }
      }
      else if(trim($request->get('state')) && trim($request->get('metroSlug'))) {
        //we have state and metro slug in request
        $coordinates = $em->getRepository('AcmeDataBundle:Dma')->findOneBy(array('dmaSlug' => trim($request->get('metroSlug')), 'state' => trim($request->get('state'))));
        if($coordinates) {
          $latitude = $coordinates->getLat();
          $longitude = $coordinates->getLng();
          $metroId = $coordinates->getId();
        }
      }
      else if(trim($request->get('key'))) {
        $zipCode = 0;
        $searchKey = trim($request->get('key'));
        $searchKeyArr = explode(",", $searchKey);
        if(count($searchKeyArr) >= 3) {
          $searchCity = isset($searchKeyArr[0]) ? trim($searchKeyArr[0]) : 0;
          $searchState = isset($searchKeyArr[1]) ? trim($searchKeyArr[1]) : 0;

          //check key state
          $searchStateArr = explode(" ", $searchState);
          $searchState = isset($searchStateArr[0]) ? trim($searchStateArr[0]) : $searchState;
        }
        else {
          if(count($searchKeyArr) == 2) {
            $searchCity = isset($searchKeyArr[0]) ? trim($searchKeyArr[0]) : 0;
            $searchState = isset($searchKeyArr[1]) ? trim($searchKeyArr[1]) : 0;
          }
          else {
            $searchCity = isset($searchKeyArr[0]) ? trim($searchKeyArr[0]) : 0;
            $searchState = 0;
          }
        }

        //check if key is zipcode
        if(is_numeric($searchKey))
          $zipCode = $searchKey;

        $pattern = '/^[0-9]{5}-[0-9]{2,4}$/'; //matches zipcodes like '02312-4345'
        $matches = array();
        preg_match($pattern, $searchKey, $matches);
        if(count($matches) > 0) {
          $zipcodes = explode('-', $searchKey);
          $searchKey = $zipcodes[0];
          $zipCode = $searchKey;
        }

        //get from DB
        $searchResults = 0;
        if($searchCity && !$searchState && !$zipCode) {
          $findProperCityAndState = $em->getRepository('AcmeDataBundle:Stores')->getCityInfo($searchCity);
            if($findProperCityAndState) {
              $searchCity = $findProperCityAndState[0]['locationCity'];
              $searchState = $findProperCityAndState[0]['locationState'];
            }
        }
        $searchResults = $em->getRepository('AcmeDataBundle:Stores')->findInUSCities($searchCity, $searchState, $zipCode);

        if(!$searchResults) {
          if($searchCity && !$searchState && !$zipCode) {
            //maybe is a valid state
            $stateSearch = array_search(ucfirst(strtolower($searchCity)), EntitiesUtility::getAllStates());

            if($stateSearch !== FALSE) {
              //check state
              $state = $em->getRepository('AcmeDataBundle:Stores')->findOneBy(array('locationState' => $stateSearch, 'locationStatus' => StoresStatus::OPEN));
              if($state) {
                $searchResults[0]['lat'] = $state->getLat();
                $searchResults[0]['lng'] = $state->getLng();
              }
            }
          }
        }

        if($searchResults) {
          $latitude = $searchResults[0]['lat'];
          $longitude = $searchResults[0]['lng'];
        }
        else {
          //get from Bing
          $bingSearch = SearchUtility::getFromBing($searchKey, $zipCode);

          if(!empty($bingSearch)) {
            $latitude = $bingSearch['latitude'];
            $longitude = $bingSearch['longitude'];
          }
          else {
            //try again from Bing
            $bingSearch = SearchUtility::getFromBing($searchKey, $zipCode);
            if(!empty($bingSearch)) {
              $latitude = $bingSearch['latitude'];
              $longitude = $bingSearch['longitude'];
            }
            else {
              //get from Google
              $googleSearch = SearchUtility::getFromGoogle($searchKey);
              if($googleSearch) {
                $latitude = $googleSearch['latitude'];
                $longitude = $googleSearch['longitude'];
              }
            }
          }
        }

      }
      else {
        return ApiResponse::setResponse('Invalid parameters', Codes::HTTP_BAD_REQUEST);
      }

      if(!strlen($latitude) || !strlen($longitude)) {
        //return response
        $entities = array();
        return ApiResponse::setResponse($entities);
      }

      $limit = trim($request->get('limit')) ? trim($request->get('limit')) : 1000;

      //intiate cache
      $veterans = trim($request->get('veterans')) ? trim($request->get('veterans')) : 0;
      if(!$veterans) {
        $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

        $cacheKey = 'stores-search' . $latitude . $longitude . $limit;
        if($cache->contains($cacheKey)) {

          $allResults = $cache->fetch($cacheKey);
          if(trim($request->get('page'))) {
            $returnResults = array_slice($allResults, trim($request->get('page')) * 5, 5);
          }
          else {
            $returnResults = $allResults;
          }

          return ApiResponse::setResponse($returnResults);
        }
      }

      //search stores
      $entities = $em->getRepository('AcmeDataBundle:Stores')->searchStores($latitude, $longitude, $limit, $metroId, $veterans);

      //parse data
      for($i=0;$i<count($entities);$i++) {

        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneById($entities[$i]['id']);

        foreach($entities[$i] as $key => $value) {
          $entities[$i][$key] = $value ? $value : '';
        }
        $entities[$i]['locationCitySlug'] = StringUtility::generateSlug($entities[$i]['locationCity']);
        $entities[$i]['distance'] = round($entities[$i]['distance'], 2);
        $entities[$i]['openStatus'] = EntitiesUtility::getStoreStatus($store);

        unset($entities[$i]['id']);
      }

      //save to cache
      if(!$veterans) {
        $cache->save($cacheKey, $entities);
      }

      //return response
      if(trim($request->get('page'))) {
        $returnResults = array_slice($entities, trim($request->get('page')) * 5, 5);
      }
      else {
        $returnResults = $entities;
      }

      return ApiResponse::setResponse($returnResults);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get store service used to get store details.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get store details",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/store/{id}/")
   *
   */
  public function getStoreAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'stores' . $id;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Stores')->getStore($id);
      //store not found
      if(!$entity)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //entity store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneById($entity[0]['id']);

      //format data
      foreach($entity[0] as $key => $value) {
        $entity[0][$key] = $value ? $value : '';
      }
      $entity[0]['openDate'] = $entity[0]['openDate'] ? $entity[0]['openDate']->format('m/d/Y') : '';
      $entity[0]['locationCitySlug'] = StringUtility::generateSlug($entity[0]['locationCity']);
      $entity[0]['longitude'] = $entity[0]['lng'];
      $entity[0]['latitude'] = $entity[0]['lat'];
      $entity[0]['openStatus'] = EntitiesUtility::getStoreStatus($store);

      //get store photos
      $photos = $em->getRepository('AcmeDataBundle:StoresPhotos')->getStorePhotos($entity[0]['id']);
      $images = array();
      for($i=0;$i<count($photos);$i++) {
        $images[] = 'http://d2pg00xycmaa7f.cloudfront.net/wp-content/plugins/wpallimport/upload/images/' . $id . '/' . $photos[$i]['name']; //TODO
      }
      $entity[0]['photos'] = $images;

      //get store services
      $featuredServices = $additionalServices = $amenities = array();
      $heating = false;
      $services = $em->getRepository('AcmeDataBundle:StoresHasServices')->getStoreServices($entity[0]['id']);

      if($services) {
        for($i=0;$i<count($services);$i++) {

          if($services[$i]->getTitle() === 'HeatingSystemRepair')
            $heating = true;

          if(!$services[$i]->getParent()) {

            if($services[$i]->getIsPrimary()) {
              $featuredServices[$i]['title'] = $services[$i]->getTitle();
              $featuredServices[$i]['slug'] = $services[$i]->getSlug();
              $featuredServices[$i]['headerText'] = $services[$i]->getHeaderText();
              $featuredServices[$i]['shortDescription'] = $services[$i]->getShortDescription();
              $featuredServices[$i]['longDescription'] = $services[$i]->getLongFZDescription();
              $featuredServices[$i]['bottomText'] = $services[$i]->getBottomText();
              $featuredServices[$i]['icon'] = $services[$i]->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getIcon() : '';
              $featuredServices[$i]['orderIdx'] = $services[$i]->getOrderIdx();
            }

            if($services[$i]->getIsAdditional()) {
              $additionalServices[$i]['title'] = $services[$i]->getTitle();
              $additionalServices[$i]['slug'] = $services[$i]->getSlug();
              $additionalServices[$i]['headerText'] = $services[$i]->getHeaderText();
              $additionalServices[$i]['shortDescription'] = $services[$i]->getShortDescription();
              $additionalServices[$i]['longDescription'] = $services[$i]->getLongFZDescription();
              $additionalServices[$i]['bottomText'] = $services[$i]->getBottomText();
              $additionalServices[$i]['icon'] = $services[$i]->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getIcon() : '';
              $additionalServices[$i]['orderIdx'] = $services[$i]->getOrderIdx();
            }

            if($services[$i]->getIsAmenity()) {
              $amenities[$i]['title'] = $services[$i]->getTitle();
              $amenities[$i]['shortDescription'] = $services[$i]->getShortDescription();
              $amenities[$i]['longDescription'] = $services[$i]->getLongFZDescription();
              $amenities[$i]['icon'] = $services[$i]->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getIcon() : '';
              $amenities[$i]['orderIdx'] = $services[$i]->getOrderIdx();
            }

          }
          else {

            if($services[$i]->getParent()->getIsPrimary()) {
              if($services[$i]->getParent()->getParent()) {
                $featuredServices[$i]['title'] = $services[$i]->getParent()->getParent()->getTitle();
                $featuredServices[$i]['slug'] = $services[$i]->getParent()->getParent()->getSlug();
                $featuredServices[$i]['headerText'] = $services[$i]->getParent()->getParent()->getHeaderText();
                $featuredServices[$i]['shortDescription'] = $services[$i]->getParent()->getParent()->getShortDescription();
                $featuredServices[$i]['longDescription'] = $services[$i]->getParent()->getParent()->getLongFZDescription();
                $featuredServices[$i]['bottomText'] = $services[$i]->getParent()->getParent()->getBottomText();
                $featuredServices[$i]['icon'] = $services[$i]->getParent()->getParent()->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getParent()->getParent()->getIcon() : '';
                $featuredServices[$i]['orderIdx'] = $services[$i]->getParent()->getParent()->getOrderIdx();

                $featuredServices[$i]['subservices'][] = $services[$i]->getParent()->getTitle();
              }
              else {
                $featuredServices[$i]['title'] = $services[$i]->getParent()->getTitle();
                $featuredServices[$i]['slug'] = $services[$i]->getParent()->getSlug();
                $featuredServices[$i]['headerText'] = $services[$i]->getParent()->getHeaderText();
                $featuredServices[$i]['shortDescription'] = $services[$i]->getParent()->getShortDescription();
                $featuredServices[$i]['longDescription'] = $services[$i]->getParent()->getLongFZDescription();
                $featuredServices[$i]['bottomText'] = $services[$i]->getParent()->getBottomText();
                $featuredServices[$i]['icon'] = $services[$i]->getParent()->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getParent()->getIcon() : '';
                $featuredServices[$i]['orderIdx'] = $services[$i]->getParent()->getOrderIdx();
              }
            }

            if($services[$i]->getParent()->getIsAdditional()) {
              if($services[$i]->getParent()->getParent()) {
                $additionalServices[$i]['title'] = $services[$i]->getParent()->getParent()->getTitle();
                $additionalServices[$i]['slug'] = $services[$i]->getParent()->getParent()->getSlug();
                $additionalServices[$i]['headerText'] = $services[$i]->getParent()->getParent()->getHeaderText();
                $additionalServices[$i]['shortDescription'] = $services[$i]->getParent()->getParent()->getShortDescription();
                $additionalServices[$i]['longDescription'] = $services[$i]->getParent()->getParent()->getLongFZDescription();
                $additionalServices[$i]['bottomText'] = $services[$i]->getParent()->getParent()->getBottomText();
                $additionalServices[$i]['icon'] = $services[$i]->getParent()->getParent()->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getParent()->getParent()->getIcon() : '';
                $additionalServices[$i]['orderIdx'] = $services[$i]->getParent()->getParent()->getOrderIdx();

                $additionalServices[$i]['subservices'][] = $services[$i]->getParent()->getTitle();
              }
              else {
                $additionalServices[$i]['title'] = $services[$i]->getParent()->getTitle();
                $additionalServices[$i]['slug'] = $services[$i]->getParent()->getSlug();
                $additionalServices[$i]['headerText'] = $services[$i]->getParent()->getHeaderText();
                $additionalServices[$i]['shortDescription'] = $services[$i]->getParent()->getShortDescription();
                $additionalServices[$i]['longDescription'] = $services[$i]->getParent()->getLongFZDescription();
                $additionalServices[$i]['bottomText'] = $services[$i]->getParent()->getBottomText();
                $additionalServices[$i]['icon'] = $services[$i]->getParent()->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getParent()->getIcon() : '';
                $additionalServices[$i]['orderIdx'] = $services[$i]->getParent()->getOrderIdx();
              }
            }

            if($services[$i]->getParent()->getIsAmenity()) {
              $amenities[$i]['title'] = $services[$i]->getParent()->getTitle();
              $amenities[$i]['shortDescription'] = $services[$i]->getParent()->getShortDescription();
              $amenities[$i]['longDescription'] = $services[$i]->getParent()->getLongFZDescription();
              $amenities[$i]['icon'] = $services[$i]->getParent()->getIcon() ? $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $services[$i]->getParent()->getIcon() : '';
              $amenities[$i]['orderIdx'] = $services[$i]->getParent()->getOrderIdx();
            }

          }

        }
      }

      //logic for unique services and subservices
      $subservices = array();
      $featuredServices = array_values(array_intersect_key($featuredServices, array_unique(array_map('serialize', $featuredServices))));
      $additionalServices = array_values(array_intersect_key($additionalServices, array_unique(array_map('serialize', $additionalServices))));
      $amenities = array_values(array_intersect_key($amenities, array_unique(array_map('serialize', $amenities))));

      for($i=0;$i<count($featuredServices);$i++) {
        if(isset($featuredServices[$i]['subservices'])) {
          $subservices[$featuredServices[$i]['title']][] = $featuredServices[$i]['subservices'][0];
        }
      }
      for($i=0;$i<count($additionalServices);$i++) {
        if(isset($additionalServices[$i]['subservices'])) {
          $subservices[$additionalServices[$i]['title']][] = $additionalServices[$i]['subservices'][0];
        }
      }

      foreach($subservices as $key => $value) {
        for($i=0;$i<count($featuredServices);$i++) {
          if($featuredServices[$i]['title'] === $key) {
            $featuredServices[$i]['subservices'] = $value;
          }
        }
        for($i=0;$i<count($additionalServices);$i++) {
          if($additionalServices[$i]['title'] === $key) {
            $additionalServices[$i]['subservices'] = $value;
          }
        }
      }
      $featuredServices = array_values(array_intersect_key($featuredServices, array_unique(array_map('serialize', $featuredServices))));
      $additionalServices = array_values(array_intersect_key($additionalServices, array_unique(array_map('serialize', $additionalServices))));

      //reorder
      $featuredServices = StringUtility::arrayOrderBy($featuredServices, 'orderIdx', SORT_ASC);
      $additionalServices = StringUtility::arrayOrderBy($additionalServices, 'orderIdx', SORT_ASC);

      //cut featuredServices to 5 and insert into additionalServices
      $removedFeaturedServices = array_splice($featuredServices, 5);
      if(!empty($removedFeaturedServices)) {
        for($i=count($removedFeaturedServices)-1;$i>-1;$i--) {
          array_unshift($additionalServices, $removedFeaturedServices[$i]);
        }
      }

      //if heating replace name of service with Heating & A/C
      if($heating) {
        for($i=0;$i<count($featuredServices);$i++) {
          if($featuredServices[$i]['title'] === 'A/C')
            $featuredServices[$i]['title'] = 'Heating & A/C';
        }
      }

      //reorder additional services
      $additionalServicesWithoutSubservices = $additionalServicesWithSubservices = array();
      for($i=0;$i<count($additionalServices);$i++) {
        if(!isset($additionalServices[$i]['subservices']))
          $additionalServicesWithoutSubservices[$i] = $additionalServices[$i];
        else
          $additionalServicesWithSubservices[$i] = $additionalServices[$i];
      }
      $newAdditionalServices = array_merge($additionalServicesWithoutSubservices, $additionalServicesWithSubservices);

      $entity[0]['featuredServices'] = $featuredServices;
      $entity[0]['additionalServices'] = $newAdditionalServices;
      $entity[0]['amenities'] = $amenities;

      //unset unused data
      unset($entity[0]['id'], $entity[0]['dateCreated'], $entity[0]['dateUpdated'], $entity[0]['timezone']);

      //save to cache
      $cache->save($cacheKey, $entity);

      //return response
      return ApiResponse::setResponse($entity);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add stores service used to add/update stores.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Add/update stores",
   *     parameters={
   *         {"name"="serializedData", "dataType"="string", "required"=true, "description"="all stores data serialized"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/stores/")
   *
   */
  public function addStoresAction(Request $request) {

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

      $total = count($finalData);

      for($i=0;$i<$total;$i++) {

        $file = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . 'cron' . date("Y-m-d") . '.txt';
        file_put_contents($file, 'Current: ' . $i . ' - ' . $finalData[$i]['shopnumber'] . ' - ' . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);

        if(strtoupper($finalData[$i]['statusflag']) !== StoresStatus::CLOSED) {

          //check if we have store id in database
          $checkStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);
          $newStore = 0;
          if($checkStore) {
            $entity = $checkStore;

            $locationStatus = $checkStore->getLocationStatus();
            if($locationStatus == StoresStatus::PIPELINE && strtoupper($finalData[$i]['statusflag']) == StoresStatus::OPEN) {
              //send email for subscribers
              $subscribers = $em->getRepository('AcmeDataBundle:PipelineSubscribers')->findByStores($checkStore);
              if($subscribers) {
                for($j=0;$j<count($subscribers);$j++) {
                  $this->get('emailNotificationBundle.email')->sendPipeline($subscribers[$j]->getEmail(), $checkStore);
                }
              }
            }

          }
          else {
            $entity = new Stores();
            $newStore = 1;
            //Full Slate parameters
            $hasFullSlate = 1;
            $timezone = NULL;
          }

          //add or update data
          $entity->setStoreId($finalData[$i]['shopnumber']);
          $entity->setStreetAddress1($finalData[$i]['streetaddress1']);
          $entity->setStreetAddress2($finalData[$i]['streetaddress2']);
          $entity->setLocationCity(ucwords(strtolower($finalData[$i]['locationcity'])));
          $entity->setLocationState($finalData[$i]['locationstate']);
          $entity->setLocationPostalCode($finalData[$i]['locationpostalcode']);
          $entity->setLocationRegion($finalData[$i]['locationregion']);
          $entity->setLocationEmail($finalData[$i]['locationemail']);
          $entity->setLocationStatus(strtoupper($finalData[$i]['statusflag']));
          $entity->setPhone($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone']) : NULL);
          $entity->setRawPhone($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone'], true) : NULL);
          $entity->setSemCamPhone($finalData[$i]['sem-cam'] ? StringUtility::formatPhoneNumber($finalData[$i]['sem-cam']) : NULL);
          $entity->setRawSemCamPhone($finalData[$i]['sem-cam'] ? StringUtility::formatPhoneNumber($finalData[$i]['sem-cam'], true) : NULL);
          $entity->setLng($finalData[$i]['longitude']);
          $entity->setLat($finalData[$i]['latitude']);
          $entity->setPrimaryContact(ucwords(strtolower($finalData[$i]['centerprimarycontact'])));
          $entity->setHoursWeekdayOpen($finalData[$i]['hoursweekdayopen']);
          $entity->setHoursWeekdayClose($finalData[$i]['hoursweekdayclose']);
          $entity->setHoursSaturdayOpen($finalData[$i]['hourssaturdayopen']);
          $entity->setHoursSaturdayClose($finalData[$i]['hourssaturdayclose']);
          $entity->setHoursSundayOpen($finalData[$i]['hourssundayopen']);
          $entity->setHoursSundayClose($finalData[$i]['hourssundayclose']);
          $entity->setLocationDirections($finalData[$i]['locationdirections']);
          $entity->setStarRating($finalData[$i]['starrating'] ? $finalData[$i]['starrating'] : NULL);
          $entity->setOpenDate($finalData[$i]['opendate'] ? new \DateTime(date("Y-m-d", strtotime($finalData[$i]['opendate']))) : NULL);
          $entity->setAmericanExpress($finalData[$i]['americanexpress']);
          $entity->setVisa($finalData[$i]['visa']);
          $entity->setAseSymbol($finalData[$i]['asesymbol']);
          $entity->setDinersClub($finalData[$i]['dinersclub']);
          $entity->setDiscover($finalData[$i]['discover']);
          $entity->setMastercard($finalData[$i]['mastercard']);
          $entity->setMeinekeCreditCard($finalData[$i]['meinekecreditcard']);
          $entity->setMilitaryDiscount($finalData[$i]['militarydiscount']);
          $entity->setSeniorDiscount($finalData[$i]['seniordiscount']);
          if($newStore) {
            $entity->setTimezone($timezone);
            $entity->setHasFullSlate($hasFullSlate);
          }

          $em->persist($entity);
          $em->flush();

          if($newStore) {
            //add store services
            $services = EntitiesUtility::getCSVServices();
            for($j=0;$j<count($services);$j++) {
              $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($services[$j]);

              if($checkService) {

                $checkStoreService = $em->getRepository('AcmeDataBundle:StoresHasServices')->findOneBy(array('stores' => $entity, 'services' => $checkService));

                if($finalData[$i][$services[$j]]) {
                  if(!$checkStoreService) {
                    //add to DB
                    $entitySHS = new StoresHasServices();
                    $entitySHS->setStores($entity);
                    $entitySHS->setServices($checkService);

                    $em->persist($entitySHS);
                    $em->flush();
                  }
                }
                else {
                  if($checkStoreService) {
                    //remove from DB
                    $em->remove($checkStoreService);
                    $em->flush();
                  }
                }
              }
            }
          }

        }
        else {
          //check if we have store id in database
          $entity = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);
          if($entity) {
            //set status closed
            $entity->setLocationStatus(strtoupper($finalData[$i]['statusflag']));
            $em->flush();
          }
        }
      }

      return ApiResponse::setResponse('Stores data successfully added/updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add stores photos service used to add/update stores photos.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Add/update stores photos",
   *     parameters={
   *         {"name"="serializedData", "dataType"="string", "required"=true, "description"="all stores photos data serialized"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/stores/photos/")
   *
   */
  public function addStoresPhotosAction(Request $request) {

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

      foreach($finalData as $key => $value) {
        //get store
        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($key);
        if($store) {
          for($i=0;$i<count($value);$i++) {
            //check if photo exists
            $checkPhoto = $em->getRepository('AcmeDataBundle:StoresPhotos')->findOneBy(array('name' => $value[$i], 'stores' => $store));
            if(!$checkPhoto) {
              //add to DB
              $entity = new StoresPhotos();

              $entity->setName($value[$i]);
              $entity->setOrderIdx(StringUtility::getPhotoIndex($value[$i]));
              $entity->setStores($store);

              $em->persist($entity);
              $em->flush();

              //TODO upload to S3
            }
          }
        }
      }

      return ApiResponse::setResponse('Stores photos successfully added/updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add social links service used to add/update stores social links.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Add/update stores social links",
   *     parameters={
   *         {"name"="serializedData", "dataType"="string", "required"=true, "description"="all stores social links data serialized"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/stores/social/")
   *
   */
  public function addStoresSocialLinksAction(Request $request) {

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
        //check if we have store id in database
        $checkStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);

        if($checkStore) {
          //update data
          $checkStore->setFacebookUrl($finalData[$i]['facebookurl']);
          $checkStore->setGoogleplusUrl($finalData[$i]['googleplusurl']);
          $checkStore->setYelpUrl($finalData[$i]['yelpurl']);
          $checkStore->setFoursquareUrl($finalData[$i]['foursquareurl']);

          $em->flush();
        }
      }

      return ApiResponse::setResponse('Stores social links successfully updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Stores states used to get a list of all Meineke location states.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="List states",
   *     filters={
   *         {"name"="isFeatured", "dataType"="integer"},
   *         {"name"="isPipeline", "dataType"="integer"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores/states/")
   *
   */
  public function getStoresStatesAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'stores-states-featured' . trim($request->get('isFeatured')) . '-pipeline-' . trim($request->get('isPipeline'));
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get states
      $entities = $em->getRepository('AcmeDataBundle:Stores')->getStates(trim($request->get('isFeatured')), trim($request->get('isPipeline')));

      //parse data
      $states = EntitiesUtility::getAllStates();

      for($i=0;$i<count($entities);$i++) {
        $entities[$i]['stateName'] = $states[$entities[$i]['stateAbbreviation']];
      }

      //save to cache
      $cache->save($cacheKey, $entities);

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Stores cities used to get a list of all Meineke location cities.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="List cities",
   *     parameters={
   *         {"name"="state", "dataType"="string", "required"=true, "description"="state - 2 letters code"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores/cities/")
   *
   */
  public function getStoresCitiesAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'cities');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check state
      $states = EntitiesUtility::getAllStates();
      if(!in_array(trim($request->get('state')), array_keys($states)))
        return ApiResponse::setResponse('Invalid state.', Codes::HTTP_BAD_REQUEST);

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'stores-cities' . trim($request->get('state'));
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get cities and metros
      $entitiesCities = $em->getRepository('AcmeDataBundle:Stores')->getCities(trim($request->get('state')));
      $entitiesMetros = $em->getRepository('AcmeDataBundle:Stores')->getMetros(trim($request->get('state')));

      //parse data
      $cities = $metros = array();
      for($i=0;$i<count($entitiesCities);$i++) {
        $cities[$i]['cityName'] = $entitiesCities[$i]['city'];
        $cities[$i]['citySlug'] = StringUtility::generateSlug($entitiesCities[$i]['city']);
      }
      for($i=0;$i<count($entitiesMetros);$i++) {
        $metros[$i]['metroName'] = $entitiesMetros[$i]['metro'];
        $metros[$i]['metroSlug'] = $entitiesMetros[$i]['metroSlug'];
      }

      $response = array(
        'stateName' => $states[trim($request->get('state'))],
        'cities' => $cities,
        'metros' => $metros
      );

      //save to cache
      $cache->save($cacheKey, $response);

      //return response
      return ApiResponse::setResponse($response);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * FullSlate service id used to open FullSlate popup with a preselected service.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get service for schedule button",
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id"},
   *         {"name"="serviceNames", "dataType"="string", "required"=true, "description"="service names separated by comma - base 64 encoded"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when store/service is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores/scheduledservice/")
   *
   */
  public function getScheduledServiceAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'scheduledService');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      $resultServices = array();

      //check store
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
      //store not found
      if(!$store)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //store does not have full slate
      if(!$store->getHasFullSlate())
        return ApiResponse::setResponse($resultServices);

      //check service
      $serviceNames = trim($request->get('serviceNames'));
      $serviceNamesArr = explode(",", base64_decode($serviceNames));
      for($i=0;$i<count($serviceNamesArr);$i++) {
        if($serviceNamesArr[$i] == 'Heating & A/C')
          $serviceNamesArr[$i] = 'A/C';
      }

      for($i=0;$i<count($serviceNamesArr);$i++) {
        $service = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($serviceNamesArr[$i]);
        //service not found
        if(!$service)
          return ApiResponse::setResponse('Service not found.', Codes::HTTP_NOT_FOUND);
      }

      //get FullSlate services
      $fullSlateServices = FullSlate::getFullSlateServices(trim($request->get('storeId')));
      $FSServices = array();
      if($fullSlateServices) {
        $fullSlateServicesArr = json_decode($fullSlateServices, true);

        for($i=0;$i<count($fullSlateServicesArr);$i++) {
          $FSServices[$i]['id'] = $fullSlateServicesArr[$i]['id'];
          $FSServices[$i]['name'] = strtolower(preg_replace('/\s+/', '', $fullSlateServicesArr[$i]['name']));
        }
      }

      //check by service name
      for($i=0;$i<count($serviceNamesArr);$i++) {
        switch($serviceNamesArr[$i]) {
          case 'Oil Change':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'oilchange', 'id');
          break;
          case 'A/C':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'airconditioning', 'id');
          break;
          case 'Exhaust & Mufflers':
            if(StringUtility::searchInArray($FSServices, 'name', 'exhaust-muffler/pipes', 'id'))
              $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'exhaust-muffler/pipes', 'id');
            else if(StringUtility::searchInArray($FSServices, 'name', 'exhaust-catalyticconverter', 'id'))
              $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'exhaust-catalyticconverter', 'id');
            else $resultServices[] = null;
          break;
          case 'Brakes':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'brakes', 'id');
          break;
          case 'Tires & Wheels':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'tires', 'id');
          break;
          case 'Batteries':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'battery', 'id');
          break;
          case 'Steering & Suspension':
            $resultServices[] = StringUtility::searchInArray($FSServices, 'name', 'shocks/struts', 'id');
          break;
          default:
            $resultServices[] = null;
        }
      }

      //return response
      return ApiResponse::setResponse($resultServices);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get store open status service used to get store open status.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Get store open status",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="store id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/store/openstatus/{id}/")
   *
   */
  public function getStoreOpenStatusAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    try {

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Stores')->getStore($id);
      //store not found
      if(!$entity)
        return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

      //store entity
      $store = $em->getRepository('AcmeDataBundle:Stores')->findOneById($entity[0]['id']);

      $openStatus = EntitiesUtility::getStoreStatus($store);

      //return response
      return ApiResponse::setResponse($openStatus);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Add stores cron service used to add/update stores.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Add/update stores",
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores-cron/")
   *
   */
  public function addStoresCronAction(Request $request) {

    set_time_limit(0);
    $array = array();
    $file = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . 'cron' . date("Y-m-d") . '.txt';

    /*$conn = ssh2_connect('74.84.210.25', 22);
    if($conn === FALSE) {
      file_put_contents($file, 'Connection to FTP failed.' . PHP_EOL, FILE_APPEND);
      exit();
    }
    if(ssh2_auth_password($conn, 'mnk', 'FqeuTL9A') === FALSE) {
      file_put_contents($file, 'Login is invalid.' . PHP_EOL, FILE_APPEND);
      exit();
    }*/

    $localFile = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . 'MeinekeCenterInfo.csv';
    //$serverFile = 'wp-content/plugins/wpallimport/upload/MeinekeCenterInfo.csv';

    //fetch file
    /*if(ssh2_scp_recv($conn, $serverFile, $localFile) === FALSE) {
      file_put_contents($file, 'Download file failed.' . PHP_EOL, FILE_APPEND);
      exit();
    }
    else {*/
      //start import into DB
      ini_set('auto_detect_line_endings', TRUE);

      $array = $fields = array(); $i = 0;
      $handle = @fopen($localFile, "r");
      if($handle) {
        while(($row = fgetcsv($handle, 4096)) !== FALSE) {
          if(empty($fields)) {
            $fields = $row;
            continue;
          }

          foreach($row as $k=>$value) {
            $array[$i][$fields[$k]] = $value;
          }
          $i++;
        }
        if(!feof($handle)) {
          file_put_contents($file, 'Error: unexpected fgets() fail.' . PHP_EOL, FILE_APPEND);
          exit();
        }
        fclose($handle);
      }
    //}

    //get doctrine manager
    $em = $this->getDoctrine()->getManager();

    //convert all keys to lowercase
    $finalData = StringUtility::changeArrayKeyCase($array, CASE_LOWER);

    try {

      $total = count($finalData);
      $openStores = 0;
      $newOpenStores = 0;
      $newClosedStores = 0;
      file_put_contents($file, 'Start importing ' . $total . ' stores...' . PHP_EOL, FILE_APPEND);

      for($i=0;$i<$total;$i++) {

        file_put_contents($file, 'Current: ' . $i . ' - ' . $finalData[$i]['shopnumber'] . ' - ' . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);

        if(strtoupper($finalData[$i]['statusflag']) !== StoresStatus::CLOSED) {

          //check if we have store id in database
          $checkStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);
          $newStore = 0;
          $hasFullSlate = 1;
          if($checkStore) {
            $entity = $checkStore;

            $locationStatus = $checkStore->getLocationStatus();

            if(strtoupper($finalData[$i]['statusflag']) == StoresStatus::OPEN)
              $openStores++;

            if($locationStatus == StoresStatus::PIPELINE && strtoupper($finalData[$i]['statusflag']) == StoresStatus::OPEN) {
              $newOpenStores++;
              //send email for subscribers
              $subscribers = $em->getRepository('AcmeDataBundle:PipelineSubscribers')->findByStores($checkStore);
              if($subscribers) {
                for($j=0;$j<count($subscribers);$j++) {
                  $this->get('emailNotificationBundle.email')->sendPipeline($subscribers[$j]->getEmail(), $checkStore);
                }
              }
            }
          }
          else {
            $entity = new Stores();
            $newStore = 1;
            //Full Slate parameters
            //$hasFullSlate = 1;
            $timezone = NULL;
          }

          //check full slate for all open stores
          if(strtoupper($finalData[$i]['statusflag']) == StoresStatus::OPEN) {
            $checkFullSlate = FullSlate::checkFullSlate($finalData[$i]['shopnumber'], $this->container->parameters['fullslate']['fullslate_url']);

            if(strpos($checkFullSlate, 'There is no scheduling page') !== FALSE || strpos($checkFullSlate, 'This Full Slate site is no longer active') !== FALSE)
              $hasFullSlate = 0;
            else
              $hasFullSlate = 1;
          }

          //add or update data
          $entity->setStoreId($finalData[$i]['shopnumber']);
          $entity->setStreetAddress1($finalData[$i]['streetaddress1']);
          $entity->setStreetAddress2($finalData[$i]['streetaddress2']);
          $entity->setLocationCity(ucwords(strtolower($finalData[$i]['locationcity'])));
          $entity->setLocationState($finalData[$i]['locationstate']);
          $entity->setLocationPostalCode($finalData[$i]['locationpostalcode']);
          $entity->setLocationRegion($finalData[$i]['locationregion']);
          $entity->setLocationEmail($finalData[$i]['locationemail']);
          $entity->setLocationStatus(strtoupper($finalData[$i]['statusflag']));
          $entity->setPhone($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone']) : NULL);
          $entity->setRawPhone($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone'], true) : NULL);
          $entity->setSemCamPhone($finalData[$i]['sem-cam'] ? StringUtility::formatPhoneNumber($finalData[$i]['sem-cam']) : ($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone']) : NULL));
          $entity->setRawSemCamPhone($finalData[$i]['sem-cam'] ? StringUtility::formatPhoneNumber($finalData[$i]['sem-cam'], true) : ($finalData[$i]['phone'] ? StringUtility::formatPhoneNumber($finalData[$i]['phone'], true) : NULL));
          $entity->setLng($finalData[$i]['longitude']);
          $entity->setLat($finalData[$i]['latitude']);
          $entity->setPrimaryContact(ucwords(strtolower($finalData[$i]['centerprimarycontact'])));
          $entity->setHoursWeekdayOpen($finalData[$i]['hoursweekdayopen']);
          $entity->setHoursWeekdayClose($finalData[$i]['hoursweekdayclose']);
          $entity->setHoursSaturdayOpen($finalData[$i]['hourssaturdayopen']);
          $entity->setHoursSaturdayClose($finalData[$i]['hourssaturdayclose']);
          $entity->setHoursSundayOpen($finalData[$i]['hourssundayopen']);
          $entity->setHoursSundayClose($finalData[$i]['hourssundayclose']);
          $entity->setLocationDirections($finalData[$i]['locationdirections']);
          $entity->setStarRating($finalData[$i]['starrating'] ? $finalData[$i]['starrating'] : NULL);
          $entity->setOpenDate($finalData[$i]['opendate'] ? new \DateTime(date("Y-m-d", strtotime($finalData[$i]['opendate']))) : NULL);
          $entity->setAmericanExpress($finalData[$i]['americanexpress']);
          $entity->setVisa($finalData[$i]['visa']);
          $entity->setAseSymbol($finalData[$i]['asesymbol']);
          $entity->setDinersClub($finalData[$i]['dinersclub']);
          $entity->setDiscover($finalData[$i]['discover']);
          $entity->setMastercard($finalData[$i]['mastercard']);
          $entity->setMeinekeCreditCard($finalData[$i]['meinekecreditcard']);
          $entity->setMilitaryDiscount($finalData[$i]['militarydiscount']);
          $entity->setSeniorDiscount($finalData[$i]['seniordiscount']);
          if($newStore) {
            $entity->setTimezone($timezone);
            //$entity->setHasFullSlate($hasFullSlate);
          }
          $entity->setHasFullSlate($hasFullSlate);

          $em->persist($entity);
          $em->flush();

          if(strtoupper($finalData[$i]['statusflag']) == StoresStatus::OPEN) {
            //add store services
            $services = EntitiesUtility::getCSVServices();
            for($j=0;$j<count($services);$j++) {
              $checkService = $em->getRepository('AcmeDataBundle:Services')->findOneByTitle($services[$j]);

              if($checkService) {

                $checkStoreService = $em->getRepository('AcmeDataBundle:StoresHasServices')->findOneBy(array('stores' => $entity, 'services' => $checkService));

                if($finalData[$i][$services[$j]]) {
                  if(!$checkStoreService) {
                    //add to DB
                    $entitySHS = new StoresHasServices();
                    $entitySHS->setStores($entity);
                    $entitySHS->setServices($checkService);

                    $em->persist($entitySHS);
                    $em->flush();
                  }
                }
                else {
                  if($checkStoreService) {
                    //remove from DB
                    $em->remove($checkStoreService);
                    $em->flush();
                  }
                }
              }
            }
          }

        }
        else {
          //check if we have store id in database
          $entity = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);
          if($entity) {
            if($entity->getLocationStatus() == StoresStatus::OPEN) {

              $newClosedStores++;

              //send email to know that the franchise has closed
              $this->get('emailNotificationBundle.email')->sendClosed($entity);
            }

            //set status closed
            $entity->setLocationStatus(strtoupper($finalData[$i]['statusflag']));
            $em->flush();
          }
        }
      }

      file_put_contents($file, $total . ' stores imported.' . PHP_EOL, FILE_APPEND);
      file_put_contents($file, $openStores . ' open stores.' . PHP_EOL, FILE_APPEND);
      file_put_contents($file, $newOpenStores . ' newly open stores.' . PHP_EOL, FILE_APPEND);
      file_put_contents($file, $newClosedStores . ' newly closed stores.' . PHP_EOL, FILE_APPEND);

      //delete redis cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();
      //find keys
      $keys = $cache->find('*stores*');
      //delete cache
      if(!empty($keys)) {
        for($i=0;$i<count($keys);$i++) {
          $cache->delete($keys[$i]);
        }
        file_put_contents($file, 'Redis Cache successfully deleted.' . PHP_EOL, FILE_APPEND);
      }

      //send email with log file
      $this->get('emailNotificationBundle.email')->sendCronStoresLogs($file);

      return ApiResponse::setResponse('Stores data successfully added/updated.');
    }
    catch (\Exception $e) {
      file_put_contents($file, $e->getMessage() . PHP_EOL, FILE_APPEND);

      //send email with log file
      $this->get('emailNotificationBundle.email')->sendCronStoresLogs($file);

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Update stores timezone service used to update stores timezone.
   *
   * @ApiDoc(
   *     section="Stores",
   *     resource=true,
   *     description="Update stores timezone",
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/stores-timezone/")
   *
   */
  public function addStoresTimezoneCronAction(Request $request) {

    set_time_limit(0);

    //get doctrine manager
    $em = $this->getDoctrine()->getManager();
    //get all open stores
    $stores = $em->getRepository('AcmeDataBundle:Stores')->findByLocationStatus(StoresStatus::OPEN);

    if($stores) {
      for($i=0;$i<count($stores);$i++) {
        //timezonedb.com API
        $timezone = json_decode(file_get_contents("http://api.timezonedb.com/?lat=" . $stores[$i]->getLat() . "&lng=" . $stores[$i]->getLng() . "&format=json&key=1LQ8WKJI1U7I"), true);
        if($timezone['status'] == "OK") {
          $stores[$i]->setTimezone($timezone['abbreviation']);

          $em->flush();
        }
      }
    }

    return ApiResponse::setResponse('Stores timezone successfully updated.');

  }

}
