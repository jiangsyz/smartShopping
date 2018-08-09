<?php
use yii\db\Migration;
class m180809_073735_add_column_to_orderBuyingRecord_table extends Migration{
    public function up(){
        $this->addColumn('order_buying_record','logisticsId','INT(10) DEFAULT NULL COMMENT "物流平台" AFTER `dataPhoto`');
    }
}
