<?php
class Kwf_Component_ContentWidthBox_PageWithEmptyBox_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['testBox'] = array(
            'component' => 'Kwc_Basic_Empty_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
