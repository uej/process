<?php
namespace process\flow;

/**
 * 流程表单管理
 * 
 * @author lxj
 */
class Form
{
    public static $fields   = [
        0 => ['fieldname' => 'Title', 'name' => '单行文本', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['placeholder']],
        1 => ['fieldname' => 'Content', 'name' => '多行文本', 'type' => 'text', 'formtype' => 'textarea', 'config' => ['placeholder']],
        2 => ['fieldname' => 'Phone', 'name' => '手机号', 'type' => 'char', 'length' => 11, 'formtype' => 'input', 'inputtype' => 'text', 'pattern' => '/^1(3|4|5|7|8|9)\d{9}$/', 'config' => ['placeholder']],
        3 => ['fieldname' => 'Num', 'name' => '整数', 'type' => 'int', 'length' => 11, 'formtype' => 'input', 'inputtype' => 'number', 'config' => ['placeholder']],
        4 => ['fieldname' => 'ChooseOne', 'name' => '单选框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'radio', 'config' => ['textarea', 'direction']],
        5 => ['fieldname' => 'Checkbox', 'name' => '复选框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'checkbox', 'config' => ['textarea', 'direction']],
        6 => ['fieldname' => 'Chooseval', 'name' => '下拉框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'select', 'config' => ['textarea']],
        7 => ['fieldname' => 'Time', 'name' => '时间', 'type' => 'int', 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['timetype']],
        8 => ['fieldname' => 'Timese', 'name' => '时间区间', 'type' => 'int', 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['timetype']],
        9 => ['fieldname' => 'FileName', 'name' => '上传附件', 'type' => 'varchar',  'length' => 255, 'formtype' => 'input', 'inputtype' => 'file', 'config' => ['placeholder']],
    ];
    
    public static $fieldstype   = [0,1,2,3,4,5,6,7,8,9];

    
    public static function createDbTable($formData, $flowid, $flowName)
    {
        $result = ['code' => 0];
        
        if (empty($formData)) {
            $result['errormsg'] = '流程表单未设置';
            return $result;
        }
        
        foreach ($formData as $key => $val) {
            if (empty($val)) {
                $result['errormsg'] = '流程表单未设置';
                return $result;
            }
            
            /* 表单类型设置数据验证 */
            if (!in_array($val['fieldtypeid'], self::$fieldstype)) {
                $result['errormsg'] = '流程表单类型id错误';
                return $result;
            }
            if (empty($val['FieldTitle'])) {
                $result['errormsg'] = '表单字段标题不能为空';
                return $result;
            }
            if (in_array($val['fieldtypeid'], [4,5,6])) {
                if (count(explode("\n", $val['Value'])) < 2) {
                    $result['errormsg'] = '选项过少';
                    return $result;
                }
            }
            
            $data['TypeID']         = intval($val['fieldtypeid']);
            $data['Type']           = self::$fields[$data['TypeID']]['type'];
            $data['FieldName']      = $val['FieldName'];
            if (!empty(self::$fields[$data['TypeID']]['length']))
                $data['FieldLength']    = self::$fields[$data['TypeID']]['length'];
            $data['FieldTitle']     = htmlspecialchars(trim($val['FieldTitle']));
            $data['FieldNote']      = $data['FieldTitle'];
            $data['Must']           = intval($val['Must']);
            $data['Placeholder']    = htmlspecialchars(trim($val['Placeholder']));
            if (in_array($data['TypeID'], [4,5]))
                $data['Direction']  = intval($val['Direction']);
            $data['Value']          = htmlspecialchars(trim($val['Value']));
            if (in_array($data['Type'], [7,8])) {
                $data['Timetype']   = intval($val['Timetype']);
                if (!in_array($data['Timetype'], [1,2,3])) {
                    $result['errormsg'] = '选项过少';
                    return $result;
                }
            }
            $data['WorkflowID'] = $flowid;
            $form[] = $data;
        }
        
        $medoo  = \process\Workflow::connectdb();
        $medoo->insert('flowform', $form);
        if (!$medoo->id()) {
            $medoo->pdo->rollBack();
            $result['errormsg'] = '创建流程表单失败';
            return $result;
        }
        
        /* 创建流程表单表 */
        $config = include(__DIR__ . '/../config.php');
        $sql    = "CREATE TABLE `{$config['dbPrefix']}formtable$flowid` ( "
                . "`ID` INT NOT NULL AUTO_INCREMENT , ";
        foreach ($form as $val) {
            $null   = $val['Must'] == 1 ? 'NOT NULL' : 'NULL';
            
            if ($val['Type'] == 'int' || $val['Type'] == 'text') {
                $type   = "INT";
            } else if ($val['Type'] == 'varchar' || $val['Type'] == 'char') {
                $type   = "{$val['Type']}({$val['FieldLength']})";
            }
            $sql   .= "`{$val['FieldName']}` $type , $null , COMMENT '{$val['FieldNote']}' ,";
        }
        $sql   .= "PRIMARY KEY (`ID`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '{$flowName}流程表单$flowid';";
        $pdoStatement   = $medoo->query($sql);
        if ($pdoStatement->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            $result['errormsg'] = '创建流程表单表失败';
            return $result;
        }
        
        $result['code'] = 1;
        $result['errormsg'] = '';
        return $result;
    }
}

