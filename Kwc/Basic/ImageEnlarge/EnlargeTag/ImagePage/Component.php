<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_ContentSender';
        $ret['assets']['dep'][] = 'KwfLightbox';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $c = $this->getData()->parent->getComponent();
        $size = $c->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];

        $ret['imageUrl'] = $c->getImageUrl();

        $ret['options'] = (object)$c->getOptions();

        $enlargeTag = $this->getData()->parent;
        $imageEnlarge = $enlargeTag->parent;
        if (is_instance_of($imageEnlarge->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $imageEnlarge = $imageEnlarge->parent;
        }
        $parent = $imageEnlarge->parent;

        $getChildren = array();
        if (is_instance_of($parent->componentClass, 'Kwc_Abstract_List_Component')) {
            //it's in an List_Gallery
        } else if ($parent->parent && is_instance_of($parent->parent->componentClass, 'Kwc_Abstract_List_Component')) {
            //it's in an List_Switch with ImageEnlarge as large component (we have to go up one more level)
            $getChildren = array('-'.$imageEnlarge->id);
            $imageEnlarge = $imageEnlarge->parent;
            $parent = $parent->parent;
        }

        //TODO optimize in generator using something like whereNextSiblingOf / wherePreviousSiblingOf
        $allImages = $parent->getChildComponents(array('componentClass'=>$imageEnlarge->componentClass));
        $previous = null;
        foreach ($allImages as $c) {
            if ($c === $imageEnlarge) {
                $ret['previous'] = self::_getImagePage($previous, $getChildren);
            }
            if ($previous === $imageEnlarge) {
                $ret['next'] = self::_getImagePage($c, $getChildren);
                break;
            }
            $previous = $c;
        }
        return $ret;
    }

    private static function _getImagePage($data, $getChildren)
    {
        if (!$data) return null;
        $ret = $data;
        foreach ($getChildren as $c) {
            $ret = $ret->getChildComponent($c);
        }
        $ret = $ret->getChildComponent('-linkTag');
        if (is_instance_of($ret->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            $ret = $ret->getChildComponent('-child');
        }
        $ret = $ret->getChildComponent('_imagePage');
        return $ret;
    }
}
