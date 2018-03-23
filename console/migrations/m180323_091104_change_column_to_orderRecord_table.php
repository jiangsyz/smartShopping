<?php
use yii\db\Migration;
class m180323_091104_change_column_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','isNeedAddress','INT(10) NOT NULL COMMENT "是否需要送货地址(0=不需要/1=需要)" AFTER `pay`');
    }
}
