<?php
class Kwc_Trl_Menu_LevelMenu_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 2;
        unset($ret['dataModel']);
        return $ret;
    }
}
