<?php
use yii\db\Migration;
class m180211_071212_drop_card_table extends Migration{
    public function up(){
        $this->dropTable('card');
    }
}
