<?php
use yii\db\Migration;
class m180410_065924_create_notice_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="通知"';
        }
        //建表
        $this->createTable(
            'notice',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
                'type'=>$this->integer(10)->notNull()->comment('通知类型'),
                'content'=>$this->string(500)->notNull()->comment('通知内容'),
                'createTime'=>$this->integer(10)->notNull()->comment('创建时间'),
                'sendStatus'=>$this->integer(10)->notNull()->comment('推送状态(0=未推送)'),
                'sendTime'=>$this->integer(10)->notNull()->comment('推送时间'),
            ),
            $options
        );
    }
}
