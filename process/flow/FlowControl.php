<?php
namespace process\flow;

/**
 * 流程控制类
 * 
 * @author lxj
 */
class FlowControl
{
    /**
     * 流程验证
     * 
     * @param array $flowArr 流程数据
     *      流程数据示例：
     *      [
     *          [
     *              'type'  => // 1:会签  2:或签
     *              'role'  => // 角色id
     *              'department => // 部门id
     *              'userid'=> // 审批人id
     *              'self'  => // 1：本部门对应角色签批
     *              'copy'  => // 抄送人id
     *              'need'  => [
     *                  [
     *                      'field' => // 字段
     *                      'type'  => // 1:值大于  2:值相等  3:值小于
     *                      'value' => // 需要时字段条件值
     *                  ]
     *                  [
     *                      'field' => // 字段
     *                      'type'  => // 1:值大于  2:值相等  3:值小于
     *                      'value' => // 需要时字段条件值
     *                  ]
     *              ]
     *              'needtype'  => // 1:且  2:或
     *          ]
     *      ]
     * @return array 检查结果
     * @access public
     */
    public static function checkFlow($flowArr)
    {
        $result = ['code' => 0];
        
        if (empty($flowArr)) {
            $result['errormsg'] = '流程为空';
            return $result;
        }
        
        foreach ($flowArr as $val) {
            if (!in_array($val['type'], [1,2])) {
                $result['errormsg'] = '流程审批节点类型错误';
                return $result;
            }
            if (empty($val['userid']) && empty($val['departmentid']) && empty($val['roleid'])) {
                $result['errormsg'] = '流程节点审批人不能为空';
                return $result;
            }
            if ($val['need'] != 1 && is_array($val['need'])) {
                if (count($val['need']) == 1 && $val['needtype'] != 1) {
                    $result['errormsg'] = '可选流程节点只有一个进入条件时必须为全部满足';
                    return $result;
                }
                if ($val['needtype'] != 1 && $val['needtype'] != 2) {
                    $result['errormsg'] = '请选择可选流程节点进入条件的满足类型';
                    return $result;
                }
                
                foreach ($val['need'] as $vv) {
                    if (empty($vv['field']) || !in_array($vv['type'], [1,2,3]) || empty($vv['value'])) {
                        $result['errormsg'] = '可选流程节点进入条件不完整';
                        return $result;
                    }
                }
            }
        }
        
        $result['code'] = 1;
        $result['errormsg'] = '';
        return $result;
    }
    
    /**
     * 编号生成规则验证
     * 
     * @param array $orderRule 编号生成规则
     *      流程数据示例
     *      [
     *          [
     *              'type'      => // 1:标签 2:日期 3:增长值
     *              'value'     => // type为1时有值
     *              'datetype'  => // type为2时有值  1:年 2:年月 3:年月日
     *              'length'    => // 增长值最高长度 type为3时有值
     *          ]
     *      ]
     * @return array 检查结果
     * @access public
     */
    public static function checkOrderRule($orderRule)
    {
        $result = ['code' => 0];
        
        if (empty($orderRule)) {
            $result['errormsg'] = '编号规则为空';
            return $result;
        }
        
        foreach ($orderRule as $val) {
            if (!in_array($val['type'], [1,2,3])) {
                $result['errormsg'] = '编号规则有误';
                return $result;
            }
            switch ($val['type']) {
                case 1:
                    if (empty($val['value'])) {
                        $result['errormsg'] = '标签不能为空';
                        return $result;
                    }
                    break;
                case 2:
                    if (!in_array($val['datetype'], [1,2,3])) {
                        $result['errormsg'] = '日期类型不正确';
                        return $result;
                    }
                    break;
                case 3:
                    if ($val['length'] < 1 || $val['length'] > 9) {
                        $result['errormsg'] = '增长值位数不正确';
                        return $result;
                    }
                    break;
            }
        }
        
        $result['code'] = 1;
        $result['errormsg'] = '';
        return $result;
    }
    
