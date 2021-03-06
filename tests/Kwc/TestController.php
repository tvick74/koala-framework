<?php
/**
 * Controller der für eigene root-komponenten verwendet werden kann.
 * für selenium-tests.
 * Url: /kwf/kwctest/Kwc_Basic_Text_Root/url
 */
class Kwc_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Zend_Registry::get('config')->debug->componentCache->disable = true;
        Zend_Registry::set('db', false);
        Kwf_Test_SeparateDb::setDbFromCookie(); // setzt es nur wenn es das cookie wirklich gibt

        //FnF models setzen damit tests nicht in echte tabellen schreiben
        Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);

        if (class_exists('APCIterator')) {
            $prefix = Kwf_Cache::getUniquePrefix();
            apc_delete_file(new APCIterator('user', '#^'.$prefix.'#'));
        } else {
            apc_clear_cache('user');
        }
        Kwf_Component_Data_Root::setComponentClass($this->_getParam('root'));
        Zend_Registry::set('testRootComponentClass', $this->_getParam('root'));
        $root = Kwf_Component_Data_Root::getInstance();
        $root->setFilename('kwf/kwctest/'.$this->_getParam('root'));

        $url = $this->_getParam('url');

        $urlParts = explode('/', $url);
        if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 7) {
                throw new Kwf_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = $urlParts[4];
            // time() wäre der 5er, wird aber nur wegen browsercache benötigt
            $filename = $urlParts[6];

            if ($checksum != Kwf_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Kwf_Exception_AccessDenied('Access to file not allowed.');
            }
            Kwf_Media_Output::output(Kwf_Media::getOutput($class, $id, $type));
        }
        if ($url == 'kwf/util/kwc/render') {
            $_REQUEST['url'] = str_replace('/'.$root->filename, '', $_REQUEST['url']);
            Kwf_Util_Component::dispatchRender();
        }

        $domain = 'http://'.Zend_Registry::get('config')->server->domain;
        $data = $root->getPageByUrl($domain.'/'.$url, null);
        if (!$data) {
            throw new Kwf_Exception_NotFound();
        }
        $root->setCurrentPage($data);
        $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent(true);


        $this->_helper->viewRenderer->setNoRender(true);
    }
}
