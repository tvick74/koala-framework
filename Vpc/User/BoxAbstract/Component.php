<?php
class Vpc_User_BoxAbstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Vps_Registry::get('userModel')->getAuthedUser();
        return $ret;
    }


    public function preProcessInput($postData)
    {
        if (isset($postData['feAutologin'])
            && !Vps_Registry::get('userModel')->getAuthedUser()
        ) {
            list($cookieId, $cookieMd5) = explode('.', $postData['feAutologin']);
            if (!empty($cookieId) && !empty($cookieMd5)) {
                $result = $this->_getAuthenticateResult($cookieId, $cookieMd5);
            }
        }

        if (isset($postData['logout'])) {
            Vps_Auth::getInstance()->clearIdentity();
            setcookie('feAutologin', '', time() - 3600);
        }
    }


    private function _getAuthenticateResult($identity, $credential)
    {
        $adapter = new Vps_Auth_Adapter_Service();
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $auth = Vps_Auth::getInstance();
        $auth->clearIdentity();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $auth->getStorage()->write(array(
                'userId' => $adapter->getUserId()
            ));
        }

        return $result;
    }
}