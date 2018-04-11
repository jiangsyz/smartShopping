<?php
use yii\db\Migration;
class m180411_055547_create_sourcePropertyConf_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="资源属性配置"';
        }
        //建表
        $this->createTable(
            'source_property_conf',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'cName'=>$this->string(200)->notNull()->comment('中文名称'),
                'eName'=>$this->string(200)->notNull()->comment('英文名称'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('cNameUnique','source_property_conf','cName',true);
        $this->createIndex('eNameUnique','source_property_conf','eName',true);
    }
}
