<?php
class Kwf_Component_Abstract_ContentSender_Default extends Kwf_Component_Abstract_ContentSender_Abstract
{
    private static function _getRequestWithFiles()
    {
        $ret = $_REQUEST;
        //in _REQUEST sind _FILES nicht mit drinnen
        foreach ($_FILES as $k=>$file) {
            if (is_array($file['tmp_name'])) {
                //wenn name[0] dann kommts in komischer form daher -> umwandeln
                foreach (array_keys($file['tmp_name']) as $i) {
                    foreach (array_keys($file) as $prop) {
                        $ret[$k][$i][$prop] = $file[$prop][$i];
                    }
                }
            } else {
                $ret[$k] = $file;
            }
        }
        return $ret;
    }

    protected function _getProcessInputComponents($includeMaster)
    {
        return self::__getProcessInputComponents($this->_data);
    }

    //public for unittest
    public static function __getProcessInputComponents($data)
    {
        $showInvisible = Kwf_Config::getValue('showInvisible');

        $cacheId = 'procI-'.$data->getPageOrRoot()->componentId;
        $success = false;
        if (!$showInvisible) { //don't cache in preview
            $processCached = Kwf_Cache_Simple::fetch($cacheId, $success);
            //cache is cleared in Kwf_Component_Events_ProcessInputCache
        }
        if (!$success) {
            $process = $data
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
            if (Kwf_Component_Abstract::getFlag($data->componentClass, 'processInput')) {
                $process[] = $data;
            }

            // TODO: Äußerst suboptimal
            if (is_instance_of($data->componentClass, 'Kwc_Show_Component')) {
                $process += $data->getComponent()->getShowComponent()
                    ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
                if (Kwf_Component_Abstract::getFlag(get_class($data->getComponent()->getShowComponent()->getComponent()), 'processInput')) {
                    $process[] = $data;
                }
            }
            if (!$showInvisible) {
                $datas = array();
                foreach ($process as $p) {
                    $datas[] = $p->kwfSerialize();
                }
                Kwf_Cache_Simple::add($cacheId, $datas);
            }
        } else {
            $process = array();
            foreach ($processCached as $d) {
                $process[] = Kwf_Component_Data::kwfUnserialize($d);
            }
        }
        return $process;
    }

    protected static function _callProcessInput($process)
    {
        $postData = self::_getRequestWithFiles();
        foreach ($process as $i) {
            Kwf_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process(false);
        }
    }

    protected static function _callPostProcessInput($process)
    {
        $postData = self::_getRequestWithFiles();
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'postProcessInput')) {
                $i->getComponent()->postProcessInput($postData);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process();
        }
    }

    protected function _render($includeMaster)
    {
        return $this->_data->render(null, $includeMaster);
    }

    public function sendContent($includeMaster)
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_getProcessInputComponents($includeMaster);
        self::_callProcessInput($process);
        Kwf_Benchmark::checkpoint('processInput');
        echo $this->_render($includeMaster);
        Kwf_Benchmark::checkpoint('render');
        self::_callPostProcessInput($process);
        Kwf_Benchmark::checkpoint('postProcessInput');
    }
}
