<?php /*a:1:{s:61:"F:\wyb\shopwm\app\admin\view\setting\system_config\create.php";i:1569378565;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$form->getTitle()?></title>
    <script src="/static/plug/vue/dist/vue.min.js"></script>
    <link href="/static/plug/iview/dist/styles/iview.css" rel="stylesheet">
    <script src="/static/plug/iview/dist/iview.min.js"></script>
    <script src="/static/plug/jquery/jquery.min.js"></script>
    <script src="/static/plug/form-create/province_city.js"></script>
    <script src="/static/plug/form-create/form-create.min.js"></script>
    <link href="/static/plug/layui/css/layui.css" rel="stylesheet">
    <script src="/static/plug/layui/layui.all.js"></script>
    <style>
        /*弹框样式修改*/
        .ivu-modal{top: 20px;}
        .ivu-modal .ivu-modal-body{padding: 10px;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-head{padding:0 0 10px 0;}
        .ivu-modal .ivu-modal-body .ivu-modal-confirm-footer{display: none;padding-bottom: 10px;}
        .ivu-date-picker {display: inline-block;line-height: normal;width: 280px;}
        .ivu-modal-footer{display: none;}
    </style>
</head>
<body>
<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
    <ul class="layui-tab-title">
        <li <?php if($get['type'] == 0): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>0)); ?>">文本框</a> </li>
        <li <?php if($get['type'] == 1): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>1)); ?>">多行文本框</a> </li>
        <li <?php if($get['type'] == 2): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>2)); ?>">单选框</a></li>
        <li <?php if($get['type'] == 3): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>3)); ?>">文件上传</a></li>
        <li <?php if($get['type'] == 4): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>4)); ?>">多选框</a></li>
        <li <?php if($get['type'] == 5): ?>class="layui-this"<?php endif; ?>><a href="<?php echo Url('create',array('tab_id'=>app('request')->param('tab_id'),'type'=>5)); ?>">下拉框</a></li>
    </ul>
    <div class="layui-tab-content" style="height: 100px;">
        <div class="layui-tab-item layui-show" id="formdiv">

        </div>
    </div>
</div>
<script>
//    formCreate.formSuccess = function(form,$r){
//        <?php echo '<?'; ?>
//=$form->getSuccessScript()?>
//        $f.btn.loading(false)();
//    };

    (function () {
        var create = (function () {
            var getRule = function () {
                var rule = <?=json_encode($form->getRules())?>;
                rule.forEach(function (c) {
                    if ((c.type == 'cascader' || c.type == 'tree') && Object.prototype.toString.call(c.props.data) == '[object String]') {
                        if (c.props.data.indexOf('js.') === 0) {
                            c.props.data = window[c.props.data.replace('js.', '')];
                        }
                    }
                });
                return rule;
            }, vm = new Vue,name = 'formBuilderExec<?= !$form->getId() ? '' : '_'.$form->getId() ?>';
            var _b = false;
            window[name] =  function create(el, callback) {
                if(_b) return ;
                _b = true;
                if (!el) el = document.getElementById('formdiv');
                var $f = formCreate.create(getRule(), {
                    el: el,
                    form:<?=json_encode($form->getConfig('form'))?>,
                    row:<?=json_encode($form->getConfig('row'))?>,
                    submitBtn:<?=$form->isSubmitBtn() ? '{}' : 'false'?>,
                    resetBtn:<?=$form->isResetBtn() ? 'true' : '{}'?>,
                    iframeHelper:true,
                    global:{
                        upload: {
                            props:{
                                onExceededSize: function (file) {
                                    vm.$Message.error(file.name + '超出指定大小限制');
                                },
                                onFormatError: function () {
                                    vm.$Message.error(file.name + '格式验证失败');
                                },
                                onError: function (error) {
                                    vm.$Message.error(file.name + '上传失败,(' + error + ')');
                                },
                                onSuccess: function (res, file) {
                                    if (res.code == 200) {
                                        file.url = res.data.filePath;
                                    } else {
                                        vm.$Message.error(res.msg);
                                    }
                                },
                            },
                        },
                    },
                    //表单提交事件
                    onSubmit: function (formData) {
                        $f.btn.loading(true);
                        $.ajax({
                            url: '<?=$form->getAction()?>',
                            type: '<?=$form->getMethod()?>',
                            dataType: 'json',
                            data: formData,
                            success: function (res) {
                                if (res.code == 200) {
                                    vm.$Message.success(res.msg);
                                    $f.btn.loading(false);
                                    formCreate.formSuccess && formCreate.formSuccess(res, $f, formData);
                                    callback && callback(0, res, $f, formData);
                                    //TODO 表单提交成功!
                                } else {
                                    vm.$Message.error(res.msg || '表单提交失败');
                                    $f.btn.loading(false);
                                    callback && callback(1, res, $f, formData);
                                    //TODO 表单提交失败
                                }
                            },
                            error: function () {
                                vm.$Message.error('表单提交失败');
                                $f.btn.loading(false);
                            }
                        });
                    }
                });
                return $f;
            };
            return window[name];
        }());
        create();
    })();
</script>
</body>
</html>