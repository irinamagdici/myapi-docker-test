<?php

namespace Acme\EmailNotificationBundle\Controller;

class BaseController
{

/**********************************************************************************************************************************
Protected Fields
**********************************************************************************************************************************/
	protected $container;
	protected $templateEngine;

/**********************************************************************************************************************************
Protected Methods
**********************************************************************************************************************************/
  protected function getFromAddress() {
  	return array($this->container->getParameter('email_notification_from_email') => $this->container->getParameter('email_notification_from_name'));
  }

  protected function getToAddress() {
  	return explode(";", $this->container->getParameter('email_notification_to'));
  }

  protected function getToAddressFleet() {
    return explode(";", $this->container->getParameter('email_notification_fleet'));
  }

  protected function getToFeedbackAddress() {
    return explode(";", $this->container->getParameter('email_feedback_to'));
  }

  protected function getToClosedAddress() {
    return explode(";", $this->container->getParameter('email_closed_to'));
  }

  protected function getToCronAddress() {
    return explode(";", $this->container->getParameter('email_cron_to'));
  }

  protected function getBccAddress() {
  	return explode(";", $this->container->getParameter('email_notification_bcc'));
  }

  protected function getFrontURL() {
    $project = $this->container->getParameter('project');

    return $project['front_url'];
  }

  protected function getCDNURL() {
    $project = $this->container->getParameter('project');

    return $project['cdn_front_resources_url'];
  }
}
