<?php
use yii\db\Migration;
class m180802_062801_change_column_to_actionTracker_table extends Migration{
	//该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        //添加联合唯一键
        $this->createIndex('requestTimeUnique','action_tracker','requestTime');
    }
}
