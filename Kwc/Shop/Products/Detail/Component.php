<?php
class Kwc_Shop_Products_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Kwc_Paragraphs_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_Image_Component';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['generators']['addToCart'] = array(
            'class' => 'Kwc_Shop_Products_Detail_AddToCartGenerator',
            'component' => array(
                'product' => 'Kwc_Shop_AddToCart_Component' // Hier kann man mehrere angeben
            ),
            'column' => 'component'
        );
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['back'] = trlKwf('Back');
        $ret['assetsAdmin']['dep'][] = 'KwfFormDateTimeField';
        $ret['editComponents'] = array('content');
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item)
    {
        $item->previewImage = $item->getChildComponent('-image');
        $item->previewText = $item->getChildComponent('-text');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['addToCart'] = $this->getData()->getChildComponent('-addToCart');
        return $ret;
    }
}
