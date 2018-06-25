<?php
use yii\db\Migration;
class m180625_083430_change_column_to_orderBuyingRecord_table extends Migration{
    public function up(){
        $this->alterColumn('order_buying_record','dataPhoto','text');
    }
}
