<?php
class Kwc_Menu_Trl_Component extends Kwc_Menu_Abstract_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $menu = array();
        $masterMenu = $this->getData()->chained->getComponent()->getMenuData(null, array('ignoreVisible'=>true));
        foreach ($masterMenu as $m) {
            $component = Kwc_Chained_Trl_Component::getChainedByMaster($m['data'], $this->getData());
            if ($component) {
                $m['data'] = $component;
                $m['text'] = $component->name;
                $menu[] = $m;
            }
        }
        $ret['menu'] = $menu;
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        return $ret;
    }

    public function hasContent()
    {
        $tvars = $this->getTemplateVars();
        $c = count($tvars['menu']);
        if (Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'emptyIfSingleEntry')) {
            if ($c > 1) return true;
        } else if ($c > 0) {
            return true;
        }
        $sub = $this->getData()->getChildComponent('-subMenu');
        if ($sub && $sub->getComponent()->hasContent()) {
            return true;
        }
        return false;
    }
}
