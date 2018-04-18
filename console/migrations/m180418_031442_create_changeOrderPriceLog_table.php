<?php
use yii\db\Migration;
class m180418_031442_create_changeOrderPriceLog_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="订单改价日志"';
        }
        //建表
        $this->createTable(
            'change_order_price_log',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'handlerType'=>$this->integer(10)->notNull()->comment('操作者资源类型'),
                'handlerId'=>$this->integer(10)->notNull()->comment('操作者资源id'),
                'orderId'=>$this->integer(10)->notNull()->comment('订单id'),
                'originaData'=>$this->string(300)->notNull()->comment('老数据'),
                'data'=>$this->string(300)->notNull()->comment('新数据'),
                'memo'=>$this->string(300)->notNull()->comment('备注'),
                'createTime'=>$this->integer(10)->notNull()->comment('日志创建时间')
            ),
            $options
        );
    }
}
