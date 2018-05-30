<?php
//行为追踪
namespace backend\models\actionTracker;
use Yii;
use yii\base\SmartException;
use yii\db\SmartActiveRecord;
use backend\models\model\source;
//========================================
class actionTracker extends SmartActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
	//========================================
	//uri列表
	public static $uriList=array(
		//开放给会员的api
		'memberApi'=>array(
			//标记模块
			'mark/api-mark'=>'标记某个资源',
			'mark/api-get-collectors'=>'获取某个资源的标记者',
			'mark/api-cancel-mark'=>'取消标记',
			'mark/api-get-mark-spu'=>'获取会员标记的spu',
			//产品展示模块
			'banner/api-get-banner'=>'获取指定广告位的幻灯片',
			'recommend/api-get-spus'=>'获取推荐或热门的spu',
			'product/api-get-spu-detail'=>'获取spu详情',
			'virtual-item/api-get-virtual-item'=>'获取虚拟商品',
			'product/api-search-spu'=>'搜索spu',
			//订单模块
			'order/api-get-order-statistics'=>'会员订单统计',
			'order/api-get-orders'=>'会员订单列表',
			'order/api-cancel'=>'取消订单',
			'buying/api-apply-pay'=>'支付订单',
			'order/api-get-detail'=>'获取订单详情',
			'order/api-receipted'=>'订单确认收货',
			'order/api-change-address'=>'订单修改地址',
			//分类模块
			'category/api-get-top-categories'=>'获取所有顶级分类',
			'category/api-get-children'=>'获取某个分类的子分类',
			'category/api-get-spu'=>'获取某个分类下的spu',
			//购买模块
			'buying/api-apply-create-order-by-fast-buying'=>'通过快速购买申请创建订单',
			'buying/api-apply-create-order-by-shopping-cart'=>'通过购物车申请创建订单',
			'buying/api-create-order-by-shopping-cart'=>'通过购物车创建订单',
			'buying/api-create-order-by-fast-buying'=>'通过快速购买创建订单',
			//购物车模块
			'shopping-cart/api-update'=>'修改购物车记录的数量',
			'shopping-cart/api-del'=>'删除购物车记录',
			'shopping-cart/api-get-shopping-cart'=>'获取购物车信息',
			//会员模块
			'member/api-get-info'=>'获取会员的基础信息',
			'member/api-upload-avatar'=>'上传会员头像',
			'member/api-upload-nickname'=>'上传会员昵称',
			'member/api-upload-push-unique-id'=>'上传会员推送平台id',
			'member/api-clear-push-unique-id'=>'清除会员推送平台id',
			'address/api-add-address'=>'会员添加收货地址',
			'address/api-del-address'=>'会员删除收货地址',
			'address/api-update-address'=>'会员修改收货地址',
			'address/api-get-address-list'=>'获取会员地址簿',
			//通知模块
			'notice/api-get-notice'=>'获取用户通知',
		),
		//开放给员工的api
		'staffApi'=>array(
			'order/api-change-price'=>'订单改商品价格',
			'order/api-change-freight'=>'订单修改运费',
			'order/api-close'=>'关闭订单',
			'refund/api-reject'=>'驳回退款',
			'refund/api-reopen'=>'重开退款',
			'refund/api-refund-by-buying-record'=>'针对单个购物行为的退款',
			'refund/api-refund'=>'退款',
			'refund/api-reset'=>'重置退款',
			'product/api-update-sku-price'=>'员工修改sku价格',
			'product/api-update-sku-keep-count'=>'员工修改sku库存',
		),
		//无用的需要删除的
		'needDel'=>array(
			'test/test',
			'test/index',
		),
	);
	//========================================
	//行为追踪
	public static function actionTrack($startId=1){
		echo "actionTrack startId={$startId}\n";
		//查询一条日志
		$sql="SELECT * FROM `smart_log_record` WHERE `id`>='{$startId}' ORDER BY `id`";
		$logs=Yii::$app->log_db->createCommand($sql)->queryAll();
		//分析
		foreach($logs as $log){
			//http请求
			if($log['logType']==1){
				//查看是否处理过
				if(self::find()->where("`logId`='{$log['id']}'")->one()) continue;
				//提取信息uri
				$data=json_decode($log['data'],true);
				//检查http情求或应答必须要有的信息
				if(!isset($data['uri'])) continue;
				if(!isset($data['requestData'])) continue;
				//会员行为
				if(isset(self::$uriList['memberApi'][$data['uri']])){
					//获取令牌字符串
					if(!isset($data['requestData']['token'])) continue;
					$tokenStr=$data['requestData']['token'];
					//获取令牌
					$token=Yii::$app->smartToken->getToken($tokenStr,array(source::TYPE_MEMBER),false);
					if(!$token) continue;
					//记录行为追踪
					$actionTracker=array();
					$actionTracker['logId']=$log['id'];
					$actionTracker['sourceType']=source::TYPE_MEMBER;
					$actionTracker['sourceId']=$token->data;
					$actionTracker['runningId']=$log['runningId'];
					$actionTracker['actionName']=self::$uriList['memberApi'][$data['uri']];
					$actionTracker['actionUri']=$data['uri'];
					$actionTracker['time']=time();
					self::addObj($actionTracker);
				}
				//员工行为
				elseif(isset(self::$uriList['staffApi'][$data['uri']])){
					//获取令牌字符串
					if(!isset($data['requestData']['token'])) continue;
					$tokenStr=$data['requestData']['token'];
					//获取令牌
					$token=Yii::$app->smartToken->getToken($tokenStr,array(source::TYPE_STAFF),false);
					if(!$token) continue;
					//记录行为追踪
					$actionTracker=array();
					$actionTracker['logId']=$log['id'];
					$actionTracker['sourceType']=source::TYPE_STAFF;
					$actionTracker['sourceId']=$token->data;
					$actionTracker['runningId']=$log['runningId'];
					$actionTracker['actionName']=self::$uriList['staffApi'][$data['uri']];
					$actionTracker['actionUri']=$data['uri'];
					$actionTracker['time']=time();
					self::addObj($actionTracker);
				}
			}
		}
	}
	//========================================
	//追踪失败
	public static function trackError($log,$error){return true;}
}