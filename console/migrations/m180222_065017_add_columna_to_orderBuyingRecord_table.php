<?php
use yii\db\Migration;
class m180222_065017_add_columna_to_orderBuyingRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_buying_record','price','DOUBLE NOT NULL COMMENT "单价" AFTER `buyingCount`');
        $this->addColumn('order_buying_record','finalPrice','DOUBLE NOT NULL COMMENT "成交单价" AFTER `price`');
    }
}
