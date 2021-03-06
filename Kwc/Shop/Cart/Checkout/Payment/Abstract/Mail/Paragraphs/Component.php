<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Component
    extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array(
            'textImage' => 'Kwc_TextImage_Component',
            'address' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Address_Component',
            'products' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component',
            'message' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Message_Component'
        );
        return $ret;
    }
}
