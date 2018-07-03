<?php
use yii\db\Migration;
class m180702_135946_add_column_to_staff_table extends Migration{
    public function up(){
        $this->addColumn('staff','password_hash','VARCHAR(100) DEFAULT NULL COMMENT "password_hash" AFTER `locked`');
        $this->addColumn('staff','auth_key','VARCHAR(100) DEFAULT NULL COMMENT "auth_key" AFTER `password_hash`');
        $this->addColumn('staff','role','smallint(6) DEFAULT 0 COMMENT "role" AFTER `auth_key`');
    }
}
