<?php
use yii\db\Migration;
class m180329_063026_add_column_to_orderBuyingRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_buying_record','dataPhoto','VARCHAR(500) NOT NULL COMMENT "数据快照" AFTER `finalPrice`');
    }
}
