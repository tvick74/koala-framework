<?php
/**
 * @group Basic_ImagePosition
 * @group Image
 *
 * Testet vorallem das Vps_Component_FieldModel Model
 */
class Vpc_Basic_ImagePosition_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Basic_ImagePosition_Root');
        $this->_root->setFilename(null);
    }

    public function testTemplateVars()
    {
        $c = $this->_root->getComponentById('1900');
        $vars = $c->getComponent()->getTemplateVars();
        $this->assertEquals('right', $vars['row']->image_position);
        $this->assertEquals('1900-image', $vars['image']->componentId);
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById('1900')->render();
        $this->assertRegExp('#^\s*<div class="vpcBasicImagePosition vpcBasicImagePositionTestComponent">'.
            '\s*<div class="posright">'
            .'\s*<div class="vpcBasicImagePositionImageTestComponent">'
            .'\s*<img src="/media/Vpc_Basic_ImagePosition_Image_TestComponent/1900-image/default/[^/]+/[0-9]+/foo.png" width="16" height="16" alt="" />'
            .'\s*</div>\s*</div>'.
            '\s*</div>\s*$#ms', $html);
    }
}