<?php
use yii\db\Migration;
class m180323_092337_create_orderProperty_table extends Migration{
   public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单属性"';
        }
        //建表
        $this->createTable(
            'order_property',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->comment('订单id'),
                'propertyKey'=>$this->string(200)->notNull()->comment('属性key'),
                'propertyVal'=>$this->text()->notNull()->comment('属性val'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('orderPropertyUnique','order_property','orderId,propertyKey',true);
    }
}
