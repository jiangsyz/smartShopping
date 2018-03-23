<?php
use yii\db\Migration;
class m180323_093004_drop_orderBackground_table extends Migration{
    public function up(){
        $this->dropTable('order_background');
    }
}
