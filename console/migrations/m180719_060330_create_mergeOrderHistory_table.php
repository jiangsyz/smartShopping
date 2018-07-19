<?php
use yii\db\Migration;
class m180719_060330_create_mergeOrderHistory_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单合并日志"';
        }
        //建表
        $this->createTable(
            'merge_order_history',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'orderId'=>$this->integer(10)->notNull()->comment('主订单ID'),
                'buyingRecordId'=>$this->integer(10)->notNull()->comment('合单时对应的buyingRecordId'),
                'logisticsId'=>$this->integer(10)->notNull()->comment('物流渠道'),
                'createTime'=>$this->integer(10)->notNull()->comment('创建时间'),
                'is_completed'=>$this->integer(10)->notNull()->defaultValue(0)->comment('是否完成回单'),
            ),
            $options
        );
    }
}
