<?php

namespace app\home\controller;

use think\facade\Lang;

use think\facade\APP;
use think\facade\View;
use crmeb\services\GroupDataService;
use app\models\store\StoreCategory;
use app\models\store\StoreProduct;
/**

 * 控制器
 */

class  Index extends BaseMall {

    public function initialize() {
        parent::initialize();

      //  Lang::load(APP::getAppPath()  . 'lang\\'.config('lang.default_lang').'\\index.lang.php');
    }

    public function index() {
        $navs = ['header' => []];//快捷导航
        $cart_goods_num  = 0;

        //热门搜索关键字获取
        $routineHotSearch = GroupDataService::getData('routine_hot_search') ?? [];
        $searchKeyword = [];
        if(count($routineHotSearch)){
            foreach ($routineHotSearch as $key=>&$item){
                array_push($searchKeyword, $item['title']);
            }
        }
        //TODO 首页顶部菜单
        $menus = GroupDataService::getData('pc_top_menu') ?: [];
        $navs['middle'] = $menus;

        $navs['footer'] = [
            ['nav_url' => '', 'nav_new_open' => 0, 'nav_title' => '底部']
        ];

        $banner = GroupDataService::getData('pc_home_banner') ?: [];//TODO 首页banner图

        //登录按钮下的文章
        $index_articles = [
            ['article_title' => '测试标题', 'article_url' => '#', 'article_id' => '1']
        ];


        $routine_index_page = GroupDataService::getData('routine_index_page');
        $fastNumber         = $routine_index_page[0]['fast_number'] ?? 6;//SystemConfigService::get('fast_number');//TODO 快速选择分类个数
        $fastList           = StoreCategory::byIndexList((int)$fastNumber);//TODO 快速选择分类个数

        $header_categories = [];
        $cateogry     = StoreCategory::with('children')->where('is_show',1)->where('pid',0)->limit(8)->select();
        $cateogry_arr = $cateogry->hidden(['add_time','is_show','sort','children.sort','children.add_time','children.pid','children.is_show'])->toArray();
        foreach ($cateogry_arr as $key => &$val) {
            $val['goods_list'] = $val['children']->toArray();
            $val['gc_name']    = $val['cate_name'];
            $header_categories[$val['id']]['gc_id']   = $val['id'];
            $header_categories[$val['id']]['gc_name'] = $val['cate_name'];
            $header_categories[$val['id']]['class2'][$key]  =  ['gc_id' => $val['id'], 'gc_name' => '全部'  . ''   . ''] ;
            $header_categories[$val['id']]['class2'][$key]['class3']  = [];

            foreach ($val['goods_list'] as $k => &$v) {
                $v['gc_name'] = $v['cate_name'];
                $v['gc_id']   = $v['id'];
                $field        = 'id as goods_id, store_name as goods_name, keyword as goods_advword, image as goods_image, ot_price as goods_marketprice, price as goods_price';
                $v['gc_list'] = StoreProduct::where('is_hot', 1)->where('is_del', 0)->where('mer_id', 0)
                    ->where('stock', '>', 0)->where('is_show', 1)->field($field)
                    ->order('sort DESC, id DESC')
                    ->limit(8)
                    ->select()
                    ->toArray();
                $header_categories[$val['id']]['class2'][$key]['class3'][] = ['gc_id' => $v['id'], 'gc_name' => $v['gc_name']];
               // array_push($header_categories[$val['id']]['class2']['class3'], );

            }
        }

        //楼层左侧的广告
        $floor_left_adv = GroupDataService::getData('pc_floor_left_adv');

        //TODO 精品推荐个数
        $info['bastList']     = StoreProduct::getBestProduct('id,image,store_name,cate_id,price,ot_price,IFNULL(sales,0) + IFNULL(ficti,0) as sales,unit_name', 5, 0);
        //TODO 热门榜单 猜你喜欢
        $info['hotList']      = StoreProduct::getHotProduct('id,image,store_name,cate_id,price,unit_name', 5);
        //TODO 首发新品个数
        $info['firstList']    = StoreProduct::getNewProduct('id,image,store_name,cate_id,price,unit_name,IFNULL(sales,0) + IFNULL(ficti,0) as sales', 5, 0);
        //TODO 首页促销单品
        $info['benefitList']  = StoreProduct::getBenefitProduct('id,image,store_name,cate_id,price,ot_price,stock,unit_name', 5);


        $this->assign('index_sign', 'index');
        $this->assign('navs', $navs);
        $this->assign('cart_goods_num', $cart_goods_num);
        $this->assign('hot_search', $searchKeyword);
        $this->assign('banner', $banner);
        $this->assign('index_articles', $index_articles);
        $this->assign('fastList', $fastList);
        $this->assign('fastList', $fastList);
        //楼层数据
        $this->assign('floor_block', $cateogry_arr);
        //获取所有分类
        $this->assign('header_categories', $header_categories);
        $this->assign('info', $info);
        $this->assign('floor_left_adv', $floor_left_adv);


        return $this->fetch($this->template_dir . 'index');
    }


