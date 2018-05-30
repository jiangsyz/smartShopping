<?php
use yii\db\Migration;
class m180529_064441_create_actionTracker_table extends Migration{
    //该表建在日志库中
    public function init(){
        parent::init();
        $this->db=yii::$app->log_db;
    }
    //========================================
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="行为追踪"';
        }
        //建表
        $this->createTable(
            'action_tracker',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'logId'=>$this->integer(10)->notNull()->comment('日志id'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'runningId'=>$this->string(200)->notNull()->comment('回调请求的runningId'),
                'actionName'=>$this->string(200)->notNull()->comment('行为名称'),
                'actionUri'=>$this->string(200)->notNull()->comment('行为uri'),
                'time'=>$this->integer(10)->notNull()->comment('日志时间'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('logIdUnique','action_tracker','logId',true);
    }
}
