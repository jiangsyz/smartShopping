<?php
use yii\db\Migration;
class m180202_084004_create_banner_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="幻灯片"';
        }
        //建表
        $this->createTable(
            'banner',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'title'=>$this->string(200)->defaultValue(NULL)->comment('标题'),
                'image'=>$this->string(200)->defaultValue(NULL)->comment('图片'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'siteNo'=>$this->string(200)->notNull()->comment('广告位编号'),
                'sort'=>$this->integer(10)->notNull()->defaultValue(0)->comment('排序值(越小越靠前)'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('bannerUnique','banner','siteNo,sourceId,sourceType',true);
    }
}
