<?php
use yii\db\Migration;
class m180301_062238_add_column_distributeType_to_spu_table extends Migration{
    public function up(){
        $this->addColumn('spu','distributeType','INT(10) NOT NULL COMMENT "配送方式(1=冷链/2=非冷链)" AFTER `freight`');
    }
}
