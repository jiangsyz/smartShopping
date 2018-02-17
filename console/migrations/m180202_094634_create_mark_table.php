<?php
use yii\db\Migration;
class m180202_094634_create_mark_table extends Migration{
    public function up(){
        //表配置
        $options=NULL;
        if($this->db->driverName==='mysql'){
            $options='CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="标记"';
        }
        //建表
        $this->createTable(
            'mark',
            array(
                'id'=>$this->primaryKey()->comment('主键'),
                'memberId'=>$this->integer(10)->notNull()->comment('会员id'),
				'markType'=>$this->integer(10)->notNull()->comment('标记类型'),
				'sourceType'=>$this->integer(10)->notNull()->comment('资源类型'),
                'sourceId'=>$this->integer(10)->notNull()->comment('资源id'),
                'createTime'=>$this->integer(10)->notNull()->comment('记录创建时间'),
            ),
            $options
        );
        //添加联合唯一键
        $this->createIndex('markUnique','mark','memberId,markType,sourceType,sourceId',true);
    }
}
