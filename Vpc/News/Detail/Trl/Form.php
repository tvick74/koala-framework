<?php
class Vpc_News_Detail_Trl_Form extends Vpc_News_Detail_Abstract_Trl_Form
{
    protected function _init()
    {
        parent::_init();
        //$this->add(Vpc_Abstract_Form::createComponentForm('news_{0}-image'));
        $detail = Vpc_Abstract::getChildComponentClass($this->getDirectoryClass(), 'detail');
        $this->add(Vpc_Abstract_Form::createChildComponentForm($detail, '-image', 'image'));
    }
}