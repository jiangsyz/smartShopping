<?php
use yii\db\Migration;
class m180714_125333_change_column_to_actionTracker_table extends Migration{
    //该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        $this->dropColumn('action_tracker','time');
        $this->addColumn('action_tracker','logTime','INT(10) NOT NULL COMMENT "日志时间" AFTER `data`');
        $this->addColumn('action_tracker','trackTime','INT(10) NOT NULL COMMENT "追踪时间" AFTER `logTime`');
    }
}
