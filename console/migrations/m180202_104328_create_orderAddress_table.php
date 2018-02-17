<?php
use yii\db\Migration;
class m180202_104328_create_orderAddress_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单收货地址"';
        }
        //建表
        $this->createTable(
            'order_address',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->unique()->comment('订单id'),
                'addressInfo'=>$this->string(500)->notNull()->comment('收获地址信息'),
            ),
            $options
        );
    }
}
