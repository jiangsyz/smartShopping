<?php
use yii\db\Migration;
class m180204_140552_create_orderRecord_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单"';
        }
        //建表
        $this->createTable(
            'order_record',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
                'index'=>$this->string(300)->notNull()->comment('索引,一棵订单树中唯一'),
                'factoryType'=>$this->string(100)->notNull()->comment('工厂类型'),
                'title'=>$this->string(200)->defaultValue(NULL)->comment('标题'),
                'price'=>$this->double()->notNull()->comment('售卖单元费用'),
                'freight'=>$this->double()->notNull()->comment('运费'),
                'pay'=>$this->integer(10)->notNull()->comment('支付费用(分单位)'),
                'parentId'=>$this->integer(10)->defaultValue(NULL)->comment('父订单id'),
                'createTime'=>$this->integer(10)->notNull()->comment('创建时间'),
                'locked'=>$this->integer(10)->notNull()->defaultValue(1)->comment('锁定(0=正常/1=锁定)'),

            ),
            $options
        );
    }
}
