<?php
use yii\db\Migration;
class m180305_085027_create_sourceProperty_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="资源属性"';
        }
        //建表
        $this->createTable(
            'source_property',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'propertyKey'=>$this->string(200)->notNull()->comment('属性key'),
                'propertyVal'=>$this->string(200)->notNull()->comment('属性val'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('sourcePropertyUnique','source_property','sourceType,sourceId,propertyKey',true);
    }
}
