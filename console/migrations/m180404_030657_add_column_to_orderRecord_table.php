<?php
use yii\db\Migration;
class m180404_030657_add_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','deliverStatus','INT(10) NOT NULL COMMENT "配送状态(0=未配货/1=已配货/2=已发货/3=已签收)" AFTER `closeStatus`');
        $this->addColumn('order_record','refundingStatus','INT(10) NOT NULL COMMENT "退费状态(0=不在退费中/1=退费中)" AFTER `deliverStatus`');
        $this->addColumn('order_record','finishStatus','INT(10) NOT NULL COMMENT "订单完成状态(0=未完成/1=已完成)" AFTER `refundingStatus`');
    }
}
