<?php /*a:6:{s:66:"F:\wyb\shopwm\app\admin\view\wechat\wechat_news_category\index.php";i:1569378565;s:49:"F:\wyb\shopwm\app\admin\view\public\container.php";i:1569378565;s:50:"F:\wyb\shopwm\app\admin\view\public\frame_head.php";i:1569378565;s:45:"F:\wyb\shopwm\app\admin\view\public\style.php";i:1569378565;s:50:"F:\wyb\shopwm\app\admin\view\public\inner_page.php";i:1569378565;s:52:"F:\wyb\shopwm\app\admin\view\public\frame_footer.php";i:1569378565;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(empty($is_layui) || (($is_layui instanceof \think\Collection || $is_layui instanceof \think\Paginator ) && $is_layui->isEmpty())): ?>
    <link href="/system/frame/css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <?php endif; ?>
    <link href="/static/plug/layui/css/layui.css" rel="stylesheet">
    <link href="/system/css/layui-admin.css" rel="stylesheet">
    <link href="/system/frame/css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <link href="/system/frame/css/animate.min.css" rel="stylesheet">
    <link href="/system/frame/css/style.min.css?v=3.0.0" rel="stylesheet">
    <script src="/system/frame/js/jquery.min.js"></script>
    <script src="/system/frame/js/bootstrap.min.js"></script>
    <script src="/static/plug/layui/layui.all.js"></script>
    <script>
        $eb = parent._mpApi;
        window.controlle="<?php echo strtolower(trim(preg_replace("/[A-Z]/", "_\\0", app('request')->controller()), "_"));?>";
        window.module="<?php echo app('request')->app();?>";
    </script>



    <title></title>
    
<link href="/system/module/wechat/news_category/css/style.css" type="text/css" rel="stylesheet">

    <!--<script type="text/javascript" src="/static/plug/basket.js"></script>-->
<script type="text/javascript" src="/static/plug/requirejs/require.js"></script>
<?php /*  <script type="text/javascript" src="/static/plug/requirejs/require-basket-load.js"></script>  */ ?>
<script>
    var hostname = location.hostname;
    if(location.port) hostname += ':' + location.port;
    requirejs.config({
        map: {
            '*': {
                'css': '/static/plug/requirejs/require-css.js'
            }
        },
        shim:{
            'iview':{
                deps:['css!iviewcss']
            },
            'layer':{
                deps:['css!layercss']
            }
        },
        baseUrl:'//'+hostname+'/',
        paths: {
            'static':'static',
            'system':'system',
            'vue':'static/plug/vue/dist/vue.min',
            'axios':'static/plug/axios.min',
            'iview':'static/plug/iview/dist/iview.min',
            'iviewcss':'static/plug/iview/dist/styles/iview',
            'lodash':'static/plug/lodash',
            'layer':'static/plug/layer/layer',
            'layercss':'static/plug/layer/theme/default/layer',
            'jquery':'static/plug/jquery/jquery.min',
            'moment':'static/plug/moment',
            'sweetalert':'static/plug/sweetalert2/sweetalert2.all.min',
            'formCreate':'/static/plug/form-create/form-create.min',

        },
        basket: {
            excludes:['system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
//            excludes:['system/util/mpFormBuilder','system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
        }
    });
</script>
<script type="text/javascript" src="/system/util/mpFrame.js"></script>
    
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary append-filed" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('append'); ?>',{w:1200,h:666})">添加图文消息</button>
                <div class="ibox-tools">
                    <button class="btn btn-white btn-sm" onclick="location.reload()"><i class="fa fa-refresh"></i> 刷新</button>
                </div>
                <div style="margin-top: 2rem"></div>
                <div class="row">
                    <div class="col-sm-8 m-b-xs">
                        <form action="" class="form-inline">
                            <i class="fa fa-search" style="margin-right: 10px;"></i>
                            <div class="input-group">
                                <input type="text" name="cate_name" value="<?php echo htmlentities($where['cate_name']); ?>" placeholder="请输入关键词" class="input-sm form-control" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div id="news_box">
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <div class="news_item col-sm-2" >
                            <div class="title" ><span>图文名称：<?php echo htmlentities($vo['cate_name']); ?></span></div>
                            <div class="news_tools hide">
                                <a onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('modify',array('id'=>$vo['id'])); ?>',{w:1200,h:666})" href="javascript:void(0)">编辑</a>
                                <a href="javascript:void(0)" data-url="<?php echo Url('delete',array('id'=>$vo['id'])); ?>" class="del_news_one">删除</a>
                            </div>
                        <?php if(is_array($vo['new']) || $vo['new'] instanceof \think\Collection || $vo['new'] instanceof \think\Paginator): $k = 0; $__LIST__ = $vo['new'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vvo): $mod = ($k % 2 );++$k;if($k == 1): ?>
                            <div class="news_articel_item" style="background-image:url(<?php echo htmlentities($vvo['image_input']); ?>)">
                                <p><?php echo htmlentities($vvo['title']); ?></p>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <?php else: ?>
                            <div class="news_articel_item other">
                                <div class="right-text"><?php echo htmlentities($vvo['title']); ?></div>
                                <div class="left-image" style="background-image:url(<?php echo htmlentities($vvo['image_input']); ?>);"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <?php endif; ?>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                 </div>
            </div>
        </div>
    </div>
</div>
<div style="margin-left: 10px">
    <link href="/system/frame/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<div class="row">
    <div class="col-sm-6">
        <div class="dataTables_info" id="DataTables_Table_0_info" role="alert" aria-live="polite" aria-relevant="all">共 <?php echo htmlentities($total); ?> 项</div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_simple_numbers" id="editable_paginate">
            <?php echo $page;?>
        </div>
    </div>
</div>
</div>



<script>
    $('body').on('mouseenter', '.news_item', function () {
        $(this).find('.news_tools').removeClass('hide');
    }).on('mouseleave', '.news_item', function () {
        $(this).find('.news_tools').addClass('hide');
    });
    $('.del_news_one').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('.news_item').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
</script>


</div>
</body>
</html>
