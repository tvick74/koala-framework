<?php
class Vpc_Basic_Text_Link_Mail_TestComponent extends Vpc_Basic_LinkTag_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Text_Link_Mail_TestModel';
        return $ret;
    }
}