<?php
class Vpc_Trl_MenuCache_MainMenu_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        $ret['cssClass'] .= ' webListNone';

        $ret['generators']['subMenu'] = array(
            'class' => 'Vpc_Menu_Generator',
            'component' => 'Vpc_Trl_MenuCache_MainMenu_SubMenu_Component'
        );

        return $ret;
    }
}