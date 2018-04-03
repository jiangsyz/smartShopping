<?php
use yii\db\Migration;
class m180403_123029_create_refund_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="退款"';
        }
        //建表
        $this->createTable(
            'refund',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'oid'=>$this->integer(10)->notNull()->comment('订单id'),
                'bid'=>$this->integer(10)->defaultValue(NULL)->comment('购物行为id'),
                'price'=>$this->double()->notNull()->comment('退款价格'),
                'applyTime'=>$this->integer(10)->notNull()->comment('申请时间'),
                'status'=>$this->integer(10)->notNull()->comment('状态(0=申请中/1=完成/-1=驳回)'),
                'applyMemo'=>$this->string(300)->defaultValue(NULL)->comment('申请备注'),
                'rejectMemo'=>$this->string(300)->defaultValue(NULL)->comment('驳回备注'),
                'refundMemo'=>$this->string(300)->defaultValue(NULL)->comment('退款备注'),
            ),
            $options
        );
    }
}
