<?php
class Vpc_Basic_Text_TestComponent_Controller extends Vpc_Basic_Text_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Basic_Text:Test';
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setAssetsType('Vpc_Basic_Text:Test');
    }
}