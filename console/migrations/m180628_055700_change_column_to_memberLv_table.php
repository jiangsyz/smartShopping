<?php
use yii\db\Migration;
class m180628_055700_change_column_to_memberLv_table extends Migration{
    public function up(){
        //删除联合唯一键
        $this->dropIndex('lvUnique','member_lv');
        //添加联合键
        $this->createIndex('lvUnique','member_lv','memberId,end,closed');
    }
}
