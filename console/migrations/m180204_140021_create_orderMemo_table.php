<?php
use yii\db\Migration;
class m180204_140021_create_orderMemo_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单备注"';
        }
        //建表
        $this->createTable(
            'order_memo',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->comment('订单id'),
                'memo'=>$this->string(300)->defaultValue(NULL)->comment('备注'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('orderMemoUnique','order_memo','orderId',true);
    }
}