    /**
     * 流程创建
     * 
     * @param array $data 流程信息数据
     * @return array 创建结果
     * @access public
     */
    public static function createFlow($data)
    {
        $flowData   = [
            'Name'          => $data['Name'],
            'CreateTime'    => time(),
            'TypeID'        => intval($data['TypeID']),
            'Introduce'     => $data['Introduce'],
        ];
        if (!empty($data['DepartmentID'])) {
            $flowData['DepartmentID']   = intval($data['DepartmentID']);
        }
        if (!empty($data['RoleID'])) {
            $flowData['RoleID'] = intval($data['RoleID']);
        }
        
        /* 流程数据验证 */
        $checkres   = self::checkFlow($data['FlowNodes']);
        if ($checkres['code'] != 1) {
            return ['code' => 0, 'errormsg' => $checkres['errormsg']];
        }
        
        /* 编号规则验证 */
        $checkres   = self::checkOrderRule($data['OrderRule']);
        if ($checkres['code'] != 1) {
            return ['code' => 0, 'errormsg' => $checkres['errormsg']];
        }
        
        $flowData['FlowNodes']  = json_encode($data['FlowNodes']);
        $flowData['OrderRule']  = json_encode($data['OrderRule']);
        $flowData['UserID']     = ','.implode(',', $data['UserID']).',';
        $medoo      = \process\Workflow::connectdb();
        $medoo->pdo->beginTransaction();
        $medoo->insert('workflow', $flowData);
        $flowid = $medoo->id();
        if (!$flowid) {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '创建流程表失败'];
        }
        
        /* 流程表单添加 */
        $createRes  = Form::createDbTable($data['From'], $flowid, $flowData['Name']);
        if ($createRes['code'] != 1) {
            return ['code' => -3, 'errormsg' => $createRes['errormsg']];
        }
        
        return ['code' => 1, 'errormsg' => ''];
    }
    
    
    public static function startNew($flowID, $userID, $data)
    {
        $medoo  = \process\Workflow::connectdb();
        
        /* 验证流程表单完整性 */
        $field  = $medoo->select('*', ['WorkflowID' => $flowID]);
        foreach ($field as $val) {
            if ($val['Must'] == 1) {
                if (
                    empty(trim($data[$val['FieldName']])) &&
                    $data[$val['FieldName']] !== 0 &&
                    $data[$val['FieldName']] !== 0.0 &&
                    $data[$val['FieldName']] !== '0'
                ) {
                    return ['code' => 0, 'errormsg' => "{$val['FieldTitle']}为必填"];
                }
            }
            if (!empty(Form::$fields[$val['TypeID']]['pattern'])) {
                if (!preg_match(Form::$fields[$val['TypeID']]['pattern'], $data[$val['FieldName']])) {
                    return ['code' => 0, 'errormsg' => "{$val['FieldTitle']}格式不正确"];
                }
            }
            if ($val['TypeID'] == 7 || $val['TypeID'] == 8) {
                $data[$val['FieldName']]    = strtotime($data[$val['FieldName']]);
            }
        }
        
        $medoo->pdo->beginTransaction();
        if ($medoo->insert("formtable$flowID", $data)->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => "数据保存失败"];
        }
        $dataID = $medoo->id();
        
        $program    = [
            'UserID'        => $userID,
            'WorkflowID'    => $flowID,
            'CreateTime'    => time(),
            'FormID'        => $dataID,
            'NowNode'       => 0,
        ];
        
        /* 计算审核人id */
        
        
    }
    
    
    public static function getCheck($flowID, $nowNode = '', $checkUserID = '')
    {
        $medoo  = \process\Workflow::connectdb();
        
        if ($nowNode === '') {
            $flowdata   = $medoo->get(['OrderRule', 'FlowNodes'], ['ID' => $flowID]);
            $flowNodes  = json_decode($flowdata['FlowNodes'], TRUE);
            $orderRule  = json_decode($flowdata['OrderRule'], TRUE);
            
        }
    }
    
    
}

