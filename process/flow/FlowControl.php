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
        if ($medoo->insert('workflow', $flowData)->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '创建流程表失败'];
        }
        
        /* 流程表单添加 */
        $createRes  = Form::createDbTable($data['Form'], $flowid, $flowData['Name']);
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
        $checkdata  = self::checkData($flowID, $data);
        if ($checkdata['code'] != 1) {
            return $checkdata;
        }
        $data   = $checkdata['data'];
        
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
        if ((empty($checkdata['nowNode']) && $checkdata['nowNode'] !== 0) || empty($checkdata['orderNum'])) {
            $medoo->pdo->rollBack();
            return [
                'code'      => -3,
                'errormsg'  => '流程处理失败',
            ];
        }
        if (empty($checkdata['checkUserID']) && empty($checkdata['checkDepartmentID']) && empty($checkdata['checkRoleID'])) {
            $medoo->pdo->rollBack();
            return [
                'code'      => -3,
                'errormsg'  => '流程处理失败',
            ];
        }
        
        $program['OrderNum']            = $checkdata['orderNum'];
        $program['NowNode']             = $checkdata['nowNode'];
        $program['CheckUserID']         = $checkdata['checkUserID'];
        $program['CopyUserID']          = $checkdata['copyUserID'];
        $program['CheckDepartmentID']   = $checkdata['checkDepartmentID'];
        $program['CheckRoleID']         = $checkdata['checkRoleID'];
        
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
            if ($flowNodes[$nowNode]['type'] == 1) {
                if (!empty($flowNodes[$nowNode]['department'])) {
                    $where['DepartmentID']  = $flowNodes[$nowNode]['department'];
                }
                if (!empty($flowNodes[$nowNode]['role'])) {
                    $where['RoleID'] = $flowNodes[$nowNode]['role'];
                }
                if ($flowNodes[$nowNode]['self'] == 1) {
                    $departmentID   = $medoo->get('user', 'DepartmentID', ['ID' => $userID]);
                    $where['DepartmentID']  = $departmentID;
                }
                $nowCheckUser   = $medoo->select('user', 'ID', $where);
                $resdata['nowNode']     = $nowNode;
                $resdata['checkUserID'] = ','. implode(',', $nowCheckUser) .',';
                $resdata['orderNum']    = $orderNum;
                return $resdata;
                
            } else {
            
                if (!empty($flowNodes[$nowNode]['department'])) {
                    $resdata['checkDepartmentID']    = ',' . $flowNodes[$nowNode]['department'] . ',';
                }
                if (!empty($flowNodes[$nowNode]['role'])) {
                    $resdata['checkRoleID']  = ',' . $flowNodes[$nowNode]['role'] . ',';
                }
                if ($flowNodes[$nowNode]['self'] == 1) {
                    $resdata['checkDepartmentID']    = ',' . $medoo->get('user', 'DepartmentID', ['ID' => $userID]) . ',';
                }

                $resdata['nowNode']     = $nowNode;
                $resdata['orderNum']    = $orderNum;
                return $resdata;
            }

        } else {
            $resdata['nowNode']     = $nowNode;
            $resdata['checkUserID'] = ','. $flowNodes[$nowNode]['user'] .',';
            $resdata['orderNum']    = $orderNum;
            return $resdata;
        }
    }
    
    /**
     * 审批流程执行
     * 
     * @param integer $programID 项目id
     * @param integer $userID 审核人id
     * @param array $data 审批数据
     * @return array 审批结果
     */
    public static function doflow($programID, $userID, $data)
    {
        $medoo      = \process\Workflow::connectdb();
        $program    = $medoo->get('program', '*', ['ID' => $programID]);
        
        /* 审批前验证 */
        if (strpos($program['CheckUserID'], ",$userID,") === FALSE && call_user_func(function($medoo, $userID, $program) {
            $checkDepartment = $checkRole = FALSE;
            $user   = $medoo->get('user', '*', ['ID' => $userID]);
            if (!empty($program['CheckDepartmentID'])) {
                if (strpos($program['CheckDepartmentID'], ','.$user['DepartmentID'].',') === FALSE) {
                    $checkDepartment    = TRUE;
                }
            }
            if (!empty($program['CheckRoleID'])) {
                if (strpos($program['CheckRoleID'], ','.$user['RoleID'].',') === FALSE) {
                    $checkRole  = TRUE;
                }
            }
            
            if ($checkDepartment && $checkRole) {
                return TRUE;
            } else {
                return FALSE;
            }
        }, $medoo, $userID, $program)) {
            return [
                'code'      => 0,
                'errormsg'  => '审核人id错误',
            ];
        }
        if ($program['IsEdit'] == 1 || $program['Result'] != 0) {
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
        
        $checklog   = [
            'WorkflowID'    => $program['WorkflowID'],
            'ProgramID'     => $programID,
            'UserID'        => $userID,
            'NodeID'        => $program['NowNode'],
            'Type'          => 1,
            'Content'       => $data['Content'],
            'Files'         => $data['File'],
            'Result'        => $data['Pass'],
        ];
        $medoo->pdo->beginTransaction();
        if ($medoo->insert('flowlog', $checklog)->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '日志保存失败'];
        }
        
        $flowNodes  = $medoo->get('workflow', 'FlowNodes', ['ID' => $program['WorkflowID']]);
        $formdata   = $medoo->get("formtable{$program['WorkflowID']}", '*', ['ID' => $program['FormID']]);
        $flowNodes  = json_decode($flowNodes, true);
        
        /* 当前审批 */
        if ($data['Pass'] == 1) {
            if ($flowNodes[$program['NowNode']]['type'] == 1) {
                $checkUserID    = explode(',', trim($program['CheckUserID'], ','));
                if (!in_array($userID, $checkUserID)) {
                    $medoo->pdo->rollBack();
                    return ['code' => 0, 'errormsg' => '审核人id错误'];
                }
                $checkUserID    = array_diff($checkUserID, [$userID]);
                
                /* 当前会签完成 */ 
                if (empty($checkUserID)) {
                    if (empty($flowNodes[$program['NowNode']+1])) {
                        
                        /* 审批通过结束 */
                        return self::successOver($programID);
                    } else {
                        
                        /* 转到下一个环节 */ 
                        return self::goNextNode($flowNodes, $program);
                    }
                } else {
                    
                    /* 会签 */
                    $checkUserID    = ','.implode(',', $checkUserID).',';
                    if ($medoo->update('program', ['CheckUserID' => $checkUserID], ['ID' => $programID])->errorCode() !== '00000') {
                        $medoo->pdo->rollBack();
                        return ['code' => -2, 'errormsg' => '审批提交失败'];
                    } else {
                        $medoo->pdo->commit();
                        return ['code' => 1, 'over' => 0, 'NowNode' => $program['NowNode']];
                    }
                }
            } else {
                
                /* 或签 */
                if (empty($flowNodes[$program['NowNode']+1])) {
                        
                    /* 审批通过结束 */
                    return self::successOver($programID);
                } else {

                    /* 转到下一个环节 */
                    return self::goNextNode($flowNodes, $program);
                }
            }
        } elseif ($data['Pass'] == 2) {
            if ($flowNodes[$program['NowNode']]['type'] == 1) {
                $nodeRes    = self::getNextNode($flowNodes, $program['NowNode'], $program['UserID']);
                if (empty($nodeRes['checkUserID'])) {
                    $medoo->pdo->rollBack();
                    return ['code' => -3, 'errormsg' => '节点信息查询失败'];
                }
                $update = [
                    'CheckUserID'       => $nodeRes['checkUserID'],
                    'IsEdit'            => 1,
                    'CheckRoleID'       => NULL,
                    'CheckDepartmentID' => NULL,
                ];
                if ($medoo->update('program', $update, ['ID' => $programID])->errorCode() !== '00000') {
                    $medoo->pdo->rollBack();
                    return ['code' => -2, 'errormsg' => '审批提交失败'];
                } else {
                    return ['code' => 1, 'over' => 0, 'NowNode' => $program['NowNode']];
                }
            } else {
                $nodeRes    = self::getNextNode($flowNodes, $program['NowNode'], $program['UserID']);
                if (empty($nodeRes['checkUserID']) && empty($nodeRes['checkRoleID']) && empty($nodeRes['checkDepartmentID'])) {
                    $medoo->pdo->rollBack();
                    return ['code' => -3, 'errormsg' => '节点信息查询失败'];
                }
                $update = [
                    'CheckUserID'       => $nodeRes['checkUserID'],
                    'IsEdit'            => 1,
                    'CheckRoleID'       => $nodeRes['checkRoleID'],
                    'CheckDepartmentID' => $nodeRes['checkDepartmentID'],
                ];
                if ($medoo->update('program', $update, ['ID' => $programID])->errorCode() !== '00000') {
                    $medoo->pdo->rollBack();
                    return ['code' => -2, 'errormsg' => '审批提交失败'];
                } else {
                    return ['code' => 1, 'over' => 0, 'NowNode' => $program['NowNode']];
                }
            }
            
        } elseif ($data['Pass'] == 3) {
            $update = [
                'Result'    => 2,
            ];
            if ($medoo->update('program', $update, ['ID' => $programID])->errorCode() !== '00000') {
                $medoo->pdo->rollBack();
                return ['code' => -2, 'errormsg' => '审批提交失败'];
            } else {
                return ['code' => 1];
            }
        }
        
    }
    
    /**
     * 获取到达节点的审批人、角色，部门
     * 
     * @param array $flowNodes 流程节点数组
     * @param integer $nextNode 下一个节点id
     * @param integer $userID 申请人id
     * @return array 下个节点的数据
     */
    private static function getNextNode($flowNodes, $nextNode, $userID)
    {
        $medoo  = \process\Workflow::connectdb();
        
        if (empty($flowNodes[$nextNode]['user'])) {
            if ($flowNodes[$nextNode]['type'] == 1) {
                if (!empty($flowNodes[$nextNode]['department'])) {
                    $where['DepartmentID']  = explode(',', $flowNodes[$nextNode]['department']);
                }
                if (!empty($flowNodes[$nextNode]['role'])) {
                    $where['RoleID'] = explode(',', $flowNodes[$nextNode]['role']);
                }
                if ($flowNodes[$nextNode]['self'] == 1) {
                    $departmentID   = $medoo->get('user', 'DepartmentID', ['ID' => $userID]);
                    $where['DepartmentID']  = $departmentID;
                }
                $nowCheckUser   = $medoo->select('user', 'ID', $where);
                $resdata['nowNode']     = $nextNode;
                $resdata['checkUserID'] = ','. implode(',', $nowCheckUser) .',';
                return $resdata;
                
            } else {
            
                if (!empty($flowNodes[$nextNode]['department'])) {
                    $resdata['checkDepartmentID']    = ',' . $flowNodes[$nextNode]['department'] . ',';
                }
                if (!empty($flowNodes[$nextNode]['role'])) {
                    $resdata['checkRoleID']  = ',' . $flowNodes[$nextNode]['role'] . ',';
                }
                if ($flowNodes[$nextNode]['self'] == 1) {
                    $resdata['checkDepartmentID']    = ',' . $medoo->get('user', 'DepartmentID', ['ID' => $userID]) . ',';
                }

                $resdata['nowNode']     = $nextNode;
                return $resdata;
            }

        } else {
            $resdata['nowNode']     = $nextNode;
            $resdata['checkUserID'] = ','. $flowNodes[$nextNode]['user'] .',';
            return $resdata;
        }
    }
    
    /**
     * 审批通过完结
     * 
     * @param integer $programID 审批项目id
     * @return array 
     */
    private static function successOver($programID)
    {
        $medoo  = \process\Workflow::connectdb();
        $successdata    = [
            'CheckUserID'   => NULL,
            'CheckRoleID'   => NULL,
            'CheckDepartmentID' => NULL,
            'Result'        => 1,
        ];
        if ($medoo->update('program', $successdata, ['ID' => $programID])->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '审批提交失败'];
        } else {
            $medoo->pdo->commit();
            return ['code' => 1, 'over' => 1];
        }
    }
    
    /**
     * 把项目转到下一个流程
     * 
     * @param array $flowNodes 流程数据数组
     * @param array $program 申请项目数据数组
     * @return array 执行结果
     */
    private static function goNextNode($flowNodes, $program)
    {
        $medoo      = \process\Workflow::connectdb();
        $nextNode   = $program['NowNode']+1;
        $nextRes    = self::getNextNode($flowNodes, $nextNode, $program['UserID']);
        $updata     = [
            'CheckUserID'       => $nextRes['checkUserID'],
            'CheckRoleID'       => $nextRes['checkRoleID'],
            'CheckDepartmentID' => $nextRes['checkDepartmentID'],
            'NowNode'           => $nextNode,
        ];
        if (!empty($flowNodes[$nextNode]['copy'])) {
            $updata['CopyUserID']   = ','.implode(',', array_merge(
                explode(',', trim($program['CopyUserID'], ',')),
                explode(',', $flowNodes[$nextNode]['copy'])
            )).',';
            $updata['CopyUserID']   = ','.implode(',', $updata['CopyUserID']).',';
        }
        if ($medoo->update('program', $updata, ['ID' => $program['ID']])->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '审批提交失败'];
        } else {
            $medoo->pdo->commit();
            return ['code' => 1, 'over' => 0, 'NowNode' => $nextNode];
        }
    }
    
    /**
     * 数据编辑
     * 
     * @param integer $programID 申请项id
     * @param integer $userID 申请人id
     * @param array $data 数据
     * @return array 提交结果
     */
    public static function edit($programID, $userID, $data)
    {
        $medoo      = \process\Workflow::connectdb();
        $program    = $medoo->get('program', '*', ['ID' => $programID]);
        
        if ($program['UserID'] != $userID) {
            return [
                'code'  => '0',
                'errormsg'  => '非申请人不能修改'
            ];
        }
        if ($program['IsEdit'] != 1) {
            return [
                'code'  => '0',
                'errormsg'  => 'g该阶段不能修改'
            ];
        }
        
        /* 数据验证 */
        $checkdata  = self::checkData($program['WorkflowID'], $data);
        if ($checkdata['code'] != 1) {
            return $checkdata;
        }
        $data   = $checkdata['data'];
        
        /* 更新数据 */
        $medoo->pdo->beginTransaction();
        if ($medoo->update("formtable{$program['WorkflowID']}", $data, ['ID' => $program['FormID']])->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '数据保存失败'];
        }
        
        if ($medoo->update("program", ['IsEdit' => 0], ['ID' => $programID])->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '提交失败'];
        }
        
        $medoo->pdo->commit();
        return ['code' => 1, 'NowNode' => $program['NowNode']];
    }
    
    /**
     * 数据检查
     * 
     * @param integer $flowID 流程id
     * @param array $data 数据数组
     * @return array 检查结果
     */
    private static function checkData($flowID, $data)
    {
        $medoo  = \process\Workflow::connectdb();
        $field  = $medoo->select('flowform', '*', ['WorkflowID' => $flowID]);
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
        
        return ['code' => 1, 'data' => $data];
    }
    
    /**
     * 撤回申请（仅当没有审核记录时能撤回）
     * 
     * @param integer $programID 申请项id
     * @param integer $userID 申请人id
     * @return array 处理结果
     */
    public static function revoke($programID, $userID)
    {
        $medoo  = \process\Workflow::connectdb();
        
        $program    = $medoo->get('program', ['IsEdit', 'UserID'], ['ID' => $programID]);
        if ($program['UserID'] != $userID) {
            return [
                'code' => 0,
                'errormsg' => '仅限申请人本人操作'
            ];
        }
        if ($program['IsEdit'] == 1) {
            return [
                'code' => 0,
                'errormsg' => '已经是可编辑状态，请勿重复操作'
            ];
        }
        
        $checklog   = $medoo->has('flowlog', ['ProgramID' => $programID]);
        if ($checklog) {
            return [
                'code' => 0,
                'errormsg' => '当前状态不能撤回'
            ];
        }
        
        if ($medoo->update('program', ['IsEdit' => 1], ['ID' => $programID])->errorCode() === '00000') {
            return [
                'code' => 1,
            ];
        } else {
            return [
                'code' => -2,
                'errormsg' => '提交失败'
            ];
        }
    }
    
    
    public static function editFlow($flowID, $data)
    {
        $medoo  = \process\Workflow::connectdb();
        
        /* 检查能否修改 */
        if ($medoo->has('program', ['WorkFlowID' => $flowID, 'Result' => 0])) {
            return [
                'code'      => 0,
                'errormsg'  => '该流程还有申请未完成，暂时不能修改',
            ];
        }
        
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
        
        $medoo->pdo->beginTransaction();
        if ($medoo->update('workflow', $flowData, ['ID' => $flowID])->errorCode() !== '00000') {
            $medoo->pdo->rollBack();
            return ['code' => -2, 'errormsg' => '修改流程表失败'];
        }
        
        /* 流程表单添加 */
        $createRes  = Form::editDbTable($data['Form'], $flowID);
        if ($createRes['code'] != 1) {
            return ['code' => -2, 'errormsg' => $createRes['errormsg']];
        }
        
        $medoo->pdo->commit();
        return ['code' => 1, 'errormsg' => ''];
    }
    
}

