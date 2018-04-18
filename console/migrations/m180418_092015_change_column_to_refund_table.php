<?php
use yii\db\Migration;
class m180418_092015_change_column_to_refund_table extends Migration{
    public function up(){
        $this->alterColumn('refund','price','DECIMAL(10,2)');
    }
}
