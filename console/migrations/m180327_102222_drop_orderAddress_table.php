<?php
use yii\db\Migration;
class m180327_102222_drop_orderAddress_table extends Migration{
    public function up(){
        $this->dropTable('order_address');
    }
}
