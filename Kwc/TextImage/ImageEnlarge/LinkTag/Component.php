<?php
class Kwc_TextImage_ImageEnlarge_LinkTag_Component extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_ImageEnlarge_LinkTag_Model';
        $ret['generators']['child']['component']['download'] = 'Kwc_Basic_DownloadTag_Component';
        $ret['generators']['child']['component'] = array_merge(
            array(
                'none' => 'Kwc_Basic_LinkTag_Empty_Component',
                'enlarge' => 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component'
            ),
            $ret['generators']['child']['component']
        );
        return $ret;
    }
}
