<?php
use yii\db\Migration;
class m180204_143952_create_recommendRecord_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="官方推荐"';
        }
        //建表
        $this->createTable(
            'recommend_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'recommendType'=>$this->integer(10)->notNull()->comment('推荐类型'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'sort'=>$this->integer(10)->notNull()->defaultValue(0)->comment('排序值(越小越靠前)'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('recommendRecordUnique','recommend_record','recommendType,sourceType,sourceId',true);
    }
}
