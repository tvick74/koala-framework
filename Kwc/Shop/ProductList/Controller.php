<?php
class Kwc_Shop_ProductList_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    public function _initFields()
    {
        parent::_initFields();

        $productTypes = array();
        $productsModel = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (is_instance_of($class, 'Kwc_Shop_Products_Detail_Component')) {
                $generators = Kwc_Abstract::getSetting($class, 'generators');
                foreach ($generators['addToCart']['component'] as $key => $c) {
                    $productTypes[$key] = Kwc_Abstract::getSetting($c, 'productTypeText');
                }
            }
            if (is_instance_of($class, 'Kwc_Shop_Products_Directory_Component')) {
                $productsModel = Kwf_Model_Abstract::getInstance(
                    Kwc_Abstract::getSetting($class, 'childModel')
                );
            }
        }

        $cards = $this->_form->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')));
        $cards->getCombobox()
            ->setShowNoSelection(true)
            ->setEmptyText(trlKwf('All'));
        foreach ($productTypes as $key => $title) {
            $card = $cards->add();
            $card->setName($key);
            $card->setTitle($title);
            $select = $productsModel->select()
                ->whereEquals('component', $key)
                ->order('pos');
        }
    }
}
