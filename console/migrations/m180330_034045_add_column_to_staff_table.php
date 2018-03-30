<?php
use yii\db\Migration;
class m180330_034045_add_column_to_staff_table extends Migration{
    public function up(){
        $this->addColumn('staff','pwd','VARCHAR(200) NOT NULL COMMENT "密码" AFTER `name`');
    }
}
