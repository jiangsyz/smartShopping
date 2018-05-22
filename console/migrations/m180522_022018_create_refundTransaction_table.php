<?php
use yii\db\Migration;
class m180522_022018_create_refundTransaction_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="退款交易"';
        }
        //建表
        $this->createTable(
            'refund_transaction',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'transactionType'=>$this->string(200)->notNull()->comment('交易渠道'),
                'transactionId'=>$this->string(200)->notNull()->comment('交易流水号'),
                'refundId'=>$this->string(200)->notNull()->comment('退款id'),
                'transactionHandlerType'=>$this->integer(10)->notNull()->comment('打款者类型'),
                'transactionHandlerId'=>$this->integer(10)->notNull()->comment('打款者id'),
                'transactionHandlerTime'=>$this->integer(10)->notNull()->comment('打款时间'),
                'status'=>$this->integer(10)->notNull()->comment('状态(0=打款中/1=成功/-1=失败)'),
            ),
            $options
        );
        //添加唯一键
        $this->createIndex('transactionIdUnique','refund_transaction','transactionId',true);
    }
}
