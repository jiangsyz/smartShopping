<?php
use yii\db\Migration;
class m180402_074457_add_column_closeStatus_to_orderRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_record','closeStatus','INT(10) NOT NULL COMMENT "关闭状态(0=未关闭/1=已关闭)" AFTER `cancelStatus`');
    }
}
