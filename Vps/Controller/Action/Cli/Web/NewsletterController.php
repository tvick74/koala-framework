<?php
class Vps_Controller_Action_Cli_Web_NewsletterController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "Call by cronjob to send waiting newsletters. If called manually, Ctrl+C stops newsletter, ist has to be started again!";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'timeLimit',
                'value'=> 60,
                'valueOptional' => true,
            ),
            array(
                'param'=> 'mailsPerMinute',
                'value'=> 20,
                'valueOptional' => true,
            ),
            array(
                'param'=> 'debug',
                'value'=> true,
                'valueOptional' => true,
            )
        );
    }

    public function indexAction()
    {
        $components = Vps_Component_Data_Root::getInstance()->getComponentsByClass('Vpc_Newsletter_Component');
        if (empty($components)) return;
        $model = $components[0]->getComponent()->getChildModel();
        $model->send($this->_getParam('timeLimit'), $this->_getParam('mailsPerMinute'), $this->_getParam('debug'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}