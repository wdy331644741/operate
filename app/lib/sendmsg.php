<?php
/**
 * Created by PhpStorm.
 * User: wanglin
 * Date: 16/7/7
 * Time: 下午6:07
 */

namespace Lib;

class sendmsg
{
    private $data = array();



    public function send($phone,$node='custom',$data=array(),$custom='')
    {
        $res = \App\service\rpcserverimpl\Common::sendMessage($phone,$node,$data);
        $result = !empty($res['result']) ? $res['result'] : $res['error'];


        if($result['message'] == 'success'){
            return true;
        }

        return false;
    }



    public function curlPost($url,$headers,$data,$arr)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//        curl_setopt($ch,CURLOPT_HEADER,$headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        
        $output = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($output,true);



        return $arr['result']['data'];




    }
}