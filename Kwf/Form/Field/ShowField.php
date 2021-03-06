<?php
/**
 * @package Form
 */
class Kwf_Form_Field_ShowField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('showfield');
    }
    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
    }
    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $ret = parent::getTemplateVars($values);
        //todo: escapen
        $ret['id'] = $name.$fieldNamePostfix;
        if ($this->getShowText()) {
            throw new Kwf_Exception("ShowField shows a field of a row, but no static text set by 'setShowText'. Use Kwf_Form_Field_Panel instead.");
        }

        $ret['html'] = '&nbsp;';
        if (isset($values[$name]) && $values[$name] != '') {
            $ret['html'] = '<span class="fieldContent">'.$values[$name].'</span>';
        }
        return $ret;
    }
}
