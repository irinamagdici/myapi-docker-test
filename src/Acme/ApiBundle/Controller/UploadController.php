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
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Utility\StringUtility;

class UploadController extends ApiController implements ClassResourceInterface {

  /**
   * Upload image service.
   * This is a secured service, you must use WSSE header authentication.
   *
   * @ApiDoc(
   *     section="Storage",
   *     resource=true,
   *     description="Upload image",
   *     parameters={
   *         {"name"="file", "dataType"="file", "required"=true, "description"="image file"},
   *         {"name"="imageType", "dataType"="string", "required"=true, "description"="COUPON_MEDIUM | COUPON_PRINT, default COUPON_MEDIUM"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/image/upload")
   *
   */
  public function imageUploadAction(Request $request) {

    //check permissions
    $user = $this->getAuthUser();
    if(!$user->hasRole(UsersRole::ADMIN) && !$user->isSuperAdmin())
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {

      $file = isset($_FILES['file']) ? $_FILES['file'] : '';

      if($file) {

        //upload image to Amazon S3
        $image = $this->get('storageBundle.image')->uploadImageToAS3($file, trim($request->get('imageType')) ? trim($request->get('imageType')) : 'COUPON_MEDIUM');

        return ApiResponse::setResponse($image);

      }

    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Upload document service.
   *
   * @ApiDoc(
   *     section="Storage",
   *     resource=true,
   *     description="Upload document",
   *     parameters={
   *         {"name"="file", "dataType"="file", "required"=true, "description"="document file"},
   *         {"name"="documentType", "dataType"="string", "required"=true, "description"="CV | STORES_COUPONS_CMS | BATCH_UPLOAD_CMS | PRINT, default CV"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/document/upload/")
   *
   */
  public function documentUploadAction(Request $request) {

    try {

      $file = isset($_FILES['file']) ? $_FILES['file'] : '';

      if($file) {

        //upload document to Amazon S3 or parse CSV
        $document = $this->get('storageBundle.document')->uploadDocumentToAS3($file, trim($request->get('documentType')) ? trim($request->get('documentType')) : 'CV');

        return ApiResponse::setResponse($document);

      }

    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}

?>
