<?php
use yii\db\Migration;
class m180316_051943_create_salesUnitKeepCountLog_table extends Migration{
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
                'handlerType'=>$this->integer(10)->notNull()->comment('操作者资源类型'),
                'handlerId'=>$this->integer(10)->notNull()->comment('操作者资源id'),
                'keepCount'=>$this->integer(10)->notNull()->comment('修改后库存'),
                'originaKeepCount'=>$this->integer(10)->defaultValue(NULL)->comment('原库存'),
                'memo'=>$this->string(200)->defaultValue(NULL)->comment('备注信息'),
                'createTime'=>$this->integer(10)->notNull()->comment('日志创建时间'),
            ),
            $options
        );
    }
}
