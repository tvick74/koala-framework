<?php
class Vpc_ListChildPages_Teaser_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Vpc_ListChildPages_Teaser_Generator',
            'component' => 'Vpc_ListChildPages_Teaser_TeaserImage_Component'
        );
        $ret['childModel'] = 'Vpc_ListChildPages_Teaser_Model';

        $ret['componentName'] = trlVps('List child pages');
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/ListChildPages/Teaser/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }
}