<?php

namespace app\home\controller;

use app\admin\model\system\SystemAttachment;
use crmeb\services\SystemConfigService;
use crmeb\services\GroupDataService;
use app\models\store\StoreCategory;
use app\models\store\StoreProduct;
use crmeb\services\UtilService;
use think\facade\Lang;
use app\models\store\StoreProductAttr;
use app\models\store\StoreProductRelation;
use app\models\store\StoreProductReply;

use think\facade\APP;
/**

 * 控制器
 */

class  Goods extends BaseMall {

    public function initialize() {
        parent::initialize();
       // Lang::load(APP::getAppPath()  . '\\lang\\'.config('lang.default_lang').'\\goods.lang.php');
    }

    public function index($goods_id) {
        $id = $goods_id;

        if (!$id || !($storeInfo = StoreProduct::getValidProduct($id))) return app('json')->fail('商品不存在或已下架');

        //公众号
        $name = $id.'_product_detail_wap.jpg';
        $imageInfo = SystemAttachment::getInfo($name,'name');
        $siteUrl = SystemConfigService::get('site_url');
        if(!$imageInfo){
            $codeUrl = UtilService::setHttpType($siteUrl.'/detail/'.$id, 1);//二维码链接
            $imageInfo = UtilService::getQRCodePath($codeUrl, $name);
            if(!$imageInfo) return app('json')->fail('二维码生成失败');
            SystemAttachment::attachmentAdd($imageInfo['name'],$imageInfo['size'],$imageInfo['type'],$imageInfo['dir'],$imageInfo['thumb_path'],1,$imageInfo['image_type'],$imageInfo['time'],2);
            $url = $imageInfo['dir'];
        }else $url = $imageInfo['att_dir'];
        if($imageInfo['image_type'] == 1)
            $url = $siteUrl.$url;
        $storeInfo['image'] = UtilService::setSiteUrl($storeInfo['image'], $siteUrl);
        $storeInfo['image_base'] = UtilService::setImageBase64(UtilService::setSiteUrl($storeInfo['image'], $siteUrl));
        $storeInfo['code_base'] = UtilService::setImageBase64($url);
        $uid = 0;//$request->uid();
        $data['uid'] = $uid;
        //替换windows服务器下正反斜杠问题导致图片无法显示
        $storeInfo['description'] = preg_replace_callback('#<img.*?src="([^"]*)"[^>]*>#i', function ($imagsSrc) {
            return isset($imagsSrc[1]) && isset($imagsSrc[0]) ? str_replace($imagsSrc[1], str_replace('\\', '/', $imagsSrc[1]), $imagsSrc[0]) : '';
        }, $storeInfo['description']);
        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($id, $uid, 'collect');
        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($id, $uid, 'like');
        list($productAttr, $productValue) = StoreProductAttr::getProductAttrDetail($id);
        setView($uid, $id, $storeInfo['cate_id'], 'viwe');
        $data['storeInfo'] = StoreProduct::setLevelPrice($storeInfo, $uid, true);
        $data['similarity'] = StoreProduct::cateIdBySimilarityProduct($storeInfo['cate_id'], 'id,store_name,image,price,sales,ficti', 4);
        $data['productAttr'] = $productAttr;
        $data['productValue'] = $productValue;
        $data['priceName'] = 0;
        if($uid){
            $storeBrokerageStatus = SystemConfigService::get('store_brokerage_statu') ?? 1;
            if($storeBrokerageStatus == 2)
                $data['priceName'] = StoreProduct::getPacketPrice($storeInfo, $productValue);
            else{
                $user = $request->user();
                if($user->is_promoter)
                    $data['priceName'] = StoreProduct::getPacketPrice($storeInfo, $productValue);
            }
            if(!strlen(trim($data['priceName'])))
                $data['priceName'] = 0;
        }
        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['id']);
        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id', $storeInfo['id'])->count();
        if ($data['replyCount']) {
            $goodReply = StoreProductReply::productValidWhere()->where('product_id', $storeInfo['id'])->where('product_score', 5)->count();
            $data['replyChance'] =  $goodReply;
            if($goodReply){
                $data['replyChance'] = bcdiv($goodReply, $data['replyCount'], 2);
                $data['replyChance'] = bcmul($data['replyChance'], 100, 2);
            }
        } else $data['replyChance'] = 0;
        $data['mer_id'] = StoreProduct::where('id', $storeInfo['id'])->value('mer_id');

        //热销
        $field = ' id,store_name,price,sales,image ';
        $model = StoreProduct::where('is_del', 0)->where('mer_id', 0)
            ->where('stock', '>', 0)->where('is_show', 1)->field($field)
            ->order('sales DESC, id DESC');
        $model->limit(5);
        $hot_sales =  StoreProduct::setLevelPrice($model->select(), $uid);


        $goods = [];
        $goods['goods_state'] = 1; //上架
        $goods['is_have_gift'] = [];

        $goods['is_virtual'] = [];//商品规格值
        $goods['is_presell'] = [];//预售商品发货时间
        $goods['is_goodsfcode'] = [];//预售商品发货时间
        $goods['goods_storage'] = $data['storeInfo']['stock'];//购买数量及库存
        $goods['buynow_text'] = '立即购买';//购买数量及库存
        $goods['cart'] = true;//加入购物车
        $goods['goods_salenum'] = 0;//销售记录总数
        $goods['goods_attr'] = 0;//销售记录总数
        $goods['is_appoint'] = 0;//
        $goods['transport_id'] = 0;// 运费
        $goods['goods_body'] = $data['storeInfo']['description'];//销售记录总数


        $data['IsHaveBuy'] = 0;//限制购买
        $data['goods_evaluate_info']['all'] = 0;//商品评价
        $data['goods_evaluate_info']['good_percent'] = 1;//商品好评价
        $data['goods_evaluate_info']['normal_percent'] = 2;//商品中评评价
        $data['goods_evaluate_info']['bad_percent'] = 3;//商品差评价

        $data['goods_evaluate_info']['good'] = 4;//商品好评价
        $data['goods_evaluate_info']['normal'] = 5;//商品中评评价
        $data['goods_evaluate_info']['bad'] = 6;//商品差评价

        $data['hot_sales'] = $hot_sales;//热销
        $data['hot_collect'] = [];//热收集
        $data['spec_list'] = '[{"sign":"","url":"\/Home\/goods\/index\/goods_id\/5.html"}]';//

        $this->assign($data);
        $this->assign('goods',$goods);
        return $this->fetch($this->template_dir . 'goods');
    }




}
