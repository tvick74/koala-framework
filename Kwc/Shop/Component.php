<?php
class Vpc_Shop_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child']['component']['products'] = 'Vpc_Shop_Products_Directory_Component';

        $ret['generators']['cart'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Component',
            'name' => trlVps('Cart'),
            'showInMenu' => true
        );

        $ret['componentName'] = trlVps('Shop');
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}