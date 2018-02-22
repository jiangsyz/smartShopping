<?php
use yii\db\Migration;
class m180222_005719_add_column_finalPrice_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','finalPrice','DOUBLE NOT NULL COMMENT "成交价" AFTER `price`');
    }
}
