<?php
/**
 * @group slow
 * @group Vpc_Columns
 */
class Vpc_Columns_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_Columns_Root');
    }

    // http://iphone.vps.niko.vivid/vps/vpctest/Vpc_Columns_Root/foo
    // http://iphone.vps.niko.vivid/vps/componentedittest/Vpc_Columns_Root/Vpc_Columns_TestComponent?componentId=3000

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_Columns_TestComponent', '3000');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }
}