<?php
class Vps_Form_CheckboxFieldsetInCards_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_CheckboxFieldsetInCards_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $cards = $this->_form->add(new Vps_Form_Container_Cards('cards', "Foo"));
        $cards->setCombobox(new Vps_Form_Field_Radio('cards', 'Foo'));

        $card0 = $cards->add();
        $card0->setName('card1');
        $card0->setTitle('Card1');

        $card1 = $cards->add();
        $card1->setName('card2');
        $card1->setTitle('Card2');

        $fs = $card1->add(new Vps_Form_Container_FieldSet("Bar"))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs2');
        $fs->add(new Vps_Form_Field_TextField("text", "Text"))
            ->setAllowBlank(false);

    }

    public function indexAction()
    {
        $config = $this->_form->getProperties();
        if (!$config) { $config = array(); }
        $config['baseParams']['id'] = 1;
        $config = array_merge(
            $config,
            array(
                'controllerUrl' => $this->getRequest()->getPathInfo(),
                'assetsType' => 'Vps_Form_CheckboxFieldsetInCards:Test',
            )
        );
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}
