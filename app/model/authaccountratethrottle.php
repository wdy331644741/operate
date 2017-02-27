<?php
namespace Model;
class AuthAccountRatethrottle extends Model
{
    //记录有效期
    private $cycleTime;

    public function __construct($pkVal = '', $cycleTime = 10800)
    {
        parent::__construct('auth_account_ratethrottle');
        if ($pkVal)
            $this->initArData($pkVal);

        $this->cycleTime = $cycleTime;
    }

    //获取当前失败次数
    public function currentFailedCounter($userId, $item)
    {
        $result = $this->initLimitLog($userId);
        //要验证的字段
        $counterField = $this->getCounterField($item);
        $timeField = $this->getLastTimeField($item);

        if (time() - $result[$timeField] > $this->cycleTime) {
            $data[$counterField] = 0;
            $data[$timeField] = time();

            $this->update($data, array('id' => $result['id']));
            return 0;
        }

        return $result[$counterField];
    }

    //失败次数自增
    public function incrementFailedCounter($userId, $item)
    {
        $log = $this->initLimitLog($userId);
        $counterField = $this->getCounterField($item);
        $timeField = $this->getLastTimeField($item);

        //自增数据
        $data[$counterField] = "{$counterField} + 1";
        $data[$timeField] = time();

        return $this->where(array('id' => $log['id']))->upd($data);
    }

    //重置失败次数
    public function resetFailedCounter($userId, $items)
    {
        //转换数组形式
        $handles = is_array($items) ? $items : explode(' ', $items);

        foreach ($handles as $item) {
            $counterField = $this->getCounterField($item);
            $data[$counterField] = 0;
        }

        return $this->update($data, array('user_id' => $userId));
    }

    //次数检查
    public function checkedCounter($userId, $item, $maxNum = 3)
    {
        $currentC = $this->currentFailedCounter($userId, $item);
        return $currentC < $maxNum;
    }

    //获取操作项的计数字段
    private function getCounterField($item)
    {
        return $item . '_failed_count';
    }

    //获取操作项的最后一次操作时间戳字段
    private function getLastTimeField($item)
    {
        return $item . '_last_failed_time';
    }

    //初始化数据
    private function initLimitLog($userId)
    {
        $result = $this->where(array('user_id' => $userId))->get()->rowArr();
        if (empty($result)) {
            $id = $this->add(array('user_id' => $userId));
            return array(
                'id' => $id,
                'user_id' => $userId,
                'trade_pwd_failed_count' => 0,
                'trade_pwd_last_failed_time' => 0,
                'login_failed_count' => 0,
                'login_last_failed_time' => 0,
                'identify_failed_count' => 0,
                'identify_last_failed_time' => 0
            );
        }
        return $result;
    }

}