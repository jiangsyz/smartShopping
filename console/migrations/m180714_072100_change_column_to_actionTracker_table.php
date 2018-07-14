<?php
use yii\db\Migration;
class m180714_072100_change_column_to_actionTracker_table extends Migration{
	//该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        $this->dropColumn('action_tracker','actionName');
        $this->dropColumn('action_tracker','actionUri');
        $this->addColumn('action_tracker','controllerId','VARCHAR(200) NOT NULL COMMENT "controllerId" AFTER `runningId`');
        $this->addColumn('action_tracker','actionId','VARCHAR(200) NOT NULL COMMENT "actionId" AFTER `controllerId`');
        $this->addColumn('action_tracker','data','TEXT NOT NULL COMMENT "日志内容" AFTER `actionId`');
        $this->addColumn('action_tracker','runningTime','INT(10) DEFAULT NULL COMMENT "业务处理时间" AFTER `time`');
        $this->addColumn('action_tracker','result','VARCHAR(200) NOT NULL COMMENT "业务处理结果" AFTER `runningTime`');
    }
}
