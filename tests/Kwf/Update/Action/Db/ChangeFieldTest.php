<?php
/**
 * @group Update_Action
 */
class Kwf_Update_Action_Db_ChangeFieldTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass(false);
    }

    public function testChangeField()
    {
        $model = new Kwf_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Kwf_Update_Action_Db_ChangeField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->type = 'int';
        $a->default = 5;
        $a->silent = true;
        $a->update();

        $rows = $a->model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $rows = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('bar'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('bar', $row->field);
        $this->assertEquals('int', $row->type);
        $this->assertEquals(5, $row->default);
    }

    public function testChangeFieldDefaultNull()
    {
        $model = new Kwf_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Kwf_Update_Action_Db_ChangeField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->null = true;
        $a->default = null;
        $a->silent = true;
        $a->update();

        $rows = $a->model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $rows = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('bar'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('bar', $row->field);
        $this->assertEquals('text', $row->type);
        $this->assertEquals(true, $row->null);
        $this->assertEquals(null, $row->default);
    }

    public function testChangeFieldDontChangeDefault()
    {
        $model = new Kwf_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Kwf_Update_Action_Db_ChangeField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->silent = true;
        $a->update();

        $row = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('bar'))->current();
        $this->assertEquals('bar', $row->field);
        $this->assertEquals('text', $row->type);
        $this->assertEquals('5', $row->default);
    }
}
