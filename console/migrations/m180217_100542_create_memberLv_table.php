<?php
use yii\db\Migration;
class m180217_100542_create_memberLv_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="会员等级"';
        }
        //建表
        $this->createTable(
            'member_lv',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
                'lv'=>$this->integer(10)->notNull()->comment('会员等级'),
                'start'=>$this->integer(10)->notNull()->comment('会员等级起始时间戳'),
                'end'=>$this->integer(10)->notNull()->comment('会员等级截至时间戳'),
                'orderId'=>$this->integer(10)->notNull()->comment('使该等级记录产生的订单号'),
            ),
            $options
        );
    }
}
