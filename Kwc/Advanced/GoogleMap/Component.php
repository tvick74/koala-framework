<?php
class Kwc_Advanced_GoogleMap_Component extends Kwc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwf('Google Maps'),
            'ownModel' => 'Kwc_Advanced_GoogleMap_Model',
            'default' => array(
                'zoom' => 8,
                'height' => 300
            ),
        ));
        $ret['assetsAdmin']['dep'][] = 'KwfGoogleMapField';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['placeholder']['noCoordinates'] = trlKwf('coordinates not entered');
        return $ret;
    }

    protected function _getOptions()
    {
        $row = $this->_getRow();
        $fields = array('coordinates', 'zoom', 'width', 'height', 'zoom_properties',
                        'scale', 'satelite', 'overview', 'routing');
        foreach ($fields as $f) {
            $ret[$f] = $row->$f;
        }
        if (!isset($ret['coordinates'])) $ret['coordinates'] = '';
        return $ret;
    }
}
