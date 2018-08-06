<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\SmartException;
use yii\base\Exception;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\recommend\recommendRecord;
use backend\models\product\spuExtraction;
class RecommendController extends SmartWebController{
	//获取推荐或热门的spu
	public function actionApiGetSpus(){
		try{
			//根据token获取会员
			$token=$this->requestGet('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取推荐类型
			$recommendType=$this->requestGet('recommendType',0);
			//获取每页多少条
			$pageSize=$this->requestGet('pageSize',0);
			//获取当前第几页
			$pageNum=$this->requestGet('pageNum',0);
			//查询query
			$sourceType=source::TYPE_SPU;
			$where="`recommendType`='{$recommendType}' AND `sourceType`='{$sourceType}'";
			$query=recommendRecord::find()->where($where)->with('source')->orderBy("`sort` ASC");
			//获取分页数据
			$result=Yii::$app->smartPagination->getData($query,$pageSize,$pageNum);
			//组织数据
			$data=$result;
			unset($data['objs']);
			$data['spus']=array();
			foreach($result['objs'] as $recommendRecord){
				$spuExtraction=new spuExtraction($recommendRecord->source);
				$data['spus'][]=$spuExtraction->getBasicData();
			}
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(Exception $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}
	}
}