<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
    <link href="<?=__CSS__?>/prompt.css" rel="stylesheet" type="text/css" />
    <?php if($status == 1) { ?><title>成功</title><?php } ?>
    <?php if($status == 0) { ?><title>失败</title><?php } ?>
    <base target="_self" />
    <script>
        function Jump() { window.location.href = '<?=$jumpUrl?>'; }
        document.onload = setTimeout("Jump()" , <?=$waitSecond?>* 1000);
    </script>
</head>
<body>
    

    <?php if($status == 1) { ?>
    <div class="Prompt success_bg">
        <div class="Prompt_top success_bg"><h1>成功提示</h1></div>
        <div class="Prompt_con success_b">
            <dl>
                <dd><span class="Prompt_ok"></span></dd>
                <dd>
                    <h2><?=$message?></h2>
                    <p>提示：<span style="color:blue;font-weight:bold"><?=$waitSecond?></span> 秒后自动跳转；直接点击 <A HREF="<?=$jumpUrl?>">返回</A></p>
                </dd>
            </dl>
            <div class="c"></div>
        </div>
    </div>
    <?php } ?>


    <?php if($status == 0) { ?>
    <div class="Prompt error_bg">
    <div class="Prompt_top error_bg"><h1>错误提示</h1></div>
        <div class="Prompt_con error_b">
            <dl>
                <dd><span class="Prompt_x"></span></dd>
                <dd>
                <h2 style="color:red"><?=$message?></h2>
                    <p>提示：<span style="color:blue;font-weight:bold"><?=$waitSecond?></span> 秒后自动跳转,点击 <A HREF="<?=$jumpUrl?>">返回</A></p>
                </dd>
            </dl>
            <div class="c"></div>
        </div>
    </div>
    <?php } ?>

</body>
</html>