<?php

namespace Acme\ApiBundle\Security\Core\Authentication\Provider;

use Escape\WSSEAuthenticationBundle\Security\Core\Authentication\Provider\Provider as Base;

class Provider extends Base
{

    protected function validateDigest($digest, $nonce, $created, $secret, $salt)
    {
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));
        
        return $digest === $expected;
    }

}
