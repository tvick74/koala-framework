<?php
class Kwc_Composite_Links_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Basic_Link_Component';
        $ret['componentName'] = trlKwf('Links');
        $ret['componentIcon'] = new Kwf_Asset('links');
        $ret['childModel'] = 'Kwc_Composite_Links_Model';
        $ret['cssClass'] = 'webStandard';

        return $ret;
    }
}
