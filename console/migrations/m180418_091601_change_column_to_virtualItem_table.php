<?php
use yii\db\Migration;
class m180418_091601_change_column_to_virtualItem_table extends Migration{
    public function up(){
        $this->alterColumn('virtual_item','price','DECIMAL(10,2)');
    }
}
