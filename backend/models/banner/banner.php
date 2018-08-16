<?php
//幻灯片
namespace backend\models\banner;
use Yii;
use yii\base\SmartException;
use common\models\LogActiveRecord;
use backend\models\product\spu;
//========================================
class banner extends LogActiveRecord{

	public static function getAvailableBanner($siteNo)
	{
		//获取推荐内容
		$banners=banner::find()->where("`siteNo`='{$siteNo}'")->orderBy("`sort` ASC")->all();
		//组织数据
		foreach($banners as $b) {
			//只展示3张banner图
			if (count($data) >= 3) {
				break;
			}
			$tmp = explode(',', $b->uri);
			//查看SPU的库存是否为0  是否上架
			$spu = spu::find()->where("id={$tmp[1]}")->one();
			if ($spu->closed == 1 || $spu->getStock() == 0) {
				continue;
			}
			$data[]=$b->getData();
		}
		return $data;
	}
}