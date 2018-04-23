<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\version\version;
use backend\models\token\tokenManagement;
use backend\models\model\source;
class VersionController extends SmartWebController{
    public function actionApiGetVersion(){
        try{
            //开启事务
            $trascation=Yii::$app->db->beginTransaction();
            //根据token获取会员
            $token=Yii::$app->request->get('token',false);
            $member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
            //获取客户端类型
            $type=Yii::$app->request->get('type',false);
            //获取最新版本信息
            $version=version::find()->where("`type`='Android'")->orderBy("`versionTime` DESC")->one();
            if(!$version) throw new SmartException("版本信息异常",-3);
            //提交事务
            $trascation->commit();
            $this->response(1,array('error'=>0,'data'=>$version->getData(array('type','versionName','memo'))));
        }
        catch(Exception $e){
            //回滚
            $trascation->rollback();
            $this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));
        }
    }
}