<?php
class Vps_Model_Select_Expr_Sum implements Vps_Model_Select_Expr_Interface
{
    private $_field;
    public function __construct($field)
    {
        $this->_field = $field;
    }
    public function getField()
    {
        return $this->_field;
    }

    public function validate()
    {
        $this->_field->validate();
    }
}