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
use Acme\DataBundle\Entity\Testimonials;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Utility\StringUtility;


class TestimonialsController extends ApiController implements ClassResourceInterface {

  /**
   * Add testimonials service used to add updated testimonials for stores.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Testimonials",
   *     resource=true,
   *     description="Add testimonials",
   *     parameters={
   *         {"name"="serializedData", "dataType"="string", "required"=true, "description"="all testimonials data serialized"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/testimonials/")
   *
   */
  public function updatedTestimonialsAction(Request $request) {

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
      //remove all old testimonials
      $em->getRepository('AcmeDataBundle:Testimonials')->truncateTestimonials();

      for($i=0;$i<count($finalData);$i++) {

        //create new testimonial
        $entity = new Testimonials();

        $entityStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($finalData[$i]['id']));
        $name = trim($finalData[$i]['condition']) == 1 ? trim($finalData[$i]['fname']) . ' ' . trim($finalData[$i]['lname']) : 'Verified Meineke Customer';

        //add data
        $entity->setName($name);
        $entity->setDescription(trim($finalData[$i]['comment']));
        $entity->setStores($entityStore);

        $em->persist($entity);
        $em->flush();
      }

      //initiate redis cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //find keys
      $keys = $cache->find('*testimonials*');

      //delete cache
      if(!empty($keys)) {
        for($i=0;$i<count($keys);$i++) {
          $cache->delete($keys[$i]);
        }
      }

      return ApiResponse::setResponse('Testimonials updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Get testimonials service used to get testimonials for a specific store.
   *
   * @ApiDoc(
   *     section="Testimonials",
   *     resource=true,
   *     description="Get testimonials",
   *     filters={
   *         {"name"="storeId", "dataType"="integer"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/testimonials/")
   *
   */
  public function getTestimonialsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get testimonials from cache
      $cacheKey = 'testimonials' . trim($request->get('storeId'));
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get testimonials from DB
      $storeId = '';
      if(trim($request->get('storeId'))) {
        $entityStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
        if($entityStore)
          $storeId = $entityStore->getId();
      }

      $entities = $em->getRepository('AcmeDataBundle:Testimonials')->getTestimonials($storeId);

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
   * Get random terstimonials service used to get two random testimonials from all testimonials in DB.
   *
   * @ApiDoc(
   *     section="Testimonials",
   *     resource=true,
   *     description="Get random terstimonials",
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/testimonials/random/")
   *
   */
  public function getRandomTestimonialsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get testimonials from cache
      $cacheKey = 'testimonialsrand' . date("Y-m-d");
      if($cache->contains($cacheKey))
          return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get testimonials from DB
      $entities = $em->getRepository('AcmeDataBundle:Testimonials')->getRandomTestimonials();

      //parse data
      for($i=0;$i<count($entities);$i++) {
        //get store details
        $store = $em->getRepository('AcmeDataBundle:Stores')->findOneById($entities[$i]['storeId']);

        //update date
        $entities[$i]['storeLocation'] = $store->getLocationCity() . ', ' . $store->getLocationState();
        unset($entities[$i]['storeId']);
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

}
