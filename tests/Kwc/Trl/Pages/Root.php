<?php
class Vpc_Trl_Pages_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Vpc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English',
            'it' => 'Italiano'
        ));
        $ret['generators']['master']['component'] = 'Vpc_Trl_Pages_Master';
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Chained_Component.Vpc_Trl_Pages_Master';
        return $ret;
    }
}