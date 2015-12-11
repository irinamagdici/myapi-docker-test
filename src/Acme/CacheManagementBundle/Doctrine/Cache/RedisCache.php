<?php

namespace Acme\CacheManagementBundle\Doctrine\Cache;

use Snc\RedisBundle\Doctrine\Cache\RedisCache as Base;

class RedisCache extends Base
{

    public function find($pattern) {
        $keys = $this->_redis->keys($pattern);
        $finalKeys = array();

        if(!empty($keys)) {
            for($i=0;$i<count($keys);$i++) {
                preg_match_all("/\[([^\]]*)\]/", $keys[$i], $matches);
                $finalKeys[] = $matches[1][0];
            }
        }

        return $finalKeys;
    }

}
