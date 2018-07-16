<?php
use yii\db\Migration;
class m180714_132432_change_column_to_actionTracker_table extends Migration{
    //该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        $this->dropColumn('action_tracker','logTime');
        $this->addColumn('action_tracker','requestTime','INT(10) NOT NULL COMMENT "请求时间" AFTER `data`');
        $this->addColumn('action_tracker','responseTime','INT(10) NOT NULL COMMENT "应答时间" AFTER `requestTime`');
    }
}
