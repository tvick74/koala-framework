<?php
class Vps_Component_View_Helper_Mail extends Vps_Component_View_Renderer
{
    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);
        $template = Vpc_Admin::getComponentFile($component->componentClass, "Mail.{$config['type']}", 'tpl');
        if (!$template) {
            $template = Vpc_Admin::getComponentFile($component->componentClass, 'Component', 'tpl');
        }
        $vars = $component->getComponent()->getMailVars($this->_getRenderer()->getRecipient());
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getMailVars() returns null. Maybe forgot "return $ret?"');
        }
        $view = new Vps_Component_View($this->_getRenderer());
        $view->assign($vars);
        return $view->render($template);
    }
}