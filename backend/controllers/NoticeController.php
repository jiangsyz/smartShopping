<?php
namespace backend\controllers;
use Yii;
use yii\web\SmartWebController;
use yii\base\Exception;
use yii\base\SmartException;
use backend\models\model\source;
use backend\models\token\tokenManagement;
use backend\models\notice\notice;
class NoticeController extends SmartWebController{
	//获取通知
	public function actionApiGetNotice(){
		try{
			$data=array();
			//根据token获取会员
			$token=Yii::$app->request->get('token',false);
			$member=tokenManagement::getManagement($token,array(source::TYPE_MEMBER))->getOwner();
			//获取每页多少条
			$pageSize=Yii::$app->request->get('pageSize',0);
			//获取当前第几页
			$pageNum=Yii::$app->request->get('pageNum',0);
			//获取sql
			$table=notice::tableName();
			$sql="SELECT * FROM {$table} WHERE `memberId`='{$member->id}' ORDER BY `createTime` DESC";
			//获取query
			$query=notice::findBySql($sql);
			//获取分页数据
			$result=Yii::$app->smartPagination->getData($query,$pageSize,$pageNum);
			//组织数据
			$data=$result;
			unset($data['objs']);
			$data['notice']=array();
			foreach($result['objs'] as $notice) $data['notice'][]=$notice->getData();
			//返回
			$this->response(1,array('error'=>0,'data'=>$data));
    	}
    	catch(SmartException $e){$this->response(1,array('error'=>$e->getCode()?$e->getCode():-1,'msg'=>$e->getMessage()));}	
	}
}