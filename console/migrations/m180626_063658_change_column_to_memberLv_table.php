<?php
use yii\db\Migration;
class m180626_063658_change_column_to_memberLv_table extends Migration{
    public function up(){
        //添加联合唯一键
        $this->createIndex('lvUnique','member_lv','memberId,end,closed',true);
    }
}
