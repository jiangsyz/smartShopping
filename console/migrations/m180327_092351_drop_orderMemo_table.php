<?php
use yii\db\Migration;
class m180327_092351_drop_orderMemo_table extends Migration{
    public function up(){
        $this->dropTable('order_memo');
    }
}
