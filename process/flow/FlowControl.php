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
     *              'user'=> // 审批人id
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
            if (empty($val['user']) && empty($val['department']) && empty($val['role'])) {
                $result['errormsg'] = '流程节点审批人不能为空';
                return $result;
            }
            if (!empty($val['department']) && $val['self'] == 1) {
                $result['errormsg'] = '申请人本部门审批后不能再选择部门';
                return $result;
            }
            if ($val['self'] == 1 && empty($val['role'])) {
                $result['errormsg'] = '申请人本部门审批必须设置审批角色';
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
        if (!empty($data['UserID']))
            $flowData['UserID'] = ','.implode(',', $data['UserID']).',';
        
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
        
        $medoo->pdo->commit();
        return ['code' => 1, 'errormsg' => ''];
    }
    
    /**
     * 开始一个新的流程
     * 
     * @param integer $flowID 流程id
     * @param integer $userID 申请人id
     * @param array $data 申请表单数据
     * @return array 结果数据
     */
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
        ];
        
        $checkdata  = self::getNewCheck($flowID, $userID, $data);
        if ((empty($checkdata['nowNode']) && $checkdata['nowNode'] !== 0) || empty($checkdata['orderNum']) || empty($checkdata['checkUserID'])) {
            $medoo->pdo->rollBack();
            return [
                'code'      => -3,
                'errormsg'  => '流程处理失败',
            ];
        }
        
        $program['OrderNum']    = $checkdata['orderNum'];
        $program['NowNode']     = $checkdata['nowNode'];
        $program['CheckUserID'] = $checkdata['checkUserID'];
        $program['CopyUserID']  = $checkdata['copyUserID'];
        if ($medoo->insert('program', $program)->errorCode() === '00000') {
            $programid  = $medoo->id();
            $medoo->pdo->commit();
            return [
                'code'      => 1,
                'programid' => $programid,
            ];
        } else {
            $medoo->pdo->rollBack();
            return [
                'code'      => -4,
                'errormsg'  => '提交失败',
            ];
        }
        
    }
    
    /**
     * 流程计算
     * 
     * @param integer $flowID 流程id
     * @param integer $userID 用户id
     * @param array $data 表单数据
     * @param integer $nowNode 节点
     * @return array 流程数据
     */
    private static function getNewCheck($flowID, $userID, $data, $nowNode = 0)
    {
        $medoo      = \process\Workflow::connectdb();
        $flowdata   = $medoo->get('workflow', ['OrderRule', 'FlowNodes'], ['ID' => $flowID]);
        $flowNodes  = json_decode($flowdata['FlowNodes'], TRUE);
        $orderRule  = json_decode($flowdata['OrderRule'], TRUE);

        if ($flowNodes[$nowNode]['need'] == 1) {
            return self::getNewNodeAndCheckUserAndOrdernum($flowNodes, $orderRule, $userID, $nowNode);
        } else if (is_array($flowNodes[0]['need'])) {

            /* 确定流程是否需要 */
            foreach ($flowNodes[0]['need'] as $val) {
                if ($val['type'] == 1) {
                    if ($data[$val['field']] > $val['value']) {
                        $need   = 1;
                        if ($flowNodes[0]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[0]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 2) {
                    if ($data[$val['field']] == $val['value']) {
                        $need   = 1;
                        if ($flowNodes[0]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[0]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 3) {
                    if ($data[$val['field']] < $val['value']) {
                        $need   = 1;
                        if ($flowNodes[0]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[0]['needtype'] == 1) {
                            break;
                        }
                    }
                }
            }
            if ($need == 1) {
                return self::getNewNodeAndCheckUserAndOrdernum($flowNodes, $orderRule, $userID, $nowNode);
            } else {
                return self::getCheck($flowID, $userID, $data, intval($nowNode)+1);
            }
        }
    }
    
    /**
     * 计算新申请的应到节点、审批人、申请编号
     * 
     * @param array $flowNodes 流程节点数组
     * @param array $orderRule 编号生成规则
     * @param integer $userID 申请人id
     * @param integer $nowNode 当前节点
     * @return array 应到节点、审批人、申请编号
     */
    private static function getNewNodeAndCheckUserAndOrdernum($flowNodes, $orderRule, $userID, $nowNode)
    {
        $nowNode    = intval($nowNode);
        $medoo      = \process\Workflow::connectdb();
        $resdata    = [];
        if (!empty($flowNodes[$nowNode]['copy'])) {
            $resdata['copyUserID']  = ',' . $flowNodes[$nowNode]['copy'] . ',';
        }
        
        /* 编号计算 */
        $orderNum   = '';
        foreach ($orderRule as $val) {
            if ($val['type'] == 1) {
                $orderNum   .= $val['value'];
            }
            if ($val['type'] == 2) {
                if ($val['datetype'] == 1) {
                    $orderNum   .= date('Y');
                    $datetype   = 1;
                } elseif ($val['datetype'] == 2) {
                    $orderNum   .= date('Ym');
                    $datetype   = 2;
                } elseif ($val['datetype'] == 3) {
                    $orderNum   .= date('Ymd');
                    $datetype   = 3;
                }
            }
            if ($val['type'] == 3) {
                if (empty($datetype) || $datetype == 3) {
                    $startTime  = strtotime(date('Y-m-d'));
                    $endTime    = strtotime(date('Y-m-d'). ' 23:59:59');
                } else if ($datetype == 1) {
                    $startTime  = strtotime(date('Y'). '-01-01');
                    $endTime    = strtotime(date('Y'). '-12-31 23:59:59');
                } else if ($datetype == 2) {
                    $startTime  = strtotime(date('Y-m'). '-01');
                    $endTime    = strtotime(date('Y-m'). "-01 +1 month -1 second");
                }
                $total  = $medoo->count('program', ['CreateTime[>=]' => $startTime, 'EndTime[<=]' => $endTime]);
                $total  = $total + 1;
                $num    = sprintf("%0{$val['length']}d", $total);
                $orderNum .= $num;
            }
        }
        
        
        /* 找出该流程的审批人员 */
        if (empty($flowNodes[$nowNode]['user'])) {
            if (!empty($flowNodes[$nowNode]['department'])) {
                $where['DepartmentID']  = $flowNodes[$nowNode]['department'];
            }
            if (!empty($flowNodes[$nowNode]['role'])) {
                $where['RoleID']  = $flowNodes[$nowNode]['role'];
            }
            if ($flowNodes[$nowNode]['self'] == 1) {
                $departmentID   = $medoo->get('user', 'DepartmentID', ['ID' => $userID]);
                $nowCheckUser   = $medoo->select('user', 'ID', ['DepartmentID' => $departmentID, 'RoleID' => $flowNodes[$nowNode]['role']]);
                
                $resdata['nowNode']     = $nowNode;
                $resdata['checkUserID'] = ','. implode(',', $nowCheckUser) .',';
                $resdata['orderNum']    = $orderNum;
                return $resdata;
            }
            $nowCheckUser   = $medoo->select('user', 'ID', $where);
            
            $resdata['nowNode']     = $nowNode;
            $resdata['checkUserID'] = ','. implode(',', $nowCheckUser) .',';
            $resdata['orderNum']    = $orderNum;
            return $resdata;

        } else {
            $resdata['nowNode']     = $nowNode;
            $resdata['checkUserID'] = ','. $flowNodes[$nowNode]['user'] .',';
            $resdata['orderNum']    = $orderNum;
            return $resdata;
        }
    }
    
    
    public static function doflow($programID, $userID, $data)
    {
        $medoo      = \process\Workflow::connectdb();
        $program    = $medoo->get('program', '*', ['ID' => $programID]);
        
        /* 审批前验证 */
        if (strpos($program['CheckUserID'], ",$userID,") === FALSE) {
            return [
                'code'      => 0,
                'errormsg'  => '审核人id错误',
            ];
        }
        if ($program['IsEdit'] == 1) {
            return [
                'code'      => 0,
                'errormsg'  => '该阶段不能审核',
            ];
        }
        if ($program['Status'] != 1) {
            return [
                'code'      => 0,
                'errormsg'  => '审批项不存在',
            ];
        }
        
        $flowNodes  = $medoo->get('workflow', 'FlowNodes', ['ID' => $program['WorkflowID']]);
        $formdata   = $medoo->get("formtable{$program['WorkflowID']}", '*', ['ID' => $program['FormID']]);
        $flowNodes  = json_decode($flowNodes, true);
        
        /* 当前审批 */
        if ($data['Pass'] == 1) {
            if ($flowNodes[$program['NowNode']]['type'] == 1) {
                
            } else {
                
            }
        } elseif ($data['Pass'] == 2) {
            
        } elseif ($data['Pass'] == 3) {
            
        }
        
    }
    
    
    
}

