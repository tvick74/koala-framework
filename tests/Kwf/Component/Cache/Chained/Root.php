<?php
class Kwf_Component_Cache_Chained_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['generators']['master']['component'] = 'Kwf_Component_Cache_Chained_Master_Component';
        $ret['generators']['chained']['component'] = 'Kwf_Component_Cache_Chained_Chained_Component.Kwf_Component_Cache_Chained_Master_Component';
        $ret['childModel'] = new Kwc_Root_TrlRoot_Model(array(
            'master' => 'Master',
            'slave' => 'Slave'
        ));
        return $ret;
    }
}
