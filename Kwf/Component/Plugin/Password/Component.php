<?php
class Kwf_Component_Plugin_Password_Component extends Kwf_Component_Plugin_View_Abstract
    implements Kwf_Component_Plugin_Interface_Login
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['password'] = 'planet';
        $ret['generators']['loginForm'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Plugin_Password_LoginForm_Component'
        );
        return $ret;
    }

    protected function _getPassword()
    {
        return Kwc_Abstract::getSetting(get_class($this), 'password');
    }

    protected function _getLoginPassword()
    {
        return isset($_POST['login_password']) ? $_POST['login_password'] : null;
    }

    protected function _saveCookie()
    {
        setcookie(get_class($this), sha1($this->_getLoginPassword()), time()+60*60*24*365);
    }

    /**
     * @deprecated
     */
    public final function isLoggedId() { return $this->isLoggedIn(); }

    public function isLoggedIn()
    {
        $pw = $this->_getPassword();
        if (!is_array($pw)) $pw = array($pw);

        if (isset($_COOKIE[get_class($this)])) {
            foreach ($pw as $p) {
                if (sha1($p) == $_COOKIE[get_class($this)]) {
                    $this->_saveCookie();
                    return true;
                }
            }
        }

        $msg = '';
        $session = new Zend_Session_Namespace('login_password');
        if (in_array($this->_getLoginPassword(), $pw)) {
            $session->login = true;
            $currentPageUrl = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId)->url;
            header('Location: '.$currentPageUrl);
            die();
        }
        return $session->login;
    }

    public function getTemplateVars()
    {
        $templateVars = array();
        $templateVars['loginForm'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))->getChildComponent('-loginForm');
        $templateVars['wrongLogin'] = isset($_POST['login_password']);
        $templateVars['placeholder'] = Kwc_Abstract::getSetting(get_class($this), 'placeholder');
        return $templateVars;
    }

    public function processOutput($output)
    {
        //TODO: nicht auf $_POST zugreifen sondern loginForm->getFormRow()
        if ($this->isLoggedIn()) {
            if (isset($_POST['save_cookie']) && $_POST['save_cookie']) {
                $this->_saveCookie();
            }
            return $output;
        }

        $template = Kwc_Admin::getComponentFile($this, 'Component', 'tpl');

        $renderer = new Kwf_Component_Renderer();
        $view = new Kwf_Component_View($renderer);
        $view->assign($this->getTemplateVars());
        return $renderer->render($view->render($template));
    }
}
