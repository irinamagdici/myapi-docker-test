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
use Acme\DataBundle\Entity\JobSubmissions;
use Acme\DataBundle\Model\Utility\ApiResponse;


class JobsController extends ApiController implements ClassResourceInterface {

  /**
   * Get jobs service used to get list of jobs.
   *
   * @ApiDoc(
   *     section="Jobs",
   *     resource=true,
   *     description="Get jobs",
   *     filters={
   *         {"name"="storeId", "dataType"="integer"},
   *         {"name"="position", "dataType"="string"},
   *         {"name"="location", "dataType"="string"},
   *         {"name"="page", "dataType"="integer", "default"="1"},
   *         {"name"="noRecords", "dataType"="integer", "default"="10"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the store is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/jobs/")
   *
   */
  public function getJobsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    try {

      $storeId = '';
      if(trim($request->get('storeId'))) {
        $entityStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));

        if(!$entityStore)
          return ApiResponse::setResponse('Store not found.', Codes::HTTP_NOT_FOUND);

        $storeId = $entityStore->getId();
      }

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      //set pagination and sorting
      $this->setListingConfigurations($request, $page, $noRecords, $sortField, $sortType);

      //get testimonials from cache
      $cacheKey = 'jobs' . $storeId . trim($request->get('position')) . trim($request->get('location')) . $page . $noRecords . $sortField . $sortType;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get jobs from DB
      $noTotal = $em->getRepository('AcmeDataBundle:Jobs')->getJobsCount($storeId, trim($request->get('position')), trim($request->get('location')));
      $entities = $em->getRepository('AcmeDataBundle:Jobs')->getJobs($storeId, trim($request->get('position')), trim($request->get('location')), $page, $noRecords, $sortField, $sortType);

      //parse data
      for($i=0;$i<count($entities);$i++) {
        $entities[$i]['datePosted'] = $entities[$i]['datePosted'] ? $entities[$i]['datePosted']->format('m/d/Y') : '';
      }
      $finalData = array('jobs' => $entities, 'noTotal' => $noTotal);

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
   * Get job service used to get job details.
   *
   * @ApiDoc(
   *     section="Jobs",
   *     resource=true,
   *     description="Get job details",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="job id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the job is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/job/{id}/")
   *
   */
  public function getJobAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'jobs' . $id;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Jobs')->getJob($id);

      //job not found
      if(!$entity)
        return ApiResponse::setResponse('Job not found.', Codes::HTTP_NOT_FOUND);

      //format data
      unset($entity[0]['dateCreated'], $entity[0]['dateUpdated']);
      foreach($entity[0] as $key => $value) {
        $entity[0][$key] = $value ? $value : '';
      }
      $entity[0]['datePosted'] = $entity[0]['datePosted'] ? $entity[0]['datePosted']->format('m/d/Y') : '';

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
   * Get job service used to get job details using slug.
   *
   * @ApiDoc(
   *     section="Jobs",
   *     resource=true,
   *     description="Get job details by slug",
   *     requirements={
   *         {"name"="slug", "dataType"="string", "required"=true, "description"="job slug"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         404="Returned when the job is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/job-slug/{slug}/")
   *
   */
  public function getJobSlugAction(Request $request, $slug) {

    $em = $this->getDoctrine()->getManager();

    try {

      //intiate cache
      $cache = $this->get('cacheManagementBundle.redis')->initiateCache();

      $cacheKey = 'job-slug' . $slug;
      if($cache->contains($cacheKey))
        return ApiResponse::setResponse($cache->fetch($cacheKey));

      //get store details
      $entity = $em->getRepository('AcmeDataBundle:Jobs')->getJobSlug($slug);

      //job not found
      if(!$entity)
        return ApiResponse::setResponse('Job not found.', Codes::HTTP_NOT_FOUND);

      //format data
      unset($entity[0]['dateCreated'], $entity[0]['dateUpdated']);
      foreach($entity[0] as $key => $value) {
        $entity[0][$key] = $value ? $value : '';
      }
      $entity[0]['datePosted'] = $entity[0]['datePosted'] ? $entity[0]['datePosted']->format('m/d/Y') : '';

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
   * Add Job submissions service used to save form data for Meineke Careers.
   *
   * @ApiDoc(
   *     section="Jobs",
   *     resource=true,
   *     description="Add/Email Job submissions form data",
   *     parameters={
   *         {"name"="jobId", "dataType"="integer", "required"=true, "description"="job id"},
   *         {"name"="location", "dataType"="string", "required"=true, "description"="location"},
   *         {"name"="firstName", "dataType"="string", "required"=true, "description"="applicant first name"},
   *         {"name"="lastName", "dataType"="string", "required"=true, "description"="applicant last name"},
   *         {"name"="email", "dataType"="string", "required"=true, "description"="applicant email address"},
   *         {"name"="phone", "dataType"="string", "required"=true, "description"="applicant phone"},
   *         {"name"="message", "dataType"="string", "required"=false, "description"="applicant message"},
   *         {"name"="resume", "dataType"="string", "required"=false, "description"="resume pdf name"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when job not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/jobs/")
   *
   */
  public function addJobsSubmissionsAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    if(!strlen(trim($request->get('location'))))
      return ApiResponse::setResponse('Location is required.', Codes::HTTP_BAD_REQUEST);

    if(!strlen(trim($request->get('jobId'))) || !is_numeric(trim($request->get('jobId'))))
      return ApiResponse::setResponse('Please select a Job you are interested in.', Codes::HTTP_BAD_REQUEST);

    $validationResult = $this->validate($request, 'jobsSubmissions');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //get stores has jobs entity
      $storesHasJobs = $em->getRepository('AcmeDataBundle:StoresHasJobs')->findOneById(trim($request->get('jobId')));
      if(!$storesHasJobs)
        return ApiResponse::setResponse('Job not found.', Codes::HTTP_NOT_FOUND);

      //create jobs submissions in DB
      $entity = new JobSubmissions();

      $location = trim($request->get('location'));
      $locationArr = explode(",", $location);

      //search for dma ids
      $dmaIds = $em->getRepository('AcmeDataBundle:DmaCareers')->findOneBy(array("dma" => trim($locationArr[0]), "state" => trim($locationArr[1])));

      $emailsToSend = array();
      if($dmaIds) {
        //search for stores emails
        $storesEmails = $em->getRepository('AcmeDataBundle:Store2dma')->findBydmaid($dmaIds->getDmaId());

        $emails = array();
        if($storesEmails) {
          for($i=0;$i<count($storesEmails);$i++) {
            $emails[] = $storesEmails[$i]->getStoreEmail();
          }
        }

        $emailsString = implode(";", $emails);
        $emailsToSendRaw = explode(";", $emailsString);
        $emailsToSend = array_unique($emailsToSendRaw);
      }

      $entity->setLocation(trim($request->get('location')));
      $entity->setFirstName(trim($request->get('firstName')));
      $entity->setLastName(trim($request->get('lastName')));
      if(trim($request->get('resume')))
        $entity->setResumePdf(trim($request->get('resume')));
      if(trim($request->get('message')))
        $entity->setBody(trim($request->get('message')));
      $entity->setEmail(trim($request->get('email')));
      $entity->setPhone(trim($request->get('phone')));
      $entity->setStoresHasJobs($storesHasJobs);

      $em->persist($entity);
      $em->flush();

      //send email with form data
      $this->get('emailNotificationBundle.email')->sendJobSubmissionsEmail($entity, $emailsToSend);

      //return response
      return ApiResponse::setResponse('Job application successfully sent.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
