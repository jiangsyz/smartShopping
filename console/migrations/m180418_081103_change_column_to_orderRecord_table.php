<?php
use yii\db\Migration;
class m180418_081103_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->alterColumn('order_record','price','DECIMAL(10,2)');
        $this->alterColumn('order_record','memberPrice','DECIMAL(10,2)');
        $this->alterColumn('order_record','finalPrice','DECIMAL(10,2)');
        $this->alterColumn('order_record','reduction','DECIMAL(10,2)');
        $this->alterColumn('order_record','freight','DECIMAL(10,2)');
    }

}
