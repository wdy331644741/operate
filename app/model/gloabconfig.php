<?php
namespace Model;
class GloabConfig extends Model
{	
	private $OPERATE_GLOAB_CONF = 'operate_gloab_conf';
    public function __construct($pkVal = '')
    {
        parent::__construct('gloab_config');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function getConfigByKey($key){
    	$res = $this->where("`status` = 1")->whereIn('key', $key)->get()->resultArr();
    	
    	$getRedis = getReidsInstance();
    	foreach ($res as $value) {
    		# code...
    		//更新redis hash表
    		$key = $value['key'];
    		$value = $value['value'];
    		$getRedis->hset($this->OPERATE_GLOAB_CONF,$key,$value );
    	}
    	// return $this->where("`status` = 1")->whereIn('key', $key)->get()->resultArr();
    	return $res;
    }

    public function redisToDb($key,$value){
        //exit($value);
        $hasKey = $this->isSetKey($key);
        // var_dump($hasKey);exit;
        if($hasKey){
            //upd
            return $this->where(['id' => $hasKey])->upd(['value' => $value]);
        }else{
            //add
            $this->key = $key;
            $this->value = stripslashes($value);

            $res = $this->save();
            return $res;
        }
        // return $this->where(['key' => $key])->upd(['value' => $value]);
    }

    private function isSetKey($key){
        $res = $this->where(['key' => $key])->get()->rowArr();
        if(!empty($res)){
            return $res['id'];
        }else{
            return false;
        }
    }
}