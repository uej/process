<?php 
use ez\core\Route;
include(__DIR__ . '/../public/head.php');
?>
        <div class="layui-body">
            <div style="padding: 15px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <a href="javascript:open('<?=Route::createUrl('addflow')?>')" class="layui-btn">添加</a>
                    </div>
                </div>
                
                <table class="layui-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>流程名称</th>
                            <th>类型</th>
                            <th>可用部门</th>
                            <th>可用角色</th>
                            <th>可用人</th>
                            <th>操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach($data as $val) { ?>
                        <tr>
                            <td><?=$val['ID']?></td>
                            <td><?=$val['Name']?></td>
                            <td><?= \example\model\Flowtype::get('Type', ['ID' => $val['TypeID']])?></td>
                            <td><?= \example\model\Department::get('Department', ['ID' => $val['DepartmentID']])?></td>
                            <td><?= \example\model\Role::get('Role', ['ID' => $val['RoleID']])?></td>
                            <td><?= \example\model\User::select('Name', ['ID' => explode(trim($val['UserID'], ','), ',')])?></td>
                            <td>
                                <a class="layui-btn layui-btn-xs" href="<?= Route::createUrl('editflow', ['id' => $val['ID']])?>">编辑</a>
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
        
        
        /**
         * 打开添加弹出层
         * 
         * @param {string} url
         */
        function open(url) {
            layer = layui.layer;
            layer.open({
                type: 2,
                title: '添加流程',
                shadeClose: true,
                shade: 0.5,
                area: ['80%', '95%'],
                content: url //iframe的url
            }); 
        }
    </script>
</body>
</html>


