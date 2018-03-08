<?php 
use ez\core\Route;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>php工作流引擎</title>
    <link rel="stylesheet" href="/layui/css/layui.css">
    <link rel="stylesheet" href="/css/class.css">
</head>
<body>
    <link href="/js/webuploader/webuploader.css" rel="stylesheet" type="text/css"/>
    
    <div style="padding: 15px;">
        <form class="layui-form" action="<?= \ez\core\Route::createUrl('addmember')?>" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">流程类型</label>
                <div class="layui-input-inline">
                    <select id="TypeID" name="TypeID" lay-verify="required" lay-filter="city">
                        <option value="">请选择流程类型</option>
                        <?php foreach($flowtype as $val) { ?>
                        <option value="<?=$val['ID']?>"><?=$val['Type']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">流程名称</label>
                <div class="layui-input-block" style="width: 50%;">
                    <input type="text" name="Name" lay-verify="required" autocomplete="off" placeholder="请输入流程名称" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">流程说明</label>
                <div class="layui-input-block" style="width: 50%;">
                    <textarea placeholder="请输入流程说明" name="Introduce" class="layui-textarea"></textarea>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">图片</label>
                <div class="layui-input-inline">
                    <input type="text" id="Img" name="Img" lay-verify="required" placeholder="请选择图片上传" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-btn" onclick="look()">预览</div>
                <div class="layui-btn" style="padding: 0 0;" id="up">选择图片</div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">简介</label>
                <div class="layui-input-block" style="width: 50%;">
                    <textarea placeholder="请输入内容" name="Content" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">人数</label>
                <div class="layui-input-block" style="width: 50%;">
                    <input type="text" name="PeopleNum" lay-verify="required|number" autocomplete="off" placeholder="请输入队伍人数" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">参赛曲目</label>
                <div class="layui-input-inline">
                    <input type="text" name="MusicName" lay-verify="required" autocomplete="off" placeholder="请输入参赛曲目" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">不要填写"《》"</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">平均年龄</label>
                <div class="layui-input-block" style="width: 50%;">
                    <input type="text" name="Age" lay-verify="number" autocomplete="off" placeholder="请输入队伍人数" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">领队姓名</label>
                <div class="layui-input-block" style="width: 50%;">
                    <input type="text" name="Leader" lay-verify="required" autocomplete="off" placeholder="请输入队伍人数" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block" style="width: 50%;">
                    <input type="text" name="Phone" lay-verify="required|phone" autocomplete="off" placeholder="请输入队伍人数" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </form>
    </div>
    

    <script src="/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="/js/webuploader/webuploader.min.js" type="text/javascript"></script>
    <script src="/layui/layui.all.js"></script>
    <script>

        /* 上传 */
        var uploader = WebUploader.create({
            // 文件自动上传
            auto: true,

            swf: '<?=HTTPHOST?>/js/Uploader.swf',

            // 文件接收服务端。
            server: '<?=\ez\core\Route::createUrl('hindex/upload')?>',

            // 选择文件的按钮。可选。内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#up',

            // 开启分片上传
            chunked: true,

            // 分片大小
            chunkSize: 4096 * 1024,

            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: true,

            // 单个文件大小限制
            fileSingleSizeLimit: 1024*1024*0.05,

            // 文件个数限制
            fileNumLimit: 1,

            // 允许的文件类型
            accept: {
                extensions: 'jpg,jpeg',
                mimeTypes: 'image/pjpeg,image/jpeg',
            },
        });


        /* 绑定上传前文件校验事件 */
        uploader.on('beforeFileQueued', function(file) {
            if(!/^(jpg|jpeg)$/i.test(file.ext)) {
                alert('不允许的文件类型');
                return false;
            }
            if(file.size > 1024*1024*0.05) {
                alert('文件过大,请限制50k以内图片');
                return false;
            }
        });

        /* 绑定上传中事件 */
        uploader.on('uploadProgress', function(file, percentage) {
            var jindu = percentage * 100;
            if(jindu == 100) {
                jindu = 99.99;
            }
            $('#loding').text('上传中' + jindu.toFixed(2) + '%');
        });

        /* 绑定上传失败事件 */
        uploader.on('uploadError', function(file, reason) {
            $('#loding').text('上传出错'+reason);
        });

        /* 绑定上传成功事件 */
        uploader.on('uploadSuccess', function(file, response) {
            if (response.code == 0) {
                $('#loding').text('上传成功');
                $("#Img").val(response.savePath);
            } else {
                $('#loding').text('上传失败');
            }
            uploader.reset();
        });


        /* 预览 */
        function look() {
            var path = $("#Img").val();
            var layer = layui.layer;
            
            if($.trim(path) == '') {
                layer.msg('请先上传图片'); 
                return;
            }
            
            layer.open({
                title: '预览',
                type: 2, 
                area: ['40%', '50%'],
                content: "<?=HTTPHOST?>/"+path //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
            }); 
        }


        /* 二级菜单联动 */
        var form = layui.form;
        form.on('select(city)', function(data) {
            $.ajax({
                url: '<?=  ez\core\Route::createUrl('area')?>',
                data: 'id='+data.value,
                dataType: 'json',
                type: 'get',
                success: function(json) {
                    if (json.code == 1) {
                        $('#AreaID').empty();
                        $('#AreaID').append('<option value=""></option>');
                        var areadata = json.data;
                        for(var i=0; i<areadata.length; i++) {
                            var html = '<option value="'+areadata[i].ID+'">'+areadata[i].Title+'</option>';
                            $('#AreaID').append(html);
                        }
                        form.render('select');
                    } else {
                        $('#AreaID').empty();
                        $('#AreaID').append('<option value=""></option>');
                        form.render('select');
                        layui.use('layer', function(){
                            layer = layui.layer;
                            layer.msg(json.msg);
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
