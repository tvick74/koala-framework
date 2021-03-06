<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRowsWithMirrorCacheSimple
 * @group slow
 */
class Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');

        $m->initialSync(false);

        $this->assertEquals(1, $m->getProxyModel()->getCollection()->find()->count());
        $row = $m->getProxyModel()->getCollection()->findOne();
        $this->assertEquals('bar', $row['foo']);
        $this->assertEquals(1, count($row['children']));
        $this->assertEquals('blub', $row['children'][0]['blub']);
    }

    public function testParentExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');
        $m->initialSync(false);
        $row = $m->getRow(1);
        $this->assertEquals('bar', $row->getChildRows('Children')->current()->parent_foo);
    }

    public function testParentExprIsntCached()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel');

        $m->initialSync(false);
        $row = $m->getProxyModel()->getCollection()->findOne();
        $this->assertTrue(!isset($row['children'][0]['parent_foo']));
    }
}