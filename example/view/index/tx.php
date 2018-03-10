<!DOCTYPE html>
<html>
<head>
<title>post</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/js/jquery-3.2.1.min.js" type="text/javascript"></script>
</head>
<body>
<div>TODO write content</div>

<script>
$(function() {
    var a = {
        "Name": "事假",
        "Introduce": "用户事假申请",
        "FlowNodes": [{
            "type": 2,
            "roleid": 2,
            "self": 1,
            "need": 1
        }, {
            "type": 2,
            "roleid": 5,
            "copy": 2,
            "need": 1
        }],
        "DepartmentID": null,
        "RoleID": 1,
        "TypeID": 1,
        "UserID": null,
        "OrderRule": [{
            "type": 1,
            "value": "SJ"
        }, {
            "type": 2,
            "datetype": 2
        }, {
            "type": 3,
            "length": 3
        }],
        "From": [{
            "fieldtypeid": 1,
            "FieldName": "Content1",
            "FieldTitle": "原因",
            "Placeholder": "请输入请假原因",
            "Must": 1
        }, {
            "fieldtypeid": 8,
            "FieldName": "TimeBetween1",
            "FieldTitle": "起止时间",
            "Timetype": 3,
            "Must": 1
        }]
    };
    $.ajax({
        url: '<?=  ez\core\Route::createUrl('addflowgo')?>',
        data: a,
        type: 'post',
        dataType: 'json', 
        success: function(data) {
            
        }
    });
});


</script>
</body>
</html>

