<?php
class Vps_Component_Generator_Components_Flag extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        return $ret;
    }
}
?>