<?php
use yii\db\Migration;
class m180413_073406_create_version_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="版本号管理"';
        }
        //建表
        $this->createTable(
            'version',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'type'=>$this->string(200)->notNull()->comment('客户端类型'),
                'versionName'=>$this->string(200)->notNull()->comment('版本'),
                'versionTime'=>$this->integer(10)->notNull()->comment('版本时间'),
                'memo'=>$this->string(500)->notNull()->comment('备注'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('versionUnique','version','type,versionTime',true);
    }
}
