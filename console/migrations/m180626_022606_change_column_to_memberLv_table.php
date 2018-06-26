<?php
use yii\db\Migration;
class m180626_022606_change_column_to_memberLv_table extends Migration{
    public function up(){
        $this->addColumn('member_lv','closed','INT(10) NOT NULL COMMENT "是否关闭(0=未关闭/1＝已关闭)" AFTER `orderId`');
    }
}
