<?php
namespace Model;
class AuthIpRatethrottle extends Model
{
    //验证码记录最长有效期
    private $cycleTime;

    public function __construct($pkVal = '', $cycleTime = 60)
    {
        parent::__construct('auth_ip_ratethrottle');
        if ($pkVal)
            $this->initArData($pkVal);

        $this->cycleTime = $cycleTime;
    }

    //初始化并获取数据记录
    public function initIpLog($ip, $handle)
    {
        $ipLog = $this->where("`ip` = '{$ip}' and `handle` = '{$handle}'")->get()->rowArr();

        if (!empty($ipLog)) {
            $interval = time() - $ipLog['last_time'];
            if ($interval > $this->cycleTime) {
                $this->update(
                    array(
                        'counter' => 0,
                        'last_time' => time()
                    ),
                    array('id' => $ipLog['id'])
                );
            }
        } else {
            $ipLog['id'] = $this->add(array(
                'ip' => $ip,
                'handle' => $handle,
                'last_time' => time()
            ));
        }

        return $this->where(array('id' => $ipLog['id']))->get()->rowArr();
    }

    //自增发送和未验证次数
    public function increment($id)
    {
        $data = array(
            'counter' => 'counter + 1',
            'last_time' => time()
        );
        return $this->where(array('id' => $id))->upd($data);
    }

    //清空记录
    public function reset($handles)
    {
        $ip = get_client_ip();
        //转换数组形式
        $handles = is_array($handles) ? $handles : explode(' ', $handles);

        foreach ($handles as $item) {
            $this->where("ip = '{$ip}' and `handle` = '{$item}'")->upd(['counter' => 0]);
        }
        return true;
    }
}