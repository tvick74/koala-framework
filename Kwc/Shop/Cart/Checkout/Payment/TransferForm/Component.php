<?php
class Kwc_Shop_Cart_Checkout_Payment_TransferForm_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Transfer Form');
        return $ret;
    }
}
