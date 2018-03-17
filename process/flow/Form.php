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
        8 => ['fieldname' => 'TimeBetween', 'name' => '时间区间', 'type' => 'int', 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['timetype']],
        9 => ['fieldname' => 'FileName', 'name' => '上传附件', 'type' => 'varchar',  'length' => 255, 'formtype' => 'input', 'inputtype' => 'file', 'config' => ['placeholder']],
    ];
    
    public static $fieldstype   = [0,1,2,3,4,5,6,7,8,9];

    
    /**
     * 添加流程表单数据和创建表单表
     * 
     * @param array $formData 表单字段数据
     * @param integer $flowID 流程id
     * @param string $flowName 流程名称
     * @return array 结果
     */
    public static function createDbTable($formData, $flowID, $flowName)
    {
        $result = ['code' => 0];
        
        if (empty($formData)) {
            $result['errormsg'] = '流程表单未设置';
            return $result;
        }
        
        $fieldRes   = self::createField($formData, $flowID);
        if ($fieldRes['code'] != 1) {
            return $fieldRes;
        } elseif (empty($fieldRes['data'])) {
            $result['errormsg'] = '流程表单生成失败';
            return $result;
        }
        $form   = $fieldRes['data'];
        
        $medoo  = \process\Workflow::connectdb();
        if ($medoo->insert('flowform', $form)) {
            $medoo->pdo->rollBack();
            $result['errormsg'] = '创建流程表单失败';
            return $result;
        }
        
        /* 创建流程表单表 */
        $config = include(__DIR__ . '/../config.php');
        $sql    = "CREATE TABLE `{$config['dbPrefix']}formtable$flowID` ( "
                . "`ID` INT NOT NULL AUTO_INCREMENT , ";
        foreach ($form as $val) {
            $null   = $val['Must'] == 1 ? 'NOT NULL' : 'NULL';
            
            if ($val['Type'] == 'int' || $val['Type'] == 'text') {
                $type   = $val['Type'];
            } else if ($val['Type'] == 'varchar' || $val['Type'] == 'char') {
                $type   = "{$val['Type']}({$val['FieldLength']})";
            }
            $sql   .= "`{$val['FieldName']}` $type $null COMMENT '{$val['FieldNote']}' ,";
        }
        $sql   .= "PRIMARY KEY (`ID`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = '{$flowName}流程表单$flowID';";
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
    
    /**
     * 编辑数据表结构和字段表
     * 
     * @param array $formData 表单修改数据
     * @param integer $flowID 流程id
     * @return array 操作结果
     */
    public static function editDbTable($formData, $flowID)
    {
        $medoo  = \process\Workflow::connectdb();
        if (!empty($formData['add'])) {
            $resdata    = self::createField($formData['add'], $flowID);
            if ($resdata['code'] != 1) {
                return $resdata;
            }
            
            $form   = $resdata['data'];
        }
        
        $config = include(__DIR__ . '/../config.php');
        if (!empty($formData['delete'])) {
            $filed  = $medoo->select('flowform', 'FieldName', ['ID' => $formData['delete']]);
            if ($medoo->delete('flowform', ['ID' => $formData['delete']])->errorCode() !== '00000') {
                $medoo->pdo->rollBack();
                return ['code' => -2, 'errormsg' => '删除字段失败'];
            }
            $sql    = "ALTER TABLE `{$config['dbPrefix']}formtable$flowID` ";
            foreach ($filed as $val) {
                $sql    .= "DROP `$val`,";
            }
            $sql    = trim($sql, ',') . ';';
            if ($medoo->query($sql)->errorCode() !== '00000') {
                $medoo->pdo->rollBack();
                return ['code' => -3, 'errormsg' => '删除字段失败'];
            }
        }
        if (!empty($form)) {
            if ($medoo->insert('flowform', $form)->errorCode() !== '00000') {
                $medoo->pdo->rollBack();
                return ['code' => -2, 'errormsg' => '添加字段失败'];
            }
            
            $sql    = "ALTER TABLE `{$config['dbPrefix']}formtable$flowID` ";
            foreach ($form as $val) {
                $null   = $val['Must'] == 1 ? 'NOT NULL' : 'NULL';
                if ($val['Type'] == 'int' || $val['Type'] == 'text') {
                    $type   = $val['Type'];
                } else if ($val['Type'] == 'varchar' || $val['Type'] == 'char') {
                    $type   = "{$val['Type']}({$val['FieldLength']})";
                }
                $sql   .= "ADD `{$val['FieldName']}` $type $null COMMENT '{$val['FieldNote']}',";
            }
            $sql    = trim($sql, ',') . ';';
            if ($medoo->query($sql)->errorCode() !== '00000') {
                $medoo->pdo->rollBack();
                return ['code' => -3, 'errormsg' => '添加字段失败'];
            }
        }
        
        return ['code' => 1];
    }
    
    /**
     * 生成字段信息数组
     * 
     * @param array $formData 字段信息数据
     * @param integer $flowID 流程id
     * @return array 生成的数据数组和结果
     */
    private static function createField($formData, $flowID)
    {
        foreach ($formData as $key => $val) {
            if (empty($val)) {
                $result['errormsg'] = '流程表单未设置';
                return $result;
            }
            
            /* 表单类型设置数据验证 */
            if (!in_array($val['fieldtypeid'], self::$fieldstype)) {
                return ['code' => 0, 'errormsg' => '流程表单类型id错误'];
            }
            if (empty($val['FieldTitle'])) {
                return ['code' => 0, 'errormsg' => '表单字段标题不能为空'];
            }
            if (in_array($val['fieldtypeid'], [4,5,6])) {
                if (count(explode("\n", $val['Value'])) < 2) {
                    return ['code' => 0, 'errormsg' => '选项过少'];
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
            if (!empty($val['Placeholder']))
                $data['Placeholder']    = htmlspecialchars(trim($val['Placeholder']));
            if (in_array($data['TypeID'], [4,5]))
                $data['Direction']  = intval($val['Direction']);
            if (in_array($val['fieldtypeid'], [4,5,6]))
                $data['Value']      = htmlspecialchars(trim($val['Value']));
            if (in_array($data['Type'], [7,8])) {
                $data['Timetype']   = intval($val['Timetype']);
                if (!in_array($data['Timetype'], [1,2,3])) {
                    return ['code' => 0, 'errormsg' => '选项过少'];
                }
            }
            $data['WorkflowID'] = $flowID;
            
            if ($val['fieldtypeid'] == 8) {
                $data['FieldName']  = $val['FieldName'] . 'Start';
                $form[] = $data;
                $data['FieldName']  = $val['FieldName'] . 'End';
                $form[] = $data;
                $data['FieldName']  = $val['FieldName'] . 'Total';
                $data['FieldTitle'] = $data['FieldNote'] = '总时长(天)';
                $data['Type']       = 'double';
                $form[] = $data;
                continue;
            }
            $form[] = $data;
        }
        
        return ['code' => 1, 'data' => $form];
    }
    
}