    //json输出商品分类
    public function josn_class() {
        /**
         * 实例化商品分类模型
         */
        $goodsclass_model = model('goodsclass');
        $goods_class = $goodsclass_model->getGoodsclassListByParentId(intval(input('get.gc_id')));
        $array = array();
        if (is_array($goods_class) and count($goods_class) > 0) {
            foreach ($goods_class as $val) {
                $array[$val['gc_id']] = array(
                    'gc_id' => $val['gc_id'],
                    'gc_name' => htmlspecialchars($val['gc_name']),
                    'gc_parent_id' => $val['gc_parent_id'],
                    'gc_sort' => $val['gc_sort']
                );
            }
        }

        echo $_GET['callback'] . '(' . json_encode($array) . ')';
    }

    /**
     * json输出地址数组 public/static/plugins/area_datas.js
     */
    public function json_area() {
        echo $_GET['callback'] . '(' . json_encode(model('area')->getAreaArrayForJson()) . ')';
    }

    /**
     * json输出地址数组 
     */
    public function json_area_show() {
        $area_info['text'] = model('area')->getTopAreaName(intval($_GET['area_id']));
        echo $_GET['callback'] . '(' . json_encode($area_info) . ')';
    }

    //判断是否登录
    public function login() {
        echo (session('is_login') == '1') ? '1' : '0';
    }

    /**
     * 查询每月的周数组
     */
    public function getweekofmonth() {
        Loader::import('mall.datehelper');
        $year = input('get.y');
        $month = input('get.m');
        $week_arr = getMonthWeekArr($year, $month);
        echo json_encode($week_arr);
        die;
    }

    /**
     * 头部最近浏览的商品
     */
    public function viewed_info() {
        $info = array();
        if (session('is_login') == '1') {
            $member_id = session('member_id');
            $info['m_id'] = $member_id;
            if (config('voucher_allow') == 1) {
                $time_to = time(); //当前日期
                $info['voucher'] = db('voucher')->where(
                                array(
                                    'voucher_owner_id' => $member_id, 'voucher_state' => 1,
                                    'voucher_startdate' => array('elt', $time_to),
                                    'voucher_enddate' => array('egt', $time_to)
                        ))->count();
            }
            $time_to = strtotime(date('Y-m-d')); //当前日期
            $time_from = date('Y-m-d', ($time_to - 60 * 60 * 24 * 7)); //7天前
            $consult_mod=model('consult');
            $info['consult'] = $consult_mod->getConsultCount(array(
                        'member_id' => $member_id, 'consult_replytime' => array(
                            array(
                                'gt', strtotime($time_from)
                            ), array('lt', $time_to + 60 * 60 * 24), 'and'
                        )
                    ));
        }
        $goods_list = model('goodsbrowse')->getViewedGoodsList(session('member_id'), 5);
        if (is_array($goods_list) && !empty($goods_list)) {
            $viewed_goods = array();
            foreach ($goods_list as $key => $val) {
                $goods_id = $val['goods_id'];
                $val['url'] = url('Goods/index', ['goods_id' => $goods_id]);
                $val['goods_image'] = goods_thumb($val, 60);
                $viewed_goods[$goods_id] = $val;
            }
            $info['viewed_goods'] = $viewed_goods;
        }
        echo json_encode($info);
    }

}
