<?php
/**
 * Author     : newiep
 * CreateTime : 19:26
 * Description: 推送相关Rpc服务
 */

namespace App\service\rpcserverimpl;

use Lib\Curl\Curl;
use App\service\Traits\Validator;
use App\service\exception\AllErrorException;

class PushRpcImpl extends BaseRpcImpl {

    use Validator;

    /**
     *  模板参数正则
     */
    const PATTERN = '/\{\{(\w+?)\}\}/';

    //短信
    public function smsMessage($params)
    {
        logs(' 请求参数：'.PHP_EOL.var_export($params, true), 'smsMessage');
        if (empty($params->phone) || !$this->validatePhone($params->phone)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '手机号不为空或格式错误');
        }
        if (empty($params->node_name)) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '模版节点不为空');
        }
        $tplParam = isset($params->tplParam) ? $params->tplParam : array();

        $smsTmpl = new \Model\SmsTemplate();
        $template = $smsTmpl->getTmplByNode($params->node_name);

        $compiledTmpl = $this->compileTmpl($template['content_tpl'], $tplParam);

        if (!$compiledTmpl) {
            throw new AllErrorException(AllErrorException::API_MIS_PARAMS, [], '模版编译失败，请检查参数');
        }

        $smslog = new \Model\SmsLog();
        $smslog->add(array(
            'mobile' => $params->phone,
            'contents' => $compiledTmpl,
            'created_at' => date("Y-m-d H:i:s")
        ));
        //发送短信
        $curlHandle = new Curl();
        $params = array(
            'userCode' => config('SERVICE_ACCOUNTS.SMS_YX.ACCOUNT'),
            'userPass' => config('SERVICE_ACCOUNTS.SMS_YX.SECRET'),
            'DesNo'    => $params->phone,
            'Msg'      => $compiledTmpl,
            'Channel'  => 0
        );
        $response = $curlHandle->post(config('SERVICE_ACCOUNTS.SMS_YX.SMS_API'), $params);
        logs('请求参数：'.PHP_EOL.var_export($params, true).PHP_EOL.'响应：'.PHP_EOL.$response, 'message');
        return array(
            'code' => 0,
            'message' => 'success'
        );
    }

    //站内信
    public function webMessage($params)
    {
    }

    //极光
    public function Jpush($params)
    {
    }

    //编译模板
    protected function compileTmpl($template, $data)
    {
        //获取所有可替换变量
        preg_match_all(self::PATTERN, $template, $matches);

        //模板中没有需要替代的参数
        if (count($matches[0]) === 0) {
            return $template;
        }

        //代替换数组
        $replace = array();

        //检查传递参数是否和被替换参数一一对应
        foreach ($matches[1] as $val) {
            if (!in_array($val, array_keys($data))) {
                return false;
            }
            //按顺序放入数组（下面替换顺序很重要）
            array_push($replace, $data[ $val ]);
        }

        //替换模板中的变量
        return str_replace($matches[0], $replace, $template);
    }
}