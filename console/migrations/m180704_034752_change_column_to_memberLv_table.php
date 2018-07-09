<?php
use yii\db\Migration;
class m180704_034752_change_column_to_memberLv_table extends Migration{
    public function up(){
        $this->alterColumn('member_lv','orderId','varchar(500)');
    }
}
