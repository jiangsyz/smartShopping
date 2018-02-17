<?php
use yii\db\Migration;
class m180204_152646_create_salesUnitKeepCountLog_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="库存改变日志"';
        }
        //建表
        $this->createTable(
            'sales_unit_keep_count_log',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'count'=>$this->integer(10)->notNull()->comment('库存改变数量'),
                'data'=>$this->string(300)->notNull()->comment('上下文数据'),
                'createTime'=>$this->integer(10)->notNull()->comment('日志创建时间'),
            ),
            $options
        );
    }
}
