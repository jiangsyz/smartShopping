<?php
use yii\db\Migration;
class m180717_081028_create_orderRecordLog_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单数据日志"';
        }
        //建表
        $this->createTable(
            'order_record_log',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'runningId'=>$this->string(200)->notNull()->comment('runningId'),
                'originaData'=>$this->text()->notNull()->comment('老数据'),
                'data'=>$this->text()->notNull(NULL)->comment('新数据'),
                'time'=>$this->integer(10)->notNull()->comment('记录添加时间'),
            ),
            $options
        );
    }
}
