<?php
use yii\db\Migration;
class m180204_155953_create_tagRecord_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="标签绑定资源"';
        }
        //建表
        $this->createTable(
            'tag_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'tagId'=>$this->integer(10)->notNull()->comment('标签id'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('tagRecordUnique','tag_record','tagId,sourceType,sourceId',true);
    }
}
