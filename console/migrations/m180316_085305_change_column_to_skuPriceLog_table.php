<?php
use yii\db\Migration;
class m180316_085305_change_column_to_skuPriceLog_table extends Migration{
    public function up(){
        $this->dropColumn('sku_price_log','type');
        $this->dropColumn('sku_price_log','data');
        $this->addColumn('sku_price_log','handlerType','INT(10) NOT NULL COMMENT "操作者资源类型" AFTER `id`');
        $this->addColumn('sku_price_log','handlerId','INT(10) NOT NULL COMMENT "操作者资源类型" AFTER `handlerType`');
        $this->addColumn('sku_price_log','memo','VARCHAR(300) DEFAULT NULL COMMENT "备注" AFTER `price`');
    }
}
