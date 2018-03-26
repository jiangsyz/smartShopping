<?php
use yii\db\Migration;
class m180326_090338_change_column_to_address_table extends Migration{
    public function up(){
        $this->addColumn('address','isDeled','INT(10) NOT NULL COMMENT "是否已被软删除" AFTER `createTime`');
        $this->dropIndex('addressUnique','address');
    }
}
