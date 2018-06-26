<?php
use yii\db\Migration;
class m180626_032934_change_column_to_memberLv_table extends Migration{
    public function up(){
        $this->addColumn('member_lv','closedMemo','VARCHAR(500) DEFAULT NULL COMMENT "关闭日志" AFTER `closed`');
    }
}
