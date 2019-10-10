<?php

/**
 * 公共用户可以访问的类(不需要登录)
 */

namespace app\home\controller;
use think\facade\Lang;
use think\facade\APP;
use crmeb\services\SystemConfigService;
use crmeb\services\GroupDataService;
/**

 * 控制器
 */
class  BaseMall extends BaseHome {

    public function initialize() {
        parent::initialize();
        Lang::load(APP::getAppPath() . '/lang'.config('lang.default_lang').'/basemall.lang.php');
        $this->template_dir = 'default/mall/'.  strtolower(request()->controller()).'/';
        $navs = ['header' => []];//快捷导航
        $cart_goods_num  = 0;
        $site_name       = SystemConfigService::get('site_name');
        $seo_title       = SystemConfigService::get('seo_title');
        $seo_keywords    = SystemConfigService::get('seo_keywords') ?? '商城，购物';
        $seo_description = SystemConfigService::get('seo_description') ?? '商城，购物';

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

        $this->assign('seo_title', $seo_title);
        $this->assign('seo_keywords', $seo_keywords);
        $this->assign('seo_description', $seo_description);
        $this->assign('navs', $navs);
        $this->assign('cart_goods_num', $cart_goods_num);
        $this->assign('hot_search', $searchKeyword);
    }
}

?>
