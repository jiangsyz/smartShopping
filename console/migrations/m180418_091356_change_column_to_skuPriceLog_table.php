<?php
use yii\db\Migration;
class m180418_091356_change_column_to_skuPriceLog_table extends Migration{
    public function up(){
        $this->alterColumn('sku_price_log','originaPrice','DECIMAL(10,2)');
        $this->alterColumn('sku_price_log','price','DECIMAL(10,2)');
    }
}
