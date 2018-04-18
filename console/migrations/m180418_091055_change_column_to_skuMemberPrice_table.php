<?php
use yii\db\Migration;
class m180418_091055_change_column_to_skuMemberPrice_table extends Migration{
    public function up(){
        $this->alterColumn('sku_member_price','price','DECIMAL(10,2)');
    }
}
