<?php
use yii\db\Migration;
class m180522_031617_drop_column_from_refund_table extends Migration{
    public function up(){
        $this->dropColumn('refund','refundHandlerType');
        $this->dropColumn('refund','refundHandlerId');
        $this->dropColumn('refund','refundTime');
        $this->dropColumn('refund','refundMemo');
    }
}
