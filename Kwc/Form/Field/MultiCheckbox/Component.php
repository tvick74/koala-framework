<?php
class Kwc_Form_Field_MultiCheckbox_Component extends Kwc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Form.Multi Checkbox');
        $ret['ownModel'] = 'Kwc_Form_Field_MultiCheckbox_Model';
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_MultiCheckbox(
            Kwf_Model_Abstract::getInstance('Kwc_Form_Field_MultiCheckbox_DataToValuesModel'),
            'Value'
        );
        $ret->setName($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setHideLabel($this->getRow()->hide_label);
        $ret->setShowCheckAllLinks($this->getRow()->show_check_all_links);
        $ret->setOutputType($this->getRow()->output_type);
        $values = array();
        foreach ($this->getRow()->getChildRows('Values') as $i) {
            $values[$ret->getName().'_'.$i->id] = $i->value;
        }
        $ret->setValues($values);
        return $ret;
    }
}
