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
use Acme\DataBundle\Model\Utility\ApiResponse;


class SlidesController extends ApiController implements ClassResourceInterface {

  /**
   * Get slides service used to get a list of all homepage slides.
   *
   * @ApiDoc(
   *     section="Slides",
   *     resource=true,
   *     description="Get slides",
   *     filters={
   *         {"name"="storeId", "dataType"="integer"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/slides/")
   *
   */
  public function getSlidesAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get slides from cache
      $cacheKey = 'slides' . trim($request->get('storeId'));
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get slides from DB
      $entities = $em->getRepository('AcmeDataBundle:Slides')->getSlides(trim($request->get('storeId')));

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
