<?php
class Vps_Component_Generator_Unique_Page extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box2'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'priority' => 3,
            'box' => 'box'
        );
        return $ret;
    }

}