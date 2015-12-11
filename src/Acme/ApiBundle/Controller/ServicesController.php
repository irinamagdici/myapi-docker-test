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
use Acme\DataBundle\Entity\Slides;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Utility\ApiResponse;


class ServicesController extends ApiController implements ClassResourceInterface {

  /**
   * Get main services used to get a list of all popular services.
   *
   * @ApiDoc(
   *     section="Services",
   *     resource=true,
   *     description="Get popular services",
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/services/")
   *
   */
  public function getServicesAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get services from cache
      $cacheKey = 'services';
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get services from DB
      $entities = $em->getRepository('AcmeDataBundle:Services')->getPopularServices();

      //parse data
      for($i=0;$i<count($entities);$i++) {
        foreach($entities[$i] as $key => $value) {
          $entities[$i][$key] = $value ? $value : '';
        }
        $entities[$i]['icon'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $entities[$i]['icon'];
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
   * Get service details by slug used to get specific service details.
   *
   * @ApiDoc(
   *     section="Services",
   *     resource=true,
   *     description="Get service details by slug",
   *     requirements={
   *         {"name"="slug", "dataType"="string", "required"=true, "description"="service slug"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the service is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/service/{slug}/")
   *
   */
  public function getServiceAction(Request $request, $slug) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get services from cache
      $cacheKey = 'services' . $slug;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get service details from DB
      $entity = $em->getRepository('AcmeDataBundle:Services')->getService($slug);

      //service not found
      if(!$entity)
        return ApiResponse::setResponse('Service not found.', Codes::HTTP_NOT_FOUND);

      //icon path
      $entity[0]['icon'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $entity[0]['icon'];

      //save to cache
      $cache->save($cacheKey, $entity);

      //return response
      return ApiResponse::setResponse($entity);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
