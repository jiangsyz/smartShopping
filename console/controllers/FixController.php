<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\member\youzanCard;
use backend\models\member\member;
use backend\models\member\memberLv;
use backend\models\order\orderBuyingRecord;
class FixController extends SmartDaemonController{
	//修复vip奖励
    public function actionFixYouzanBonus(){
    	try{
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //修复计数
            $fix=0;
            //获取所有会员
            $members=member::find()->all();
            //逐个检查所有会员
            foreach($members as $member){
            	//获取会员的vip
            	$lvs=memberLv::find()->where("`memberId`='{$member->id}'")->all();
            	//确认修复对象
            	if(count($lvs)!=1) continue;
            	if($lvs[0]->handlerType!=999) continue;
            	//获取卡
            	$card=youzanCard::find()->where("`card_no`='{$lvs[0]->handlerId}'")->one();
            	if($card){
            		if($card->card_title=='月卡' || $card->card_title=='新月卡'){
            			//奖励的购买区间
						$start=strtotime("20180615");
						$end=strtotime("20180717");	
						if($card->start_time>=$start && $card->start_time<$end){
							//新增vip记录
							$memberLvData=array();
							$memberLvData['memberId']=$member->id;
							$memberLvData['lv']=1;
							$memberLvData['start']=$card->end_time+1;
							$memberLvData['end']=$memberLvData['start']+60*60*24*31;
							$memberLvData['handlerType']=998;
							$memberLvData['handlerId']=$card->card_no;
							$memberLv=memberLv::addObj($memberLvData);
							//计数
							$fix++;
							//显示电话
							var_dump($member->phone);
						}
            		}
            	}
            }
            //提交事务
            $trascation->commit();
            //处理成功
            var_dump($fix);
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            //处理错误
            echo $e->getMessage();
        }
    }
    //========================================
    //修复购买记录中的物流平台id
    public function actionFixBuyingRecordLogisticsId(){
        try{
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //修复计数
            $fix=0;
            //查询所有购物记录
            $orderBuyingRecords=orderBuyingRecord::find()->all();
            //逐个修复
            foreach($orderBuyingRecords as $orderBuyingRecord){
                //已经有物流平台id的跳过
                if($orderBuyingRecord->logisticsId) continue;
                //获取物流
                $logistics=$orderBuyingRecord->salesUnit->getLogistics();
                //更新物流平台id
                if(!$logistics) 
                    $orderBuyingRecord->updateObj(array('logisticsId'=>NULL));
                else
                    $orderBuyingRecord->updateObj(array('logisticsId'=>$logistics->id));
                //更新修复计数
                $fix++;
            }
            //提交事务
            $trascation->commit();
            //处理成功
            var_dump($fix);
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            //处理错误
            echo $e->getMessage();
        }
    }
}
?>