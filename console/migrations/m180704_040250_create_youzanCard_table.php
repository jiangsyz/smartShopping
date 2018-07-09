<?php
use yii\db\Migration;
class m180704_040250_create_youzanCard_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="有赞历史会员卡"';
        }
        //建表
        $this->createTable(
            'youzan_card',
            array(
                'card_no'=>$this->bigInteger()->notNull()->comment('卡号'),
                'mobile'=>$this->string(200)->defaultValue(NULL)->comment('手机号'),
                'card_alias'=>$this->string(200)->notNull()->comment('卡类型'),
                'card_title'=>$this->string(200)->notNull()->comment('卡名称'),
                'start_time'=>$this->integer(10)->notNull()->comment('开始时间'),
                'end_time'=>$this->integer(10)->notNull()->comment('结束时间'),
                'yz_openid'=>$this->string(200)->notNull()->comment('有赞openid'),
                'fansid'=>$this->string(200)->defaultValue(NULL)->comment('有赞fansid'),
                'unionid'=>$this->string(200)->defaultValue(NULL)->comment('有赞unionid(废弃不用)'),
                'result'=>$this->string(200)->defaultValue(NULL)->comment('处理结果'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('cardNoUnique','youzan_card','card_no',true);
    }
}
