<?php
use yii\db\Migration;
class m180704_051621_change_column_to_memberLv_table extends Migration{
    public function up(){
        $this->dropColumn('member_lv','orderId');
        $this->addColumn('member_lv','handlerType','INT(10) NOT NULL COMMENT "操作者的资源类型" AFTER `end`');
        $this->addColumn('member_lv','handlerId','BIGINT NOT NULL COMMENT "操作者的资源id" AFTER `handlerType`');
    }
}
