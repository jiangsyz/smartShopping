<?php
use yii\db\Migration;
class m180202_094000_create_categoryRecord_table extends Migration{
    public function up()
    {
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="分类记录"';
        }
        //建表
        $this->createTable(
            'category_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'categoryId'=>$this->integer(10)->notNull()->comment('分类id'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('categoryRecordUnique','category_record','categoryId,sourceType,sourceId',true);
    }
}
