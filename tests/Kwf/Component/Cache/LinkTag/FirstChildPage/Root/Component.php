<?php
class Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_LinkTag_FirstChildPage_Root_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'link' => 'Kwc_Basic_LinkTag_FirstChildPage_Component',
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['link'] = $this->getData()->getComponentById(2);
        return $ret;
    }
}