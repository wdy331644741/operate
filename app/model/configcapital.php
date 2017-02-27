<?php
namespace Model;
class ConfigCapital extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('config_capital');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    public function formatAllSettings()
    {
        $settings = $this->fields('name, config_value')->get()->resultArr();
        return array_column($settings, 'config_value', 'name');
    }
}