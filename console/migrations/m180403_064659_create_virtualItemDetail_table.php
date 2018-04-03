<?php
use yii\db\Migration;
class m180403_064659_create_virtualItemDetail_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="虚拟产品详情"';
        }
        //建表
        $this->createTable(
            'virtual_item_detail',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'vid'=>$this->integer(10)->notNull()->comment('虚拟产品id'),
                'benefitType'=>$this->string(200)->notNull()->comment('利益类型'),
                'benefitDetail'=>$this->string(500)->notNull()->comment('利益详情'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('vUnique','virtual_item_detail','vid,benefitType',true);
    }
}
