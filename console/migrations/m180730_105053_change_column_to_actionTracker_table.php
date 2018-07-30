<?php
use yii\db\Migration;
class m180730_105053_change_column_to_actionTracker_table extends Migration{
    //该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        $this->alterColumn('action_tracker','responseTime','INT(10) DEFAULT NULL COMMENT "应答时间"');
    }
}
