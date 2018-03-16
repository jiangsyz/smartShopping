<?php
use yii\db\Migration;
class m180316_051658_drop_salesUnitKeepCountLog_table extends Migration{
    public function up(){
        $this->dropTable('sales_unit_keep_count_log');
    }
}
