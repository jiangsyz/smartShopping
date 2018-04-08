<?php
use yii\db\Migration;
class m180408_082049_add_column_to_spu_table extends Migration{
    public function up(){
        $this->addColumn('spu','logisticsId','INT(10) NOT NULL COMMENT "物流渠道id" AFTER `distributeType`');
    }
}
