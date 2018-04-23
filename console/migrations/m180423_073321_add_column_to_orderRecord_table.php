<?php
use yii\db\Migration;
class m180423_073321_add_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','code','VARCHAR(200) DEFAULT NULL COMMENT "订单编号" AFTER `id`');
        $this->createIndex('codeUnique','order_record','code',true);
    }
}
