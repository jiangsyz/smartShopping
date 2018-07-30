<?php
//行为追踪
namespace backend\models\actionTracker;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\model\source;
//========================================
class actionTracker extends LogActiveRecord{
	//指定库
	public static function getDb(){return Yii::$app->log_db;}
	//========================================
	//uri列表
	public static $uriList=array(
		//开放给会员的api
		'memberApi'=>array(
			//入口文件
			'/smartShopping/backend/web/index.php'=>array(
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
				'product/api-get-recommend-spus'=>'获取spu的相关推荐',
				//订单模块
				'order/api-get-order-statistics'=>'会员订单统计',
				'order/api-get-orders'=>'会员订单列表',
				'order/api-cancel'=>'取消订单',
				'buying/api-apply-pay'=>'支付订单',
				'order/api-get-detail'=>'获取订单详情',
				'order/api-receipted'=>'订单确认收货',
				'order/api-change-address'=>'订单修改地址',
				'order/api-get-logistics-list'=>'获取订单物流列表',
				'order/api-get-logistics-detail'=>'获取订单物流详情',
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
		),
		//开放给员工的api
		'staffApi'=>array(
			'/smartShopping/backend/web/index.php'=>array(
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
				'member/api-close-vip'=>'员工关闭vip',
				'order/api-change-address-by-staff'=>'员工修改订单地址',
			),
		),
		//无用的需要删除的
		'needDel'=>array(
			'/smartShopping/backend/web/index.php'=>array(
				'test/test'=>'测试用',
				'test/index'=>'获取时间戳',
			),
		),
	);
	//========================================
	//行为追踪
	public static function actionTrack($startId=1,$limit=50,$safeSeconds=60){
		//确定时间,一分钟内产生的日志不取
		if($safeSeconds<0) throw new SmartException("error safeSeconds");
		$time=time()-$safeSeconds;
		//查询该批次需要分析的日志
		$table="`smart_log_record`";
		$sql="SELECT * FROM {$table} WHERE `id`>='{$startId}' AND `time`<='{$time}' ORDER BY `id` LIMIT {$limit}";
		$logs=self::getDb()->createCommand($sql)->queryAll();
		//最后分析的日志id
		$maxId=NULL;
		//逐条分析
		foreach($logs as $log){
			//更新最后分析的日志id
			$maxId=$log['id'];
			//只分析httpLog
			if($log['logType']!=1) continue;
			//防止重复分析
			if(self::find()->where("`logId`='{$log['id']}'")->one()) continue;
			//提取信息
			$data=json_decode($log['data'],true);
			//校验信息完整度
			if(!isset($data['file'])) continue;
			if(!isset($data['controllerId'])) continue;
			if(!isset($data['actionId'])) continue;
			if(!isset($data['requestTime'])) continue;
			if(!isset($data['requestData']['token'])) continue; else $tokenStr=$data['requestData']['token'];
			//拼接uri
			$uri="{$data['controllerId']}/{$data['actionId']}";
			//删掉无用的
			if(isset(self::$uriList['needDel'][$data['file']][$uri]))
				self::getDb()->createCommand("DELETE FROM {$table} WHERE `id`='{$log['id']}'")->execute();
			//获取令牌和描述
			$token=NULL;
			$desc=NULL;
			if(isset(self::$uriList['memberApi'][$data['file']][$uri])){
				$token=Yii::$app->smartToken->getToken($tokenStr,array(source::TYPE_MEMBER),false);
				$desc=self::$uriList['memberApi'][$data['file']][$uri];
			}
			if(isset(self::$uriList['staffApi'][$data['file']][$uri])){
				$token=Yii::$app->smartToken->getToken($tokenStr,array(source::TYPE_STAFF),false);
				$desc=self::$uriList['staffApi'][$data['file']][$uri];
			}
			if(!$token) continue;
			if(!$desc) continue;
			//分析业务运行结果
			$actionTracker=array();
			$actionTracker['logId']=$log['id'];
			$actionTracker['sourceType']=$token->type;
			$actionTracker['sourceId']=$token->data;
			$actionTracker['runningId']=$log['runningId'];
			$actionTracker['controllerId']=$data['controllerId'];
			$actionTracker['actionId']=$data['actionId'];
			$actionTracker['desc']=$desc;
			$actionTracker['data']=$log['data'];
			$actionTracker['requestTime']=$data['requestTime'];
			$actionTracker['responseTime']=$data['responseTime'];
			$actionTracker['trackTime']=time();
			if($actionTracker['responseTime'])
				$actionTracker['runningTime']=$actionTracker['responseTime']-$actionTracker['requestTime'];
			else
				$actionTracker['runningTime']=NULL;
			if(isset($data['responseData']['data']['error']) && $data['responseData']['data']['error']===0)
				$actionTracker['result']='成功';
			else
				$actionTracker['result']='失败';
			self::addObj($actionTracker);
		}
		return $maxId;
	}
}