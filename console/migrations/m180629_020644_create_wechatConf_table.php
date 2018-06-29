<?php
use yii\db\Migration;
class m180629_020644_create_wechatConf_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="公众号配置"';
        }
        //建表
        $this->createTable(
            'wechat_conf',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'confKey'=>$this->string(200)->notNull()->comment('配置key'),
                'confVal'=>$this->string(200)->notNull()->comment('配置val'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('confKeyUnique','wechat_conf','confKey',true);
    }
}
