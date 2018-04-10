<?php
use yii\db\Migration;
class m180410_073956_add_column_to_orderBuyingRecord extends Migration{
    public function up(){
        $this->dropColumn('order_buying_record','logisticsId');
        $this->addColumn('order_buying_record','logisticsCode','VARCHAR(200) DEFAULT NULL COMMENT "物流单号" AFTER `dataPhoto`');
    }
}
