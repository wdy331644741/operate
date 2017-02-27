<?php
namespace Model;
class MarginRefundCounter extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('margin_refund_counter');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    //获取用户某月免手续费次数
    public function getCounter($userId, $date)
    {
        $formatData = $this->formatDate($date);

        $result = $this->where(array('user_id' => $userId, 'date' => $formatData))->get()->rowArr();
        if (empty($result)) {
            return 0;
        }
        return $result['counter'];
    }

    //自增免手续费次数
    public function incrementCounter($userId, $date)
    {
        $formatData = $this->formatDate($date);

        $result = $this->where(array('user_id' => $userId, 'date' => $formatData))->get()->rowArr();
        if (empty($result)) {
            $data = array(
                'user_id' => $userId,
                'date' => $date,
                'counter' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            );
            $res = $this->add($data);
        } else {
            $data = array(
                'counter' => $result['counter'] + 1,
                'update_time' => date('Y-m-d H:i:s')
            );
            $res = $this->where(array('id' => $result['id']))->upd($data);
        }
        //清除用户信息缓存
        invalidUserProfileCache($userId);

        return $res;
    }

    //自减免手续费次数
    public function decrementCounter($userId, $date)
    {
        $formatData = $this->formatDate($date);

        $result = $this->where(array('user_id' => $userId, 'date' => $formatData))->get()->rowArr();
        if (!empty($result)) {
            $data = array(
                'counter' => $result['counter'] - 1,
                'update_time' => date('Y-m-d H:i:s')
            );
            return $this->where(array('id' => $result['id']))->upd($data);
        }
    }

    //格式化日期（Y-m）
    protected function formatDate($date)
    {
        return date("Y-m", strtotime($date));
    }
}