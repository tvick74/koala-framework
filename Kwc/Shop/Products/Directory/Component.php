<?php
class Kwc_Shop_Products_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Shop_Products_View_Component';

        $ret['generators']['detail']['class'] = 'Kwc_Shop_Products_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Shop_Products_Detail_Component';
        $ret['generators']['detail']['dbIdShortcut'] = 'shopProducts_';

        $ret['generators']['addToCart'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Shop_Products_Directory_AddToCart_Component'
        );

        $ret['childModel'] = 'Kwc_Shop_Products';

        $ret['componentName'] = trlKwf('Shop.Products');
        $ret['flags']['hasResources'] = true;

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';
        return $ret;
    }
}
