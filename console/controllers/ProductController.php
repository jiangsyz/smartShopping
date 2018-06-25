<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use yii\console\SmartDaemonController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\product\sku;
class ProductController extends SmartDaemonController{
    //检查库存,sku自动下架
    public function actionDaemonSkuAutoClose(){
        $this->begin();
        //循环处理
        while(1){
            //查找所有库存=0且为上架状态的sku
            $skus=sku::find()->where("`closed`='0' AND `count`='0'")->all();
            //逐个下架
            foreach($skus as $sku){
                //自动切为下架
                try{
                    //开启事务
                    $trascation=Yii::$app->db->beginTransaction();
                    //下架
                    $sku->updateObj(array('closed'=>1));
                    //记录日志
                    Yii::$app->smartLog->consoleLog('SkuAutoClose='.$sku->id);
                    //提交事务
                    $trascation->commit();
                }
                catch(Exception $e){$trascation->rollback();}
            }
            //休息一下
            $this->sleep();
            //报告存活
            $this->alive();
        }
    }
    //========================================
    //如果下属sku全部处于下架状态则自动下架spu
    public function actionDaemonSpuAutoClose(){
         $this->begin();
        //循环处理
        while(1){
            //查找所有上架状态的spu
            $spus=spu::find()->where("`closed`='0'")->all();
            //逐个检查
            foreach($spus as $spu){
                //自动切为下架
                try{
                    //开启事务
                    $trascation=Yii::$app->db->beginTransaction();
                    //统计是否全部下架
                    $closedFlag=false;
                    foreach($spu->skus as $sku) if(!$sku->isClosed()) $closedFlag=true;
                    //如果sku全部下架spu自动下架
                    if(!$closedFlag) $spu->updateObj(array('closed'=>1));
                    //记录日志
                    Yii::$app->smartLog->consoleLog('SpuAutoClose='.$spu->id);
                    //提交事务
                    $trascation->commit();
                }
                catch(Exception $e){$trascation->rollback();}
            }
            //休息一下
            $this->sleep();
            //报告存活
            $this->alive();
        }
    }
}
?>