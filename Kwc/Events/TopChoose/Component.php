<?php
class Kwc_Events_TopChoose_Component extends Kwc_News_TopChoose_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Events.Top');
        $ret['componentIcon'] = new Kwf_Asset('date');
        $ret['showDirectoryClass'] = 'Kwc_Events_Directory_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Events_List_View_Component';
        return $ret;
    }
}
