<?php
use yii\db\Migration;
class m180410_073219_add_column_to_orderBuyingRecord extends Migration{
    public function up(){
        $this->addColumn('order_buying_record','logisticsId','VARCHAR(200) DEFAULT NULL COMMENT "物流单号" AFTER `dataPhoto`');
    }
}
