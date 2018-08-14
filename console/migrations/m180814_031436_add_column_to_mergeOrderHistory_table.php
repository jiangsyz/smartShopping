<?php
use yii\db\Migration;
class m180814_031436_add_column_to_mergeOrderHistory_table extends Migration{
    public function up(){
        $this->addColumn('merge_order_history','mergeId','VARCHAR(200) DEFAULT NULL COMMENT "合单号" AFTER `id`');
    }
}
