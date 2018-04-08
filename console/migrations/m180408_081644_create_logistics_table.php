<?php
use yii\db\Migration;
class m180408_081644_create_logistics_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="物流渠道"';
        }
        //建表
        $this->createTable(
            'logistics',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'name'=>$this->string(200)->notNull()->comment('物流渠道'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('nameUnique','logistics','name',true);
    }
}
