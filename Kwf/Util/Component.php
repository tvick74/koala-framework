<?php
class Kwf_Util_Component
{
    public static function getHtmlLocations($components)
    {
        $ids = array();
        $msg = '';
        foreach ($components as $c) {
            if ($c->getPage()) {
                if (in_array($c->getPage()->componentId, $ids)) continue;
                $ids[] = $c->getPage()->componentId;
                $url = $c->getPage()->url;
                $msg .= "<br /><a href=\"$url\" target=\"_blank\">".$c->getTitle()."</a>";
            } else {
                if (in_array($c->componentId, $ids)) continue;
                $ids[] = $c->componentId;
                $t = array();
                do {
                    if (is_instance_of($c->componentClass, 'Kwc_Root_Abstract')) continue;
                    if (is_instance_of($c->componentClass, 'Kwc_Root_Category_Component')) continue;
                    if (is_instance_of($c->componentClass, 'Kwc_Root_DomainRoot_Domain_Component')) continue;
                    $n = Kwc_Abstract::getSetting($c->componentClass, 'componentName');
                    if ($n) {
                        $t[] = str_replace('.', ' ', $n);
                    }
                } while($c = $c->parent);
                if ($t) {
                    $msg .= "<br />".implode(' - ', array_reverse($t));
                } else {
                    $msg .= "<br />".trlKwf("on multiple pages, eg. in a footer");
                }
            }
        }
        return $msg;
    }

    public static function getDuplicateProgressSteps(Kwf_Component_Data $source)
    {
        return $source->generator->getDuplicateProgressSteps($source);
    }

    public static function duplicate(Kwf_Component_Data $source, Kwf_Component_Data $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        $new = $source->generator->duplicateChild($source, $parentTarget, $progressBar);

        if (!$new) {
            throw new Kwf_Exception("Failed duplicating '$source->componentId'");
        }

        Kwf_Component_Generator_Abstract::clearInstances();
        Kwf_Component_Data_Root::reset();

        //TODO: schöner wär ein flag bei den komponenten ob es diese fkt im admin
        //gibt und dann für alle admins aufrufen
        //ändern sobald es für mehrere benötigt wird
        Kwc_Root_TrlRoot_Chained_Admin::duplicated($source, $new);

        return $new;
    }

    public static function dispatchRender()
    {
        if ((!isset($_REQUEST['url']) || !$_REQUEST["url"]) && (!isset($_REQUEST['componentId']) || !$_REQUEST['componentId'])) {
            throw new Kwf_Exception_NotFound();
        }
        if (isset($_REQUEST['componentId'])) {
            $data = Kwf_Component_Data_Root::getInstance()->getComponentById($_REQUEST['componentId']);
        } else {
            $url = $_REQUEST['url'];
            $parsedUrl = parse_url($url);
            $_GET = array();
            if (isset($parsedUrl['query'])) {
                foreach (explode('&' , $parsedUrl['query']) as $get) {
                    if (!$get) continue;
                    $pos = strpos($get, '=');
                    $_GET[substr($get, 0, $pos)] = substr($get, $pos+1); //ouch
                }
            }
            $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl($url, null);
        }
        if (!$data) throw new Kwf_Exception_NotFound();
        $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent(false);

        Kwf_Benchmark::shutDown();
        exit;
    }
}
