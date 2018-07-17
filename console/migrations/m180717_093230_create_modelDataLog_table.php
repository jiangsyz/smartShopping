<?php
use yii\db\Migration;
class m180717_093230_create_modelDataLog_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="数据日志"';
        }
        //建表
        $this->createTable(
            'model_db_log',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'runningId'=>$this->string(200)->notNull()->comment('runningId'),
                'modelName'=>$this->string(200)->notNull()->comment('模型名'),
                'originaData'=>$this->text()->notNull()->comment('老数据'),
                'data'=>$this->text()->notNull(NULL)->comment('新数据'),
                'time'=>$this->integer(10)->notNull()->comment('记录添加时间'),
            ),
            $options
        );
    }
}
