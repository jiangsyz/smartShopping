<?php
use yii\db\Migration;
class m180328_071903_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->dropColumn('order_record','status');
        $this->addColumn('order_record','payStatus','INT(10) NOT NULL COMMENT "支付状态(0=待支付/1=已支付/-1=支付超时)" AFTER `createTime`');
        $this->addColumn('order_record','cancelStatus','INT(10) NOT NULL COMMENT "取消状态(0=未取消/1=已取消)" AFTER `payStatus`');
    }
}
