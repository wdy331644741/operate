<?php

namespace Lib;

class ParseUA {

    /**
     * 来源平台（iOS android H5 PC）
     * @var
     */
    protected $platform;

    /**
     * 系统 （iOS， android， Pc）
     * @var
     */
    protected $system;

    /**
     * user agent
     * @var string
     */
    protected $userAgent;

    /**
     * @var
     */
    protected $parseInfo;

    public function parse($userAgent)
    {
        $this->userAgent = $userAgent;

        //匹配系统
        $pattern = "/Android|iPhone|iPad|wlbapp|MicroMessenger/imx";
        preg_match_all($pattern, $userAgent, $result, PREG_PATTERN_ORDER);

        //大小写转换
        $this->parseInfo = array_map(function ($val) {
            return strtoupper($val);
        }, $result[0]);

        return $this;
    }

    public function getPlatform()
    {
        if ($this->in_array_mul(array('IPHONE', 'IPAD'), $this->parseInfo)) {
            //ios
            if (in_array('WLBAPP', $this->parseInfo)) {
                $platform = "IOS";
            } else {
                $platform = "H5";
            }
        } elseif (in_array('ANDROID', $this->parseInfo)) {
            //ios
            if (in_array('WLBAPP', $this->parseInfo)) {
                $platform = "ANDROID";
            } else {
                $platform = "H5";
            }
        } else {
            $platform = "PC";
        }

        return $platform;
    }

    public function getSystem()
    {
        if ($this->in_array_mul(array('IPHONE', 'IPAD'), $this->parseInfo)) {
            $system = "IOS";

        } elseif (in_array('ANDROID', $this->parseInfo)) {
            $system = "ANDROID";

        } else {
            $system = "PC";
        }

        return $system;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function getParseInfo()
    {
        return array(
            'user_agent' => $this->userAgent,
            'platform'   => $this->getPlatform(),
            'system'   => $this->getSystem()
        );
    }

    //在haystack_arr 查找是否存在needle_arr中的某个值
    protected function in_array_mul($needle_arr, $haystack_arr)
    {
        return count(array_intersect($needle_arr, $haystack_arr));
    }
}