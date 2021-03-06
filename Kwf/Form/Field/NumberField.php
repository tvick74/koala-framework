<?php
/**
 * Numeric text field
 *
 * ExtJS provides automatic keystroke filtering and numeric validation.
 *
 * @package Form
 */
class Kwf_Form_Field_NumberField extends Kwf_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('numberfield');
        $this->setDecimalSeparator(trlcKwf('decimal separator', '.'));
        $this->setDecimalPrecision(2);
    }
    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getMaxValue()) {
            $this->addValidator(new Kwf_Validate_MaxValue($this->getMaxValue()));
        }
        if ($this->getMinValue()) {
            $this->addValidator(new Kwf_Validate_MinValue($this->getMinValue()));
        }
        if ($this->getAllowNegative() === false) {
            $this->addValidator(new Kwf_Validate_NotNegative());
        }
        if ($this->getAllowDecimals() === false) {
            $this->addValidator(new Kwf_Validate_Digits(true));
        } else {
            $l = null;
            if (trlcKwf('locale', 'C') != 'C') {
                $l = Zend_Locale::findLocale(trlcKwf('locale', 'C'));
            }
            $this->addValidator(new Zend_Validate_Float($l));
        }
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        if ($postData[$fieldName] == ''
            && !(is_int($postData[$fieldName]) && $postData[$fieldName] === 0)
        ) {
            $postData[$fieldName] = null;
        }
        if (!is_numeric($postData[$fieldName])) $postData[$fieldName] = null;
        if (!is_null($postData[$fieldName])) {
            if ($this->getDecimalSeparator() != '.') {
                $postData[$fieldName] = str_replace($this->getDecimalSeparator(), '.', $postData[$fieldName]);
            }
            $postData[$fieldName] = (float)$postData[$fieldName];
            $postData[$fieldName] = round($postData[$fieldName], $this->getDecimalPrecision());
        }
        return $postData[$fieldName];
    }

    protected function _getOutputValueFromValues($values)
    {
        $ret = parent::_getOutputValueFromValues($values);
        if (!$ret) return '';
        $ret = number_format($ret, $this->getDecimalPrecision(), $this->getDecimalSeparator(), '');
        return $ret;
    }

    /**
     * The maximum allowed value
     *
     * @param float
     * @return $this
     */
    public function setMaxValue($value)
    {
        return $this->setProperty('maxValue', $value);
    }

    /**
     * The minimum allowed value
     *
     * @param float
     * @return $this
     */
    public function setMinValue($value)
    {
        return $this->setProperty('minValue', $value);
    }

    /**
     * False to prevent entering a negative sign (defaults to true)
     *
     * @param bool
     * @return $this
     */
    public function setAllowNegative($value)
    {
        return $this->setProperty('allowNegative', $value);
    }

    /**
     * False to disallow decimal values (defaults to true)
     *
     * @param bool
     * @return $this
     */
    public function setAllowDecimals($value)
    {
        return $this->setProperty('allowDecimals', $value);
    }

    /**
     * Character(s) to allow as the decimal separator (default depends on current language)
     *
     * @param string
     * @return $this
     */
    public function setDecimalSeparator($value)
    {
        return $this->setProperty('decimalSeparator', $value);
    }

    /**
     * The maximum precision to display after the decimal separator (defaults to 2)
     *
     * @param int
     * @return $this
     */
    public function setDecimalPrecision($value)
    {
        return $this->setProperty('decimalPrecision', $value);
    }
}
