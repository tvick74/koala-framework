<?php
class Kwc_Chained_CopyTarget_PagesGenerator extends Kwc_Root_Category_Cc_Generator
{
    protected function _getChainedGenerator()
    {
        return Kwf_Component_Generator_Abstract::getInstance('Kwc_Root_Category_Component', 'page');
    }

    protected function _getChainedData($data)
    {
        $parentClasses = Kwc_Abstract::getParentClasses($data->componentClass);
        if (in_array('Kwc_Chained_CopyTarget_Component' , $parentClasses)) {
            return $data->getComponent()->getTargetComponent();
        } else {
            return parent::_getChainedData($data);
        }
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $parentClasses = Kwc_Abstract::getParentClasses($parentData->componentClass);
        if (in_array('Kwc_Chained_CopyTarget_Component' , $parentClasses)) {
            $ret['parent'] = $parentData;
        }
        return $ret;
    }
}