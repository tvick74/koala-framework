<?php
class Vpc_Basic_Table_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'), array('ignoreVisible'=>true));
        $maxColumns = Vpc_Abstract::getSetting($component->componentClass, 'maxColumns');

        $sel = new Vps_Form_Field_Select();
        $rowStyles = Vpc_Abstract::getSetting($this->_getParam('class'), 'rowStyles');
        $rowStylesSelect = array();
        foreach ($rowStyles as $k => $rowStyle) {
            $rowStylesSelect[$k] = $rowStyle['name'];
        }
        $sel->setValues($rowStylesSelect);
        $sel->setShowNoSelection(true);
        $this->_columns->add(new Vps_Grid_Column('css_style', trlVps('Style'), 100))
            ->setEditor($sel);
        $this->_columns->add(new Vps_Grid_Column_Visible());
        for ($i = 1; $i <= $maxColumns; $i++) {
            $this->_columns->add(new Vps_Grid_Column("column$i", $this->_getColumnLetterByIndex($i-1), 150))
                ->setEditor(new Vps_Form_Field_TextField());
        }
    }
}