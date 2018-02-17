<?php
use yii\db\Migration;
class m180204_153327_create_sku_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="库存量单位"';
        }
        //建表
        $this->createTable(
            'sku',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'spuId'=>$this->integer(10)->notNull()->comment('SpuId'),
                'title'=>$this->string(200)->notNull()->comment('标题'),
                'price'=>$this->double()->notNull()->comment('价格'),
                'count'=>$this->integer(10)->notNull()->defaultValue(0)->comment('库存数量'),
                'closed'=>$this->integer(10)->notNull()->defaultValue(0)->comment('是否下架(0=上架/1=下架)'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(0)->comment('锁定(0=正常/1=锁定)'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('skuUnique','sku','spuId,title',true);
    }
}
