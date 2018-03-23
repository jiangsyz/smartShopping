<?php
use yii\db\Migration;
class m180323_093257_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','status','INT(10) NOT NULL COMMENT "订单状态" AFTER `createTime`');
    }
}
