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
use Acme\DataBundle\Model\Constants\CouponsStatus;
use Acme\DataBundle\Model\Constants\CouponsCategory;
use Acme\DataBundle\Model\Utility\StringUtility;
use Acme\DataBundle\Model\Utility\EntitiesUtility;
use Acme\DataBundle\Model\Utility\Barcode39;
use Acme\StorageBundle\Model\S3;


class CouponsController extends ApiController implements ClassResourceInterface {

  /**
   * Get coupons service used to get all coupons.
   *
   * @ApiDoc(
   *     section="Coupons",
   *     resource=true,
   *     description="Get coupons",
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=false, "description"="store id"},
   *         {"name"="metroSlug", "dataType"="integer", "required"=false, "description"="metro slug"},
   *         {"name"="limit", "dataType"="integer", "required"=false, "description"="coupons results limit"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the store/metro not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/coupons/")
   *
   */
  public function getCouponsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      $limit = trim($request->get('limit')) ? trim($request->get('limit')) : 1000;

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'stores-coupons' . trim($request->get('storeId')) . $limit;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //check store and metro
      $store = $metro = '';
      if(trim($request->get('storeId'))) {
        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));

        //store not found
        if(!$store)
          return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

        $optin = $store->getOptin() ? 1 : 0;
      }
      if(trim($request->get('metroSlug'))) {
        $metro = $em->getRepository('AcmeDataBundle:Dma')->findOneByDmaSlug(trim($request->get('metroSlug')));

        //metro not found
        if(!$metro)
          return ApiResponse::setResponse('Metro not found.', Codes::HTTP_NOT_FOUND);

        $optin = 0;
      }

      //get coupons from DB
      $entities = $em->getRepository('AcmeDataBundle:Coupons')->getCoupons($store ? $store->getId() : '', $metro ? $metro->getId() : '', $optin);

      if(trim($request->get('storeId')) && !$optin) {
        //get all default coupons and add them to the coupons result
        $couponsDefault = $em->getRepository('AcmeDataBundle:Coupons')->getCMSAllCoupons(true, '', true);
        if($couponsDefault) {
          for($i=0;$i<count($couponsDefault);$i++) {
            array_push($entities, array(
                'title' => $couponsDefault[$i]['title'],
                'image' => $couponsDefault[$i]['image'],
                'barcode' => $couponsDefault[$i]['barcode'],
                'type' => $couponsDefault[$i]['type'],
                'position' => $couponsDefault[$i]['position']
              ));
          }
        }

        //order coupons by position and type
        $entities = StringUtility::arrayOrderBy($entities, 'type', SORT_ASC, 'position', SORT_ASC);
      }

      //parse data
      $totalNo = count($entities) > $limit ? $limit : count($entities);
      $responseEntities = array();
      for($i=0;$i<$totalNo;$i++) {

        $responseEntities[$i]['title'] = $entities[$i]['title'];
        $responseEntities[$i]['barcode'] = $entities[$i]['barcode'] ? $entities[$i]['barcode'] : '';

        //check if barcode exists
        $barcodeImg = $entities[$i]['barcode'] ? $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/barcode_' . $entities[$i]['barcode'] . '.png' : '';

        //build images urls
        $extension = StringUtility::getFileInfo($entities[$i]['image'], 'extension');
        $fileName = StringUtility::getFileInfo($entities[$i]['image'], 'filename');
        $responseEntities[$i]['imageBarcode'] = $barcodeImg;
        $responseEntities[$i]['imageSmall'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $entities[$i]['image'];
        $responseEntities[$i]['imageMedium'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-med.' . $extension;
        $responseEntities[$i]['imageLarge'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'uploads/images/coupons/' . $fileName . '-lg.' . $extension;

      }

      //save to cache
      $cache->save($cacheKey, $responseEntities);

      //return response
      return ApiResponse::setResponse($responseEntities);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Check coupons service used to check all promo coupons for endDate.
   *
   * @ApiDoc(
   *     section="Coupons",
   *     resource=true,
   *     description="Check coupons for endDate",
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/coupons/checkexpired/")
   *
   */
  public function getCouponsCheckAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //get coupons from DB
      $entities = $em->getRepository('AcmeDataBundle:Coupons')->getPromoExpiredCoupons();

      //set coupons inactive
      if($entities) {
        for($i=0;$i<count($entities);$i++) {
          $entity = $em->getRepository('AcmeDataBundle:Coupons')->findOneById($entities[$i]['id']);

          $entity->setStatus(0);

          $em->flush();
        }

        //clear redis cache for coupons
        $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

        //find keys
        $keys = $cache->find('*coupons*');

        //delete cache
        if(!empty($keys)) {
          for($i=0;$i<count($keys);$i++) {
            $cache->delete($keys[$i]);
          }
        }
      }

      //return response
      return ApiResponse::setResponse('Check completed successfully.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Import coupons service used to import coupons from CSV.
   *
   * @ApiDoc(
   *     section="Coupons",
   *     resource=true,
   *     description="Import coupons CSV",
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/coupons/coupons-cron/")
   *
   */
  public function addCouponsCronAction(Request $request) {

    set_time_limit(0);
    $array = array();
    $file = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . 'cron-coupons' . date("Y-m-d") . '.txt';
    $localFile = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . 'CenterCoupons.csv';

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

    $em = $this->getDoctrine()->getManager();

    //convert all keys to lowercase
    $finalData = StringUtility::changeArrayKeyCase($array, CASE_LOWER);

    try {

      $total = count($finalData);

      file_put_contents($file, 'Start importing ' . $total . ' rows...' . PHP_EOL, FILE_APPEND);

      //get all shops ids
      $shopsIds = array();
      for($i=0;$i<$total;$i++) {
        $shopsIds[] = $finalData[$i]['shopnumber'];
      }
      $shopsIds = array_values(array_unique($shopsIds));

      //get all coupons mapping
      $allMaping = array();
      for($i=0;$i<count($shopsIds);$i++) {
        //get store entity
        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($shopsIds[$i]);

        //get coupons
        $allStoresHasCoupons = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findByStores($store->getId());
        if($allStoresHasCoupons) {
          for($j=0;$j<count($allStoresHasCoupons);$j++) {
            $allMaping[$shopsIds[$i]][] = $allStoresHasCoupons[$j]->getId();
          }
        }
      }

      //check mapping
      $newMaping = array();
      for($i=0;$i<$total;$i++) {

        file_put_contents($file, 'Current: ' . $i . ' - ' . $finalData[$i]['shopnumber'] . ' - ' . $finalData[$i]['imagename'] . ' - ' . date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);

        //get coupon from DB
        $coupons = $em->getRepository('AcmeDataBundle:Coupons')->findBy(array('image' => trim($finalData[$i]['imagename']) . '.png', 'category' => CouponsCategory::STORE), array('id' => 'DESC'));
        if($coupons) {
          $couponId = $coupons[0]->getId();
          //check store
          $store = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId($finalData[$i]['shopnumber']);
          if($store) {
            //set store opt in
            $store->setOptin(1);

            $em->flush();
          }

          //check mapping store <-> coupon
          $coupon_store = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneBy(array('stores' => $store->getId(), 'coupons' => $couponId));
          //map if mapping not already exists
          if(!$coupon_store) {
            $storesHasCoupons = new StoresHasCoupons();
            $storesHasCoupons->setStores($store);
            $storesHasCoupons->setCoupons($coupons[0]);

            $em->persist($storesHasCoupons);
            $em->flush();

            $newMaping[$finalData[$i]['shopnumber']][] = $storesHasCoupons->getId();
          }
          else {
            $newMaping[$finalData[$i]['shopnumber']][] = $coupon_store->getId();
          }
        }

      }

      $toDelete = array();
      foreach($allMaping as $key => $value) {
        $toDelete[$key] = array_diff($allMaping[$key], $newMaping[$key]);
      }

      foreach($toDelete as $key => $value) {
        foreach($value as $k => $v) {
          $entity = $em->getRepository('AcmeDataBundle:StoresHasCoupons')->findOneById($v);
          //delete mapping if coupon is type store
          if($entity->getCoupons()->getCategory() == CouponsCategory::STORE) {
            $em->remove($entity);
            $em->flush();
          }
        }
      }

      //set all other stores to opt-out
      $em->getRepository('AcmeDataBundle:Stores')->setStoresOptOut($shopsIds);

      //delete redis cache for stores and coupons
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();
      //find keys
      $keysStores = $cache->find('*stores*');
      //delete cache
      if(!empty($keysStores)) {
        for($i=0;$i<count($keysStores);$i++) {
          $cache->delete($keysStores[$i]);
        }

        file_put_contents($file, 'Redis Cache successfully deleted.' . PHP_EOL, FILE_APPEND);
      }

      file_put_contents($file, 'Coupons successfully updated.' . PHP_EOL, FILE_APPEND);

      //return response
      return ApiResponse::setResponse('Coupons successfully updated.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
