<?php
class Kwc_Posts_Directory_Component extends Kwc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Posts');
        $ret['componentIcon'] = new Kwf_Asset('comments');
        $ret['childModel'] = 'Kwc_Posts_Directory_Model';

        $ret['generators']['detail']['class'] = 'Kwf_Component_Generator_PseudoPage_Table';
        $ret['generators']['detail']['component'] = 'Kwc_Posts_Detail_Component';
        $ret['generators']['detail']['filenameColumn'] = 'id';
        $ret['generators']['detail']['uniqueFilename'] = 'id';

        $ret['generators']['write'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Posts_Write_Component',
            'name' => trlKwfStatic('Write'),
        );
        $ret['generators']['child']['component']['view'] = 'Kwc_Posts_Directory_View_Component';
        $ret['placeholder']['writeText'] = null;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['write'] = $this->getData()->getChildComponent('_write');
        return $ret;
    }
    public function hasContent()
    {
        //der write-link ist ja immer da
        return true;
    }
}
