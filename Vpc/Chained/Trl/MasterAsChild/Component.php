<?php
class Vpc_Chained_Trl_MasterAsChild_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent('-child');
        return $ret;
    }

}