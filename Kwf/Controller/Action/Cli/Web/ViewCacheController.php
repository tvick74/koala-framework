<?php
class Kwf_Controller_Action_Cli_Web_ViewCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelpOptions()
    {
        $ret = array();
        $ret[] = array('param' => 'componentId');
        $ret[] = array('param' => 'domain');
        return $ret;
    }

    public static function getHelp()
    {
        return "Various view cache commands";
    }

    public function generateOneAction()
    {
        Zend_Session::start(true);
        $ids = $this->_getParam('componentId');

        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $this->_generate($id);
        }
        exit;
    }

    /*
    private function _generate($componentId)
    {
        $domain = $this->_getParam('domain');
        if (!$domain) $domain = Kwf_Registry::get('config')->server->domain;
        $login = Kwf_Registry::get('config')->preLogin ? 'vivid:planet@' : '';
        $url = 'http://' . $login . $domain . '/kwf/util/render/render?componentId=' . $componentId;
        echo "$url: ";
        //$b = Kwf_Benchmark::start('render');
        $content = file_get_contents($url);
        //$b->stop();
        echo round(strlen($content) / 1000, 2) . 'KB';
        echo " rendered\n";
    }
    */
    private function _generate($componentId)
    {
        echo $componentId.' ';
        $renderer = new Kwf_Component_Renderer();
        $renderer->setEnableCache(true);
        try {
            $content = $renderer->renderComponent(Kwf_Component_Data_Root::getInstance()->getComponentById($componentId));
        } catch (Kwf_Exception $e) {
            echo $e->getMessage().' ';
            $content = '';
        }
        echo round(strlen($content) / 1000, 2) . 'KB';
        echo " rendered\n";
    }

    public function generateAction()
    {
        if ($this->_getParam('componentId')) $this->generateOneAction();

        $queueFile = 'temp/viewCacheGenerateQueue';
        $processedFile = 'temp/viewCacheGenerateProcessed';

        $componentId = 'root';
        file_put_contents($processedFile, $componentId);
        file_put_contents($queueFile, $componentId);
        while(true) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new Kwf_Exception("fork failed");
            } else if ($pid) {
                //parent process
                pcntl_wait($status); //Schützt uns vor Zombie Kindern
                if ($status != 0) {
                    throw new Kwf_Exception("child process failed");
                }

                //echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
                if (!file_get_contents($queueFile)) {
                    echo "fertig.\n";
                    break;
                }
            } else {

                Zend_Session::start(true);
                while (true) {
                    //child process

                    //echo "memory_usage (child): ".(memory_get_usage()/(1024*1024))."MB\n";
                    if (memory_get_usage() > 50*1024*1024) {
                        echo "new process...\n";
                        break;
                    }

                    $queue = file_get_contents($queueFile);
                    if (!$queue) break;

                    $queue = explode("\n", $queue);
                    //echo "queued: ".count($queue)."\n";
                    $componentId = array_shift($queue);
                    file_put_contents($queueFile, implode("\n", $queue));

                    //echo "==> ".$componentId.' ';
                    $page = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId);
                    if (!$page) continue;
                    //echo "$page->url\n";
                    foreach ($page->getChildPseudoPages(array(), array('pseudoPage'=>false)) as $c) {
                        //echo "queued $c->componentId\n";
                        if (!in_array($c->componentId, file($processedFile))) {
                            file_put_contents($processedFile, "\n".$c->componentId, FILE_APPEND);
                            $queue[] = $c->componentId;
                            file_put_contents($queueFile, implode("\n", $queue));
                        }
                    }

                    if (!$page->isPage) continue;
                    if (is_instance_of($page->componentClass, 'Kwc_Abstract_Feed_Component')) continue;

                    $this->_generate($page->componentId);
                }
                //echo "child finished\n";
                exit(0);
            }
        }
        exit;
    }
}
