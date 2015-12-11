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
use Acme\DataBundle\Entity\Users;
use Acme\DataBundle\Model\Utility\ApiResponse;
use Acme\DataBundle\Model\Constants\UsersRole;
use Acme\DataBundle\Model\Constants\UtilsConstants;
use Acme\DataBundle\Model\Utility\StringUtility;
use Acme\DataBundle\Model\Utility\EntitiesUtility;
use Acme\DataBundle\Model\Utility\Clutch;


class SecurityController extends ApiController implements ClassResourceInterface {

  /**
   * Login service used to login with email and password.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Basic login",
   *     parameters={
   *         {"name"="email", "dataType"="string", "required"=true, "description"="user email"},
   *         {"name"="password", "dataType"="string", "required"=true, "description"="user password"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the user is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/account/login/")
   *
   */
  public function loginAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'login');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check user by email/username
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneByUsername(trim($request->get('email')));

      //no user found
      if(!$entity)
        return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

      //user is not active
      if(!$entity->isEnabled())
        return ApiResponse::setResponse('Your account is not active.', Codes::HTTP_UNAUTHORIZED);

      //check password
      $password = $entity->getPassword();

      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($entity);
      $pass = $encoder->encodePassword($request->get('password'), $entity->getSalt());

      if(strcmp($password, $pass) !== 0)
        return ApiResponse::setResponse('Incorrect password.', Codes::HTTP_UNAUTHORIZED);

      //get clutch customer data
      $customerData = Clutch::getCustomerInfo($this->container->parameters['clutch'], $entity->getEmail(), $entity->getPhone());

      if(!empty($customerData)) {
        //update data in DB
        $entity->setCardNumber($customerData['cardNumber']);
        $entity->setCustomCardNumber($customerData['customCardNumber']);
        $entity->setLoyaltyPointsBalance($customerData['balance']);

        $em->flush();
      }

      //if user has My Meineke set, get store coupons
      $coupons = array();
      if($entity->getMyStore()) {
        $coupons = $this->getMyMeinekeCoupons($entity->getMyStore());
      }

      //return response
      return ApiResponse::setResponse(EntitiesUtility::getUserData($entity, $coupons));
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Login service used to login with facebook.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Facebook login",
   *     parameters={
   *         {"name"="facebookId", "dataType"="string", "required"=true, "description"="user facebookId"},
   *         {"name"="email", "dataType"="string", "required"=true, "description"="user email from facebook account"},
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         404="Returned when the user is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/account/login/fb/")
   *
   */
  public function loginFbAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'fbLogin');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check user by facebook id
      $entityFb = $em->getRepository('AcmeDataBundle:Users')->findOneByFacebookId(trim($request->get('facebookId')));

      //no user found
      if(!$entityFb) {
        //check user by email/username
        $entityUsername = $em->getRepository('AcmeDataBundle:Users')->findOneByUsername(trim($request->get('email')));

        //no user found
        if(!$entityUsername)
          return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

        //update user with facebook id
        $entityUsername->setFacebookId($request->get('facebookId'));
        $em->persist($entityUsername);
        $em->flush();

        $entity = $entityUsername;
      }
      else $entity = $entityFb;

      //user is not active
      if(!$entity->isEnabled())
        return ApiResponse::setResponse('Your account is not active.', Codes::HTTP_UNAUTHORIZED);

      //get clutch customer data
      $customerData = Clutch::getCustomerInfo($this->container->parameters['clutch'], $entity->getEmail(), $entity->getPhone());

      if(!empty($customerData)) {
        //update data in DB
        $entity->setCardNumber($customerData['cardNumber']);
        $entity->setCustomCardNumber($customerData['customCardNumber']);
        $entity->setLoyaltyPointsBalance($customerData['balance']);

        $em->flush();
      }

      //if user has My Meineke set, get store coupons
      $coupons = array();
      if($entity->getMyStore()) {
        $coupons = $this->getMyMeinekeCoupons($entity->getMyStore());
      }

      //return response
      return ApiResponse::setResponse(EntitiesUtility::getUserData($entity, $coupons));
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Register service used to create a user.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Create user",
   *     parameters={
   *         {"name"="facebookId", "dataType"="string", "required"=false, "description"="user facebookId"},
   *         {"name"="firstName", "dataType"="string", "required"=false, "description"="user first name"},
   *         {"name"="lastName", "dataType"="string", "required"=false, "description"="user last name"},
   *         {"name"="email", "dataType"="string", "required"=true, "description"="user email address"},
   *         {"name"="phone", "dataType"="string", "required"=true, "description"="user phone number"},
   *         {"name"="password", "dataType"="string", "required"=true, "description"="user password"},
   *         {"name"="confirmPassword", "dataType"="string", "required"=true, "description"="user confirm password"},
   *         {"name"="storeId", "dataType"="integer", "required"=false, "description"="store id"},
   *         {"name"="newsletter", "dataType"="integer", "required"=false, "description"="newsletter settings"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         409="Returned when the user already exists/password do not match.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/account/register/")
   *
   */
  public function registerAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'register');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    $em->getConnection()->beginTransaction();
    try {

      //check user
      if($request->get('facebookId')) {
        $entityFb = $em->getRepository('AcmeDataBundle:Users')->findOneByFacebookId(trim($request->get('facebookId')));
        if($entityFb)
          return ApiResponse::setResponse('You are already registered with facebook.', Codes::HTTP_CONFLICT);
      }

      //check user by email/username
      $entityUsername = $em->getRepository('AcmeDataBundle:Users')->findOneByUsername(trim($request->get('email')));
      if($entityUsername)
        return ApiResponse::setResponse('A user with this email address already exists.', Codes::HTTP_CONFLICT);

      //check password and confirmPassword
      if(strcmp($request->get('password'), $request->get('confirmPassword')) !== 0)
        return ApiResponse::setResponse('Password and Confirmation Password do not match.', Codes::HTTP_CONFLICT);

      //create user
      $entity = new Users();

      //compose password
      $p = $request->get('password');
      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($entity);
      $pass = $encoder->encodePassword($p, $entity->getSalt());

      $entity->setUsername(trim($request->get('email')));
      $entity->setUsernameCanonical(trim($request->get('email')));
      $entity->setEmail(trim($request->get('email')));
      $entity->setEmailCanonical(trim($request->get('email')));
      $entity->setPassword($pass);
      $entity->setFirstName(trim($request->get('firstName')));
      $entity->setLastName(trim($request->get('lastName')));
      $entity->setPhone(trim($request->get('phone')));
      if(trim($request->get('facebookId')))
        $entity->setFacebookId(trim($request->get('facebookId')));
      if(trim($request->get('newsletter')))
        $entity->setNewsletter(1);

      if(trim($request->get('storeId'))) {
        //get store by store id
        $entityStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));
        if($entityStore)
          $entity->setMyStore($entityStore);
      }

      $entity->setEnabled(0);
      $entity->setLocked(0);
      $entity->setExpired(0);
      $entity->setRoles(array(UsersRole::MEINEKE));

      $em->persist($entity);

      //get clutch customer data
      $customerData = Clutch::getCustomerInfo($this->container->parameters['clutch'], trim($request->get('email')), trim($request->get('phone')));

      if(!empty($customerData)) {
        //add data in DB
        $entity->setCardNumber($customerData['cardNumber']);
        $entity->setCustomCardNumber($customerData['customCardNumber']);
        $entity->setLoyaltyPointsBalance($customerData['balance']);
      }

      $em->flush();

      //send email with the activation link
      $this->get('emailNotificationBundle.email')->sendActivationEmail($entity);

      //commit account creation
      $em->getConnection()->commit();

      //return response
      return ApiResponse::setResponse('Account created successfully. Please check your email in order to activate your account.');
    }
    catch(\Exception $e) {
      $em->getConnection()->rollback();
      $em->close();

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Resend activation service used to resend the email with activation link.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Resend activation link",
   *     parameters={
   *         {"name"="email", "dataType"="string", "required"=true, "description"="user email address"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when the user is not found.",
   *         409="Returned when the user is already active.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/account/activation/resend/")
   *
   */
  public function resendActivationAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'resendActivation');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check user by email
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneByUsername(trim($request->get('email')));

      //no user found
      if(!$entity)
        return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

      if($entity->isEnabled())
        return ApiResponse::setResponse('Your account is already active.', Codes::HTTP_CONFLICT);

      //send email with the activation link
      $this->get('emailNotificationBundle.email')->sendActivationEmail($entity);

      //return response
      return ApiResponse::setResponse('Email successfully sent. Please check your email in order to activate your account.');
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Activate service used to activate a user.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Activate user",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="token", "dataType"="string", "required"=true, "description"="validation token"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when the user is not found.",
   *         409="Returned when the user is already active.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/account/activate/{id}/")
   *
   */
  public function activateAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'activate');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    $em->getConnection()->beginTransaction();
    try {

      //check user by email/username
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //no user found
      if(!$entity)
        return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

      //check token
      $token = md5($id . $entity->getEmail() . $entity->getPassword());
      if(strcmp($token, trim($request->get('token'))) !== 0)
        return ApiResponse::setResponse('Invalid token.', Codes::HTTP_BAD_REQUEST);

      if($entity->isEnabled())
        return ApiResponse::setResponse('Your account is already active.', Codes::HTTP_CONFLICT);

      //activate user
      $entity->setEnabled(1);

      //get clutch customer data
      $customerData = Clutch::getCustomerInfo($this->container->parameters['clutch'], $entity->getEmail(), $entity->getPhone());

      if(!empty($customerData)) {
        //update data in DB
        $entity->setCardNumber($customerData['cardNumber']);
        $entity->setCustomCardNumber($customerData['customCardNumber']);
        $entity->setLoyaltyPointsBalance($customerData['balance']);
      }

      $em->flush();

      //send email with the login details
      $this->get('emailNotificationBundle.email')->sendActivatedEmail($entity);

      //commit account activation
      $em->getConnection()->commit();

      //if user has My Meineke set, get store coupons
      $coupons = array();
      if($entity->getMyStore()) {
        $coupons = $this->getMyMeinekeCoupons($entity->getMyStore());
      }

      //return response
      return ApiResponse::setResponse(EntitiesUtility::getUserData($entity, $coupons));
    }
    catch(\Exception $e) {
      $em->getConnection()->rollback();
      $em->close();

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   * Forgot password service used to recover the password.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Forgot password",
   *     parameters={
   *         {"name"="email", "dataType"="string", "required"=true, "description"="user email address"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when the user is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/account/forgotpassword/")
   *
   */
  public function forgotPasswordAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'forgotPassword');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {

      //check user by email/username
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneByUsername(trim($request->get('email')));

      //no user found
      if(!$entity)
        return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

      if(!$entity->isEnabled())
        return ApiResponse::setResponse('Your account is not active.', Codes::HTTP_UNAUTHORIZED);

      //send email
      $this->get('emailNotificationBundle.email')->sendForgotPasswordEmail($entity);

      return ApiResponse::setResponse('Reset email successfully sent.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Reset password service used to reset the password.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Reset password",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="timestamp", "dataType"="string", "required"=true, "description"="validation timestamp"},
   *         {"name"="token", "dataType"="string", "required"=true, "description"="validation token"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         404="Returned when the user is not found.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/account/resetpassword/{id}/")
   *
   */
  public function resetPasswordAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //validate request parameters
    $validationResult = $this->validate($request, 'resetPassword');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    $em->getConnection()->beginTransaction();
    try {

      //check user by email/username
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //no user found
      if(!$entity)
        return ApiResponse::setResponse('User not found.', Codes::HTTP_NOT_FOUND);

      //check timestamp
      if(strtotime('now') - trim($request->get('timestamp')) > UtilsConstants::RESET_PASSWORD_EXPIRATION)
        return ApiResponse::setResponse('Token expired.', Codes::HTTP_BAD_REQUEST);

      //check token
      $token = md5(trim($request->get('timestamp')) . $id . $entity->getEmail() . $entity->getPassword());
      if(strcmp($token, trim($request->get('token'))) !== 0)
        return ApiResponse::setResponse('Invalid token.', Codes::HTTP_BAD_REQUEST);

      //reset password
      $p = StringUtility::generateRandomString();

      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($entity);
      $pass = $encoder->encodePassword($p, $entity->getSalt());

      $entity->setPassword($pass);

      $em->flush();

      //send email
      $this->get('emailNotificationBundle.email')->sendResetPasswordEmail($entity, $p);

      //commit password change
      $em->getConnection()->commit();

      return ApiResponse::setResponse('Email with new password successfully sent.');
    }
    catch (\Exception $e) {
      $em->getConnection()->rollback();
      $em->close();

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Change password service used to change the password.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Change password",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="currentPassword", "dataType"="string", "required"=true, "description"="user current password"},
   *         {"name"="newPassword", "dataType"="string", "required"=true, "description"="new password"},
   *         {"name"="confirmPassword", "dataType"="string", "required"=true, "description"="confirm new password"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         409="Returned when the passwords do not match.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/account/changepassword/{id}/")
   *
   */
  public function changePasswordAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'changePassword');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //compare current password
      $password = $entity->getPassword();
      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($entity);
      $pass = $encoder->encodePassword($request->get('currentPassword'), $entity->getSalt());

      if(strcmp($password, $pass) !== 0)
        return ApiResponse::setResponse('The Old Password you entered is incorrect.', Codes::HTTP_BAD_REQUEST);

      //check newPassword and confirmPassword
      if(strcmp($request->get('newPassword'), $request->get('confirmPassword')) !== 0)
        return ApiResponse::setResponse('New Password and Confirmation Password do not match.', Codes::HTTP_CONFLICT);

      //set new password
      $newpass = $encoder->encodePassword($request->get('newPassword'), $entity->getSalt());

      $entity->setPassword($newpass);

      $em->flush();

      return ApiResponse::setResponse($newpass);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Check password service used to check the current password for a specific user.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Check password",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="currentPassword", "dataType"="string", "required"=true, "description"="user current password"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Post("/secured/account/checkpassword/{id}/")
   *
   */
  public function checkPasswordAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'checkPassword');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //compare current password
      $password = $entity->getPassword();
      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($entity);
      $pass = $encoder->encodePassword($request->get('currentPassword'), $entity->getSalt());

      if(strcmp($password, $pass) !== 0)
        return ApiResponse::setResponse('The Old Password you entered is incorrect.', Codes::HTTP_BAD_REQUEST);

      return ApiResponse::setResponse('Current password is correct.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Edit profile service used to edit profile info.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Edit profile",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="firstName", "dataType"="string", "required"=true, "description"="user first name"},
   *         {"name"="lastName", "dataType"="string", "required"=true, "description"="user last name"},
   *         {"name"="phone", "dataType"="string", "required"=true, "description"="user phone number"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/account/profile/{id}/")
   *
   */
  public function editProfileAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'profile');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //set new info
      $entity->setFirstName(trim($request->get('firstName')));
      $entity->setLastName(trim($request->get('lastName')));
      $entity->setPhone(trim($request->get('phone')));

      $em->flush();

      //set info in Clutch
      if($entity->getCardNumber())
        Clutch::setCustomerInfo($this->container->parameters['clutch'], $entity);

      return ApiResponse::setResponse('Profile successfully updated.');
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Set Meineke store used to update profile info for a user with his favorite Meineke location.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Set favorite Meineke location",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="storeId", "dataType"="integer", "required"=true, "description"="store id | 0 to delete favorite meineke store"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Put("/secured/account/meineke/{id}/")
   *
   */
  public function editMeinekeAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    //validate request parameters
    $validationResult = $this->validate($request, 'myMeineke');
    if(!$validationResult->isSuccess)
      return ApiResponse::setResponse($validationResult->errorMessage, Codes::HTTP_BAD_REQUEST);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);
      if(trim($request->get('storeId')) != 0) {
        //get store by store id
        $entityStore = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(trim($request->get('storeId')));

        //set Meineke
        $entity->setMyStore($entityStore);

        $em->flush();

        $coupons = $this->getMyMeinekeCoupons($entityStore);

        return ApiResponse::setResponse(EntitiesUtility::getMyMeinekeDetails($entityStore, $coupons));
      }
      else {
        //set Meineke
        $entity->setMyStore(NULL);
        $em->flush();

        return ApiResponse::setResponse('My Meineke successfully removed.');
      }
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get profile service used to get full profile info.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Get profile",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/account/profile/{id}/")
   *
   */
  public function getProfileAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //get clutch customer data
      $customerData = Clutch::getCustomerInfo($this->container->parameters['clutch'], $entity->getEmail(), $entity->getPhone());

      //harcoded vehicles TODO
      /*$vehicles = array();
      $vehicleIds = array('VECH20001', 'VECH20002');
      for($i=0;$i<count($vehicleIds);$i++) {
        $vehicleInfo = Clutch::getVehicleInfo($this->container->parameters['clutch'], $vehicleIds[$i]);
        if(!empty($vehicleInfo['brandDemographics'])) {
          $vehicles[$i]['make'] = $vehicleInfo['brandDemographics']['vehicle1make'];
          $vehicles[$i]['year'] = $vehicleInfo['brandDemographics']['vehicle1year'];
          $vehicles[$i]['model'] = $vehicleInfo['brandDemographics']['vehicle1model'];
          $vehicles[$i]['vin'] = $vehicleInfo['brandDemographics']['vehicle1vin'];
          $vehicles[$i]['tag'] = $vehicleInfo['brandDemographics']['vehicle1tag'];
        }
      }*/

      $historyTransactions = array();
      $index = 0;
      if(!empty($customerData)) {
        //update data in DB
        $entity->setCardNumber($customerData['cardNumber']);
        $entity->setCustomCardNumber($customerData['customCardNumber']);
        $entity->setLoyaltyPointsBalance($customerData['balance']);

        $em->flush();

        //get clutch vehicles information
        $vehicles = array();
        $historyTransactionVehicle = array();
        if(!empty($customerData['brandDemographics'])) {
          $vehicleIds = explode(",", $customerData['brandDemographics']['vehicleIds']);
          for($i=0;$i<count($vehicleIds);$i++) {
            $vehicleInfo = Clutch::getVehicleInfo($this->container->parameters['clutch'], 'veh_' . $vehicleIds[$i]);

            //vehicle information
            if(!empty($vehicleInfo['brandDemographics'])) {
              $vehicles[$i]['make'] = isset($vehicleInfo['brandDemographics']['vehicle1make']) ? $vehicleInfo['brandDemographics']['vehicle1make'] : '';
              $vehicles[$i]['year'] = isset($vehicleInfo['brandDemographics']['vehicle1year']) ? $vehicleInfo['brandDemographics']['vehicle1year'] : '';
              $vehicles[$i]['model'] = isset($vehicleInfo['brandDemographics']['vehicle1model']) ? $vehicleInfo['brandDemographics']['vehicle1model'] : '';
              $vehicles[$i]['vin'] = isset($vehicleInfo['brandDemographics']['vehicle1vin']) ? $vehicleInfo['brandDemographics']['vehicle1vin'] : '';
              $vehicles[$i]['tag'] = isset($vehicleInfo['brandDemographics']['vehicle1tag']) ? $vehicleInfo['brandDemographics']['vehicle1tag'] : '';
            }

            //get history transaction for every vehicle
            $historyTransactionVehicle[$i]['vehicle'] = $vehicles[$i]['make'] . ' ' . $vehicles[$i]['model'];
            $historyTransactionVehicle[$i]['transactions'] = Clutch::getHistoryTransaction($this->container->parameters['clutch'], 'veh_' . $vehicleIds[$i], '');

          }

          //print_r($historyTransactionVehicle);
          //exit();

          for($i=0;$i<count($historyTransactionVehicle);$i++) {

            for($j=0;$j<count($historyTransactionVehicle[$i]['transactions']);$j++) {
              if(isset($historyTransactionVehicle[$i]['transactions'][$j]['callType']) && $historyTransactionVehicle[$i]['transactions'][$j]['callType'] == "CHECKOUT_COMPLETE") {
                $transactionTime = date("m/d/y", intval($historyTransactionVehicle[$i]['transactions'][$j]['transactionTime'] / 1000));
                //get transaction details

                $transactionDetails = Clutch::getTransactionDetails($this->container->getParameter('clutch'), $historyTransactionVehicle[$i]['transactions'][$j]['transactionId']);
              // var_dump( $historyTransactionVehicle[$i]['transactions'][$j]['transactionId']);
              // var_dump($this->container->getParameter('clutch'));
               // var_dump("----");
               // var_dump($transactionDetails);
               

                if($transactionDetails) {
                  for($k=0;$k<count($transactionDetails);$k++) {

//                    $historyTransactions[$index]['vehicleServed'] = $historyTransactionVehicle[$i]['vehicle'];

                    $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['transactionTime'] = $transactionTime;
                    $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['amount'] = $transactionDetails[$k]['amount'];
                    $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['store'] = array();
//                    if($k == 3){
//                      var_dump($historyTransactions);die;
//                    }

                    if($transactionDetails[$k]['locationId']) {
                      //get location info
                      $locationInfo = $em->getRepository('AcmeDataBundle:Stores')->findOneByStoreId(str_replace("MK", "", $transactionDetails[$k]['locationId']));
                      if(is_object($locationInfo)){
                        $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['store'] = array(
                          'city' => $locationInfo->getLocationCity(),
                          'state' => $locationInfo->getLocationState(),
                          'phone' => $locationInfo->getPhone(),
                          'semCamPhone' => $locationInfo->getSemCamPhone()
                        );
                      }
                    }
                    if($transactionDetails[$k]['sku'] !== "0-0, MEMO" && $transactionDetails[$k]['sku'] !== "0-0, Discount"){

                    

                      if($transactionDetails[$k]['sku']) {
                        if (strpos($transactionDetails[$k]['sku'],', ') !== false) {
                          $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $transactionDetails[$k]['sku'];

                         $skuArr = explode(",", $transactionDetails[$k]['sku']);
                         $skuSecond = explode("-", $skuArr[0]);
                         // for($sk=0;$sk<count($skuArr);$sk++) {
                           $service = $em->getRepository('AcmeDataBundle:Sku')->findOneBySkuCode(trim($skuSecond[1])."-".trim($skuSecond[0]));
                         // var_dump($service);die;

                           if($service != NULL) {
                             $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $service->getDisplayName();
                           }else{
                              if(!empty($skuSecond[0]) && !empty($skuSecond[1])){
                                $stmt = $this->getDoctrine()->getEntityManager()
                                    ->getConnection()
                                    ->prepare('select * from sku_old_lines where id = :id');
                                    $stmt->bindParam(':id', trim($skuSecond[1]));
                                    $stmt->execute();
                                    $result1 = $stmt->fetchAll();
                               
                                $stmt2 = $this->getDoctrine()->getEntityManager()
                                    ->getConnection()
                                    ->prepare('select * from sku_old_classes where id = :id');
                                    $stmt2->bindParam(':id', trim($skuSecond[0]));
                                    $stmt2->execute();
                                    $result2 = $stmt->fetchAll();
                                $result = $result1[0]["longdescription"] . $result2[0]["longdescription"];
                              }
                                    
                              $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['skuName'] = $transactionDetails[$k]['sku'];
                              $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $result;
                           }
                         // }
                        }else{
                          $service = $em->getRepository('AcmeDataBundle:Sku')->findOneBySkuCode($transactionDetails[$k]['sku']);
                           if($service != NULL) {
                             $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $service->getDisplayName();
                           }else{
                              $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = NULL;//"No service found. Call customer care at <a href='tel: 1-800-447-3070'>1-800-447-3070</a>";
                           }
                          // $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $transactionDetails[$k]['sku'];
                        }
                      }
                      else
                        $historyTransactions[$historyTransactionVehicle[$i]['vehicle']][$index]['service'] = $transactionDetails[$k]['sku'];// de pus sku (121 - 01)

                      $index++;
                    }
                  }//End checking 0-0, MEMO or 0-0, Discount
                }

              }
            }
          }
          //print_r($historyTransactions);
          //exit();
        }

        //get history transactions for last location
        $historyTransactionsLastLocation = Clutch::getHistoryTransaction($this->container->parameters['clutch'], $entity->getCardNumber(), '');
        $transactionTime = 0;
        $transactionId = 0;
        $lastVisitedStoreId = '';
        for($i=0;$i<count($historyTransactionsLastLocation);$i++) {
          if(isset($historyTransactionsLastLocation[$i]['callType']) && $historyTransactionsLastLocation[$i]['callType'] == "CHECKOUT_COMPLETE") {
            if(intval($historyTransactionsLastLocation[$i]['transactionTime'] / 1000) > intval($transactionTime / 1000)) {
              $transactionTime = $historyTransactionsLastLocation[$i]['transactionTime'];
              $transactionId = $historyTransactionsLastLocation[$i]['transactionId'];
            }
          }
        }

        if($transactionId)
          $lastVisitedStoreId = Clutch::getTransactionDetailsForLastLocation($this->container->parameters['clutch'], $transactionId);
      }

      //check future appointments in Full Slate
      $appointments = $em->getRepository('AcmeDataBundle:Appointments')->getFutureAppointments($id);
      if($appointments) {
        //check services
        for($i=0;$i<count($appointments);$i++) {
          //format date
          $fullDate = $appointments[$i]['appointmentDate'];
          $appointments[$i]['appointmentDate'] = $fullDate->format('m/d/Y');
          $appointments[$i]['appointmentHours'] = $fullDate->format('g:i a');

          $servicesApp = $em->getRepository('AcmeDataBundle:AppointmentsHasServices')->findByAppointments($appointments[$i]['id']);
          if($servicesApp) {
            $services = array();
            for($j=0;$j<count($servicesApp);$j++) {
              $services[$j] = $servicesApp[$j]->getServices()->getTitle();
            }
            $appointments[$i]['services'] = implode(", ", $services);
          }
        }
      }

      //get rewards
      $myRewards = $em->getRepository('AcmeDataBundle:Rewards')->getMyRewards($entity->getLoyaltyPointsBalance() ? $entity->getLoyaltyPointsBalance() : 0);
      if($myRewards) {
        for($i=0;$i<count($myRewards);$i++) {
          //update image link
          $myRewards[$i]['image'] = $this->container->parameters['project']['cdn_front_resources_url'] . 'images/' . $myRewards[$i]['image'];
        }
      }

      //if user has My Meineke set, get store coupons
      $coupons = array();
      if($entity->getMyStore()) {
        $coupons = $this->getMyMeinekeCoupons($entity->getMyStore());
      }

      //get info
      $info = EntitiesUtility::getUserData($entity, $coupons);
      $info['vehicles'] = $vehicles;
      $info['futureAppointments'] = $appointments;
      $info['myRewards'] = $myRewards;
      $info['historyTransactions'] = $historyTransactions;
      $info['lastVisitedStoreId'] = str_replace("MK", "", $lastVisitedStoreId);

      return ApiResponse::setResponse($info);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get history transactions service used to get a list of users's transactions history.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Get history transactions",
   *     requirements={
   *         {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
   *     },
   *     parameters={
   *         {"name"="period", "dataType"="string", "required"=false, "description"="period of time in days"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         401="Returned when the user is not authorized.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/secured/account/transactions/{id}/")
   *
   */
  public function getHistoryTransactionAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();

    //check permissions
    $user = $this->getAuthUser();
    if($user->getId() != $id)
      return ApiResponse::setResponse('User not authorized.', Codes::HTTP_UNAUTHORIZED);

    try {
      //get user by id
      $entity = $em->getRepository('AcmeDataBundle:Users')->findOneById($id);

      //get clutch customer data
      $info = array();

      if($entity->getCardNumber()) {
        //period of time
        $period = trim($request->get('period'));

        //get history transactions
        $historyTransactions = Clutch::getHistoryTransaction($this->container->parameters['clutch'], $entity->getCardNumber(), $period);
        $index = 0;
        for($i=0;$i<count($historyTransactions);$i++) {
          if($historyTransactions[$i]['callType'] == "CHECKOUT_COMPLETE") {
            $transactionTime = date("m/d/y", intval($historyTransactions[$i]['transactionTime'] / 1000));
            //get transaction details
            $transactionDetails = Clutch::getTransactionDetails($this->container->parameters['clutch'], $historyTransactions[$i]['transactionId']);
            if($transactionDetails) {
              for($j=0;$j<count($transactionDetails);$j++) {
                $info[$index]['transactionTime'] = $transactionTime;
                $info[$index]['amount'] = $transactionDetails[$j]['amount'];
                if($transactionDetails[$j]['sku']) {
                  $skuArr = explode(",", $transactionDetails[$j]['sku']);
                  for($k=0;$k<count($skuArr);$k++) {
                    $service = $em->getRepository('AcmeDataBundle:Sku')->findOneBySkuCode($skuArr[$k]);
                    if($service) {
                      $info[$index]['service'] = $service->getDisplayName();
                    }
                  }
                }
                else
                  $info[$index]['service'] = '';

                $index++;
              }
            }
          }
        }
      }

      return ApiResponse::setResponse($info);
    }
    catch (\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Get customer data from Clutch for register service.
   *
   * @ApiDoc(
   *     section="Security",
   *     resource=true,
   *     description="Get user information from Clutch",
   *     parameters={
   *         {"name"="keytagId", "dataType"="string", "required"=false, "description"="user keytag id"},
   *         {"name"="phoneNumber", "dataType"="string", "required"=false, "description"="user phone number"}
   *     },
   *     statusCodes={
   *         200="Returned when successful.",
   *         400="Returned when parameters are invalid.",
   *         500="Returned when the server makes a booboo."
   *     }
   * )
   *
   * @Get("/account/checkregister/")
   *
   */
  public function registerCheckAction(Request $request) {

    try {

      //get clutch customer data
      $customCardNumber = trim($request->get('keytagId'));
      $phoneNumber = trim($request->get('phoneNumber'));
      $customerData = Clutch::getCustomerInfoForRegister($this->container->parameters['clutch'], $customCardNumber, $phoneNumber);

      //return response
      return ApiResponse::setResponse($customerData);
    }
    catch(\Exception $e) {

      return ApiResponse::setResponse($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

}
