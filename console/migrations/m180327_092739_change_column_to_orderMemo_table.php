<?php
use yii\db\Migration;
class m180327_092739_change_column_to_orderMemo_table extends Migration{
    public function up(){
        $this->dropIndex('orderPropertyUnique','order_property');
    }
}
