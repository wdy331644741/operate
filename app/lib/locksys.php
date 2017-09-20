<?php
namespace Lib;
class LockSys
{

	//const LOCK_TYPE_DB = 'SQLLock';  
    const LOCK_TYPE_FILE = '\Lib\FileLock';
    const LOCK_TYPE_REDIS = '\Lib\RedisLock';

	private $_lock = null;  
    private static $_supportLocks = array('FileLock', 'SQLLock', 'RedisLock'); 



	public function __construct($type, $options = array())   
    {  
        if(false == empty($type))
        {
            $this->createLock($type, $options);
        }
    }

    public function createLock($type, $options=array())
    {
        $this->_lock = new $type();//实例化 锁
        // $this->_lock = new RedisLock();//实例化 锁
    }

    public function getLock($key, $timeout = ILock::EXPIRE)
    {  
        if (false == $this->_lock instanceof ILock)
        {
            throw new Exception('false == $this->_lock instanceof ILock');
        }
        $this->_lock->getLock($key, $timeout);
    }  

    public function releaseLock($key)  
    {  
        if (false == $this->_lock instanceof ILock)
        {
            throw new Exception('false == $this->_lock instanceof ILock');
        }
        $this->_lock->releaseLock($key);
    }
}


interface ILock
{  
    const EXPIRE = 5;
    public function getLock($key, $timeout=self::EXPIRE);
    public function releaseLock($key);
}

//redis 锁
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
        // while ($totalWaitime < $time && false == $this->memcache->add($key, 1, $timeout))
        while ($totalWaitime < $time && false == $this->redis->set($key, 1, array('nx','ex'=> $timeout) ))   
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

//文件锁
class FileLock implements ILock
{  
    private $_fp;  
    private $_single;  
  
    public function __construct($options)  
    {  
        if (isset($options['path']) && is_dir($options['path']))  
        {  
            $this->_lockPath = $options['path'].'/';  
        }  
        else  
        {  
            $this->_lockPath = '/tmp/';  
        }  
         
        $this->_single = isset($options['single'])?$options['single']:false;  
    }  
  
    public function getLock($key, $timeout=self::EXPIRE)  
    {  
        $startTime = Timer::getTimeStamp();  
  
        $file = md5(__FILE__.$key);  
        $this->fp = fopen($this->_lockPath.$file.'.lock', "w+");  
        if (true || $this->_single)  
        {  
            $op = LOCK_EX + LOCK_NB;  
        }  
        else  
        {  
            $op = LOCK_EX;  
        }  
        if (false == flock($this->fp, $op, $a))  
        {  
            throw new Exception('failed');  
        }  
         
        return true;  
    }  
  
    public function releaseLock($key)  
    {  
        flock($this->fp, LOCK_UN);  
        fclose($this->fp);  
    }  
}




?>