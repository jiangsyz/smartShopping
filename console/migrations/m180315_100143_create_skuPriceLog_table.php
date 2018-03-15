<?php
use yii\db\Migration;
class m180315_100143_create_skuPriceLog_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="sku价格日志"';
        }
        //建表
        $this->createTable(
            'sku_price_log',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'type'=>$this->integer(10)->notNull()->comment('操作类型(1=员工改价/2=系统改价)'),
                'skuId'=>$this->integer(10)->notNull()->comment('skuId'),
                'lv'=>$this->integer(10)->notNull()->comment('会员等级'),
                'originaPrice'=>$this->double()->defaultValue(NULL)->comment('原价'),
                'price'=>$this->double()->notNull()->comment('修改后的数据'),
                'data'=>$this->string(300)->notNull()->comment('上下文数据'),
                'createTime'=>$this->integer(10)->notNull()->comment('日志创建时间'),
            ),
            $options
        );
    }

}
