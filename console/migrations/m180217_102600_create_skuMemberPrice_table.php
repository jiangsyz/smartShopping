<?php
use yii\db\Migration;
class m180217_102600_create_skuMemberPrice_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="sku会员价"';
        }
        //建表
        $this->createTable(
            'sku_member_price',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'skuId'=>$this->integer(10)->notNull()->comment('库存计量单元id'),
                'lv'=>$this->integer(10)->notNull()->comment('会员等级'),
                'price'=>$this->double()->notNull()->comment('价格'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('memberPriceUnique','sku_member_price','skuId,lv',true);
    }
}
