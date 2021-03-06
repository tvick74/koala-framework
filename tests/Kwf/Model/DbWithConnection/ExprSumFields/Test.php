<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Expr_SumFields
 */
class Kwf_Model_DbWithConnection_ExprSumFields_Test extends Kwf_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testExprSumFields()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprSumFields_Model');
        $m->setUp();

        $this->assertEquals(110, $m->getRow(1)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(1)->sum_int_int);
        $this->assertEquals(101, $m->getRow(1)->sum_field_field);

        $this->assertEquals(410, $m->getRow(2)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(2)->sum_int_int);
        $this->assertEquals(402, $m->getRow(2)->sum_field_field);

        $this->assertEquals(410, $m->getRow(3)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(3)->sum_int_int);
        $this->assertEquals(403, $m->getRow(3)->sum_field_field);

        $m->dropTable();
    }

    public function testExprSumFieldsEfficient()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprSumFields_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('sum_field_int');
        $s->expr('sum_int_int');
        $s->expr('sum_field_field');
        $s->order('id');
        $rows = $m->getRows($s)->toArray();

        $this->assertEquals(110, $rows[0]['sum_field_int']);
        $this->assertEquals(100+10+99, $rows[0]['sum_int_int']);
        $this->assertEquals(101, $rows[0]['sum_field_field']);

        $this->assertEquals(410, $rows[1]['sum_field_int']);
        $this->assertEquals(100+10+99, $rows[1]['sum_int_int']);
        $this->assertEquals(402, $rows[1]['sum_field_field']);

        $this->assertEquals(410, $rows[2]['sum_field_int']);
        $this->assertEquals(100+10+99, $rows[2]['sum_int_int']);
        $this->assertEquals(403, $rows[2]['sum_field_field']);

        $m->dropTable();
    }

    public function testExprSumFieldsWhereEquals()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprSumFields_Model');
        $m->setUp();

        $s = $m->select();
        $s->whereEquals('sum_field_int', 410);
        $s->order('id');
        $this->assertEquals(2, $m->getRow($s)->id);

        $m->dropTable();
    }
}
