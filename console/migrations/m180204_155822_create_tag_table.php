<?php
use yii\db\Migration;
class m180204_155822_create_tag_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="标签"';
        }
        //建表
        $this->createTable(
            'tag',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'tag'=>$this->string(200)->notNull()->unique()->comment('标签'),
            ),
            $options
        );
    }
}
