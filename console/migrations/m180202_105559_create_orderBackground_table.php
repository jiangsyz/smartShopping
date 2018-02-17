<?php
use yii\db\Migration;
class m180202_105559_create_orderBackground_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单上下文快照"';
        }
        //建表
        $this->createTable(
            'order_background',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->comment('订单id'),
                'backgroundKey'=>$this->string(200)->notNull()->comment('上下文索引'),
                'backgroundVal'=>$this->text()->notNull()->comment('上下文'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('orderBackgroundUnique','order_background','orderId,backgroundKey',true);
    }
}
