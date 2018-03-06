<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>up</title>

</head>
<body>
    <form action="<?= \ez\core\Route::createUrl('checkVerify')?>" method="post">
        <input name="VerifyCode" type="text">
        <img src="<?=\ez\core\Route::createUrl('verify', ['sda' => 'ss'])?>">
        <input type="submit" value="提交">
    </form>
</body>
</html>

