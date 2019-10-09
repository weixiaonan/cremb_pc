<?php

/**
 * 公共用户可以访问的类(不需要登录)
 */

namespace app\home\controller;
use think\facade\Lang;
use think\facade\APP;
use crmeb\services\SystemConfigService;
/**

 * 控制器
 */
class  BaseMall extends BaseHome {

    public function initialize() {
        parent::initialize();
        Lang::load(APP::getAppPath() . '/lang'.config('lang.default_lang').'/basemall.lang.php');
        $this->template_dir = 'default/mall/'.  strtolower(request()->controller()).'/';

        $site_name       = SystemConfigService::get('site_name');
        $seo_title       = SystemConfigService::get('seo_title');
        $seo_keywords    = SystemConfigService::get('seo_keywords') ?? '商城，购物';
        $seo_description = SystemConfigService::get('seo_description') ?? '商城，购物';

        $this->assign('seo_title', $seo_title);
        $this->assign('seo_keywords', $seo_keywords);
        $this->assign('seo_description', $seo_description);
    }
}

?>
