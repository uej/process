<?php include(__DIR__ . '/../public/head.php') ?>
        <div class="layui-body">
            <div style="padding: 15px;">
                <form class="layui-form" action="" method="get">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label" style="width: auto;">名称</label>
                            <div class="layui-input-inline" style="width: 200px;">
                                <input type="text" name="name" placeholder="队伍名称" autocomplete="off" class="layui-input">
                            </div>
                            <label class="layui-form-label" style="width: auto;">地州</label>
                            <div class="layui-input-inline">
                                <select id="city" name="city" lay-filter="city">
                                    <option value=""></option>
                                    <?php foreach ($city as $val) { ?>
                                    <option value="<?=$val['ID']?>"><?=$val['Title']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="layui-form-label" style="width: auto;">区县</label>
                            <div class="layui-input-inline" lay-filter="area">
                                <select id="area" name="area" lay-filter="area">
                                    <option value=""></option>
                                </select>
                            </div>
                            <button class="layui-btn" lay-submit lay-filter="formDemo">搜索</button>
                        </div>
                    </div>
                </form>
                <table class="layui-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>队伍名称</th>
                            <th>报名时间</th>
                            <th>州/市</th>
                            <th>区县</th>
                            <th>状态</th>
                            <th>操作</th>
                            <th>生成二维码</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach($data as $val) { ?>
                        <tr>
                            <td><?=$val['ID']?></td>
                            <td><?=$val['Name']?></td>
                            <td><?=date('Y-m-d H:i', $val['CreateTime'])?></td>
                            <td><?=getAreaName($val['CityID'])?></td>
                            <td><?=getAreaName($val['AreaID'])?></td>
                            <?php if($val['Status'] == 1) { echo '<td class="layui-bg-blue">正常';} else { echo '<td class="layui-bg-red">已删除'; } ?></td>
                            <td>
                                <a class="layui-btn layui-btn-xs" href="<?= \ez\core\Route::createUrl('hindex/editmember', ['id' => $val['ID']])?>">编辑</a>
                                <?php if($val['Status'] == 1) { ?>
                                <a class="layui-btn layui-btn-xs layui-btn-danger" href="<?= \ez\core\Route::createUrl('hindex/delmember', ['id' => $val['ID'], 'status' => 0])?>">删除</a>
                                <?php } else { ?>
                                <a class="layui-btn layui-btn-xs layui-btn-warm" href="<?= \ez\core\Route::createUrl('hindex/delmember', ['id' => $val['ID'], 'status' => 1])?>">恢复</a>
                                <?php } ?>
                            </td>
                            <td>
                                <a class="layui-btn layui-btn-xs" href="<?=\ez\core\Route::createUrl('qrcode', ['id' => $val['ID']])?>">生成二维码</a>
                                <?php if(!empty($val['QRcode'])){ ?><a class="layui-btn layui-btn-xs" href="<?=$val['QRcode']?>" target="_blank">查看</a><?php } ?>
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