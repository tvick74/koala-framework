<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Intern_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Intern_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Intern_Model',
            'componentName' => trlKwf('Link.Intern'),
        ));
        $ret['assetsAdmin']['dep'][] = 'KwfPageSelectField';
        return $ret;
    }
}
