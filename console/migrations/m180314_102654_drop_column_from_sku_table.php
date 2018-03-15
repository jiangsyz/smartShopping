<?php
use yii\db\Migration;
class m180314_102654_drop_column_from_sku_table extends Migration{
    public function up(){
        $this->dropColumn('sku','price');
    }
}
