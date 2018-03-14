<?php
use yii\db\Migration;
class m180314_073313_drop_column_from_member_table extends Migration{
    public function up(){
        $this->dropColumn('member','wechatUnionID');
        $this->dropColumn('member','wechatOpenID');
    }
}