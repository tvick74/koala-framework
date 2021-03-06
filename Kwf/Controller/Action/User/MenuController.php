<?php
class Kwf_Controller_Action_User_MenuController extends Kwf_Controller_Action
{
    public function jsonDataAction()
    {
        $showLogout = true;
        $acl = $this->_getAcl();
        $menus = $acl->getMenuConfig($this->_getAuthData());

        if (empty($menus) && $this->_getUserRole() == 'guest') {
            $menu = array();
            $menu['type'] = 'commandDialog';
            $menu['menuConfig']['text'] = 'Login';
            $menu['commandClass'] = 'Kwf.User.Login.Dialog';
            $menus[] = $menu;
            $showLogout = false;
        }

        foreach ($acl->getAllResources() as $resource) {
            if ($resource instanceof Kwf_Acl_Resource_UserSelf
                && $acl->isAllowedUser($this->_getAuthData(), $resource, 'view')
            ) {

                $this->view->userSelfControllerUrl = $resource->getControllerUrl();
                break;
            }
        }

        $authData = $this->_getAuthData();

        $this->view->menus = $menus;
        $this->view->showLogout = $showLogout;
        $this->view->userId = $authData ? $authData->id : 0;
        $this->view->fullname = $authData ? $authData->__toString() : '';

        $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        $this->view->changeUser = $acl->isAllowed($role, 'kwf_user_changeuser', 'view');

        if (Kwf_Registry::get('acl')->has('kwf_component_pages')) {
            $this->view->hasFrontend = true;
        } else {
            $this->view->hasFrontend = false;
        }
    }
}
