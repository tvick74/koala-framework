<?php
class Vpc_Basic_Line_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Line')
        ));
    }
}