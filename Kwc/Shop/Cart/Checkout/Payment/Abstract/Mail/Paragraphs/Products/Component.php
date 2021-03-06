<?php
//TODO: könnte von Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component erben
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwf('Order Products');
        return $ret;
    }

    public function getMailVars(Kwc_Shop_Cart_Order $order)
    {
        $ret = parent::getMailVars($order);

        $ret['items'] = $order->getProductsData();

        $c = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component');
        $ret['sumRows'] = $c->getComponent()->getSumRows($order);

        return $ret;
    }
}
