<?php
class Kwc_User_Register_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwf('Your Account has been created successfully. You will receive an confirmation e-mail soon.');
        return $ret;
    }

}
