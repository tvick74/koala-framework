<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component extends Vpc_Mail_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Component';
        $ret['recipientSources'] = array(
            'ord' => 'Vpc_Shop_Cart_Orders'
        );
        return $ret;
    }

    public function getNameForEdit()
    {
        return trlVps('Shop Confirmation Text') . ' ' . Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName');
    }

    public function getPlaceholders(Vpc_Shop_Cart_Order $o)
    {
        $ret = parent::getPlaceholders($o);
        $ret = array_merge($ret, $o->getPlaceholders());
        return $ret;
    }
}