<?php
use yii\db\Migration;
class m180202_091613_create_category_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="分类"';
        }
        //建表
        $this->createTable(
            'category',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'name'=>$this->string(200)->notNull()->comment('名称'),
                'icon'=>$this->string(200)->defaultValue(NULL)->comment('图标'),
                'pid'=>$this->integer(10)->defaultValue(NULL)->comment('父分类id'),
                'sort'=>$this->integer(10)->notNull()->defaultValue(0)->comment('排序值(越小越靠前)'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('categoryUnique','category','name,pid',true);
    }
}
