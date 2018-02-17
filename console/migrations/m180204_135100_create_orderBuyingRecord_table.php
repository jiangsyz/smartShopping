<?php
use yii\db\Migration;
class m180204_135100_create_orderBuyingRecord_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单购买行为"';
        }
        //建表
        $this->createTable(
            'order_buying_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->comment('订单id'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'buyingCount'=>$this->integer(10)->notNull()->comment('购买数量'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('orderBuyingRecordUnique','order_buying_record','orderId,sourceType,sourceId',true);
    }
}
