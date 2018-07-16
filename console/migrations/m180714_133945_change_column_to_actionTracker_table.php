<?php
use yii\db\Migration;
class m180714_133945_change_column_to_actionTracker_table extends Migration{
    //该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        $this->addColumn('action_tracker','desc','VARCHAR(200) NOT NULL COMMENT "接口描述" AFTER `actionId`');
    }
}
