<?php
class Vps_Form_Field_Static extends Vps_Form_Field_Abstract
{
    //setText

    public function __construct($text, $fieldLabel = null)
    {
        parent::__construct(null, $fieldLabel);
        $this->setXtype('staticfield');
        $this->setText($text);
    }

    public function setFieldLabel($v)
    {
        $this->setLabelSeparator($v ? ':' : '');
        return $this->setProperty('fieldLabel', $v);
    }

}