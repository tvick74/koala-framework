<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['text'] =
            'Kwc_List_ChildPages_Teaser_TeaserImage_Text_Component';
        $ret['generators']['child']['component']['image'] =
            'Kwc_List_ChildPages_Teaser_TeaserImage_Image_Component';
        $ret['componentName'] = trlKwf('Teaser image');
        $ret['cssClass'] = 'webStandard';
        $ret['ownModel'] = 'Kwc_List_ChildPages_Teaser_TeaserImage_Model';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'visible';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['readMoreLinktext'] = $this->getRow()->link_text;
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        if (isset($this->getData()->targetPage->row)) {
            $ret[] = new Kwf_Component_Cache_Meta_Component($this->getData()->targetPage);
        }
        return $ret;
    }
}
