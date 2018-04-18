<?php
use yii\db\Migration;
class m180418_091811_change_column_to_orderBuyingRecord_table extends Migration{
    public function up(){
        $this->alterColumn('order_buying_record','price','DECIMAL(10,2)');
        $this->alterColumn('order_buying_record','finalPrice','DECIMAL(10,2)');
    }
}
