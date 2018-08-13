<?php
use yii\db\Migration;
class m180813_080051_add_column_to_memberLv_table extends Migration{
    public function up(){
        $this->addColumn('member_lv','handlerMemo','VARCHAR(500) DEFAULT NULL COMMENT "操作备注" AFTER `handlerId`');
    }
}