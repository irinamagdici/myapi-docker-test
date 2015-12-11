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
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\StorageBundle\Model\S3;


class CacheController extends ApiController implements ClassResourceInterface {

  /**
   * Invalidate cache service used to invalidate cache for a specific key.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Cache",
   *     resource=true,
   *     description="Invalidate cache",
   *     parameters={
   *         {"name"="key", "dataType"="string", "required"=true, "description"="key for invalidate"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the key doesn't exists.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/invalidate/cache")
   *
   */
  public function invalidateCacheAction(Request $request) {

    try {

      //check permissions
      $user = $this->getAuthUser();
      if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
        return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

      //validate request parameters
      $validationResult = $this->validate($request, 'cache');
      if(!$validationResult->isSuccess)
        return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

      //initiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //find keys
      $keys = $cache->find('*'.trim($request->get('key')).'*');

      //delete cache
      if(!empty($keys)) {

        for($i=0;$i<count($keys);$i++) {
          $cache->delete($keys[$i]);
        }

        return ApiResponse::setResponse('Cache successfully invalidated.');
      }

      return ApiResponse::setResponse('Cache key not found.', Codes::HTTP_NOT_FOUND);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Invalidate CDN cache service used to invalidate cache for a specific folder on CDN.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Cache",
   *     resource=true,
   *     description="Invalidate CDN cache",
   *     parameters={
   *         {"name"="folder", "dataType"="string", "required"=true, "description"="folder to invalidate"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the folder doesn't exists.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/invalidate/cdncache")
   *
   */
  public function invalidateCDNCacheAction(Request $request) {

    try {

      //check permissions
      $user = $this->getAuthUser();
      if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
        return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

      //validate request parameters
      $validationResult = $this->validate($request, 'cacheCDN');
      if(!$validationResult->isSuccess)
        return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

      //initiate S3
      $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);

      $invalidation = $s3->invalidateDistribution($this->container->parameters['storage']['amazon_s3_distribution_id'], '<?xml version="1.0" encoding="UTF-8"?><InvalidationBatch><Path>' . trim($request->get('folder')) . '</Path><CallerReference>' . microtime(true) . '</CallerReference></InvalidationBatch>');
      if($invalidation)
        return ApiResponse::setResponse('Request successfully sent to Amazon.');

      return ApiResponse::setResponse('Folder not found.', Codes::HTTP_NOT_FOUND);

    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
