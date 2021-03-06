<?php
class Kwc_Newsletter_Subscribe_DoubleOptIn_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwf('Your E-Mail address has been verified. You will receive our newsletters in future.');
        return $ret;
    }

    public function processMailRedirectInput($recipient, $params)
    {
        $recipient->unsubscribed = 0;
        $recipient->activated = 1;
        $recipient->save();
    }
}
