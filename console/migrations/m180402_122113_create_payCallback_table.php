<?php
use yii\db\Migration;
class m180402_122113_create_payCallback_table extends Migration{
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
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="支付回调"';
        }
        //建表
        $this->createTable(
            'pay_callback',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'runningId'=>$this->string(200)->notNull()->comment('回调请求的runningId'),
                'payType'=>$this->string(200)->notNull()->comment('支付类型'),
                'callBackData'=>$this->text()->notNull()->comment('回调数据'),
                'status'=>$this->integer(10)->defaultValue(NULL)->comment('状态(1=成功/-1=失败)'),
                'memo'=>$this->string(200)->defaultValue(NULL)->comment('备注信息'),
            ),
            $options
        );
    }   
}
