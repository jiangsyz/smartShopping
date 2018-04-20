<?php
use yii\db\Migration;
class m180419_025701_create_yzOrder_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="有赞订单临时表,有赞订单物流脚本工具使用"';
        }
        //建表
        $this->createTable(
            'yz_order',
            array(
                'order_id'=>$this->string(200)->notNull()->comment('订单号'),
                'spu_title'=>$this->string(200)->notNull()->comment('商品名称'),
                'sku_code'=>$this->string(200)->notNull()->comment('SKU编码'),
                'store_code'=>$this->string(200)->notNull()->comment('商家编码'),
                'consignee'=>$this->string(200)->notNull()->comment('收件人'),
                'address'=>$this->string(200)->notNull()->comment('收件地址'),
                'phone'=>$this->string(200)->notNull()->comment('收件人手机'),
                'hash'=>$this->string(200)->notNull()->comment('收件人+收件地址+收件人手机的哈希值'),
                'quantity'=>$this->integer(11)->notNull()->comment('数量'),
            ),
            $options
        );
    }    
}
