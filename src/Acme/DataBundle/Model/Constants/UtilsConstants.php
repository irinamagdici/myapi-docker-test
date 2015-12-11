<?php

namespace Acme\DataBundle\Model\Constants;

use Doctrine\ORM\Mapping as ORM;

class UtilsConstants {

  //search
	const EARTHRADIUS = 3959; //earth's mean radius in miles (use 6371 for kilometers)
	const RADIUS = 50; //radius of bounding circle in miles (kilometers for radius in kilometers)

  //password
  const RESET_PASSWORD_EXPIRATION = 86400; //24 hours (in seconds)

  //grand opening stores (featured shops)
  const DAYS = 60;

  //coupon -med size
  const WIDTH = 255;
  const HEIGHT = 154;

}
