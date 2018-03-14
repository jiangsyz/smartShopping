<?php
use yii\db\Migration;
class m180314_072558_add_column_to_member_table extends Migration{
    public function up(){
        $this->addColumn('member','pushUniqueId','VARCHAR(200) DEFAULT NULL COMMENT "推送平台唯一编号" AFTER `wechatOpenID`');
        $this->addColumn('member','customServiceUniqueId','VARCHAR(200) DEFAULT NULL COMMENT "客服平台唯一编号" AFTER `pushUniqueId`');
        $this->createIndex('pushUniqueId','member','pushUniqueId',true);
        $this->createIndex('customServiceUniqueId','member','customServiceUniqueId',true);
    }
}