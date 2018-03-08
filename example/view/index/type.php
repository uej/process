<?php 
use ez\core\Route;
include(__DIR__ . '/../public/head.php');
?>
        <div class="layui-body">
            <div style="padding: 15px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">添加</button>
                    </div>
                </div>
                
                <table class="layui-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>类型名称</th>
                            <th>操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach($data as $val) { ?>
                        <tr>
                            <td><?=$val['ID']?></td>
                            <td><?=$val['Type']?></td>
                            <td>
                                <a class="layui-btn layui-btn-xs" href="javascript:edit(<?=$val['ID']?>)">编辑</a>
                                <a class="layui-btn layui-btn-xs layui-btn-danger" href="javascript:del(<?= $val['ID']?>)">删除</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="pages">
                    <?=$html?>
                </div>
            </div>
        </div>
  
        <div class="layui-footer">
            <!-- 底部固定区域 -->
            © 多彩贵州印象网络传媒股份有限公司
        </div>
    </div>

    <script src="/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="/layui/layui.all.js"></script>
    <script>
        // JavaScript代码区域
        var element = layui.element;
        
        var form = layui.form;

        form.on('select(city)', function(data) {
            $.ajax({
                url: '<?=  ez\core\Route::createUrl('area')?>',
                data: 'id='+data.value,
                dataType: 'json',
                type: 'get',
                success: function(json) {
                    if (json.code == 1) {
                        $('#area').empty();
                        $('#area').append('<option value=""></option>');
                        var areadata = json.data;
                        for(var i=0; i<areadata.length; i++) {
                            var html = '<option value="'+areadata[i].ID+'">'+areadata[i].Title+'</option>';
                            $('#area').append(html);
                        }
                        form.render('select');
                    } else {
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
