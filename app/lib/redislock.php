<?php
namespace Lib;
use Lib\ILock;
class RedisLock implements ILock
{  
    public function __construct()  
    {
        $this->redis = getReidsInstance();
        // $this->memcache = new Memcache();  
    }  
  
    public function getLock($key, $timeout=self::EXPIRE)  
    {       
        $waitime = 20000;  
        $totalWaitime = 0;  
        $time = $timeout*1000000;
        while ($totalWaitime < $time && false == $this->redis->add($key, 1, $timeout))   
        {  
            usleep($waitime);  
            $totalWaitime += $waitime;  
        }  
        if ($totalWaitime >= $time)  
            throw new Exception('can not get lock for waiting '.$timeout.'s.');  
  
    }  
  
    public function releaseLock($key)  
    {  
        $this->redis->delete($key);  
    }  
}

?>