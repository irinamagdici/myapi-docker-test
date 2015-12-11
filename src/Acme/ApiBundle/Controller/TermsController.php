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
use Acme\DataBundle\Entity\Terms;
use Acme\DataBundle\Model\Utility\ApiResponse;

class TermsController extends ApiController implements ClassResourceInterface {

  /**
   * Get terms service used to get a list of terms starting with specified letter.
   *
   * @ApiDoc(
   *     section="Terms",
   *     resource=true,
   *     description="Get terms starting with specified letter",
   *     filters={
   *          {"name"="keyword", "dataType"="string"},
   *          {"name"="firstLetter", "dataType"="string"},
   *          {"name"="exactKeyword", "dataType"="string"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/terms/")
   *
   */
  public function getTermsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {
      $keyword = trim($request->get('keyword'));
      $firstLetter = trim($request->get('firstLetter'));
      $exactKeyword = trim($request->get('exactKeyword')) ? trim($request->get('exactKeyword')) : '';
      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get terms from cache
      $cacheKey = 'terms' . $keyword . $firstLetter . $exactKeyword;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get terms from DB
      $entities = $em->getRepository('AcmeDataBundle:Terms')->getTerms($keyword, $firstLetter, $exactKeyword);
      //save to cache
      $cache->save($cacheKey, $entities);

      //return response
      return ApiResponse::setResponse($entities);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get terms service used to get a term by its slug.
   *
   * @ApiDoc(
   *     section="Terms",
   *     resource=true,
   *     description="Get terms by slug",
   *     requirements={
   *         {"name"="slug", "dataType"="string", "required"=true, "description"="coupon slug"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/terms/slug/{slug}")
   *
   */
  public function getTermsBySlugAction(Request $request, $slug) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get terms from cache
      $cacheKey = 'terms-slug' . $slug;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get terms from DB
      $entity = $em->getRepository('AcmeDataBundle:Terms')->getTermBySlug($slug);
      //save to cache
      $cache->save($cacheKey, $entity[0]);

      //return response
      return ApiResponse::setResponse($entity[0]);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get random term service used to get a random term from dictionary.
   *
   * @ApiDoc(
   *     section="Terms",
   *     resource=true,
   *     description="Get random term",
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/terms/random/")
   *
   */
  public function getRandomTermAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {
      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //get terms from cache
      $cacheKey = 'termrand' . date("Y-m-d");
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get term from DB
      $entity = $em->getRepository('AcmeDataBundle:Terms')->getRandomTerm();
      //save to cache
      $cache->save($cacheKey, $entity);

      //return response
      return ApiResponse::setResponse($entity);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}
