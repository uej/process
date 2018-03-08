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
     * 
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
     * 
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
            
        }
    }
    
    public static function createFlow()
    {
        $flowData   = [
            'Name'          => filter_input(INPUT_POST, 'Name'),
            'CreateTime'    => time(),
            'TypeID'        => intval($_POST['TypeID']),
            'Introduce'     => htmlspecialchars(filter_input(INPUT_POST, 'Introduce')),
        ];
        if (!empty($_POST['DepartmentID'])) {
            $flowData['DepartmentID']   = intval($_POST['DepartmentID']);
        }
        if (!empty($_POST['RoleID'])) {
            $flowData['RoleID'] = intval($_POST['RoleID']);
        }
        
        /* 流程数据验证 */
        $checkres   = self::checkFlow($_POST['FlowNodes']);
        if ($checkres['code'] != 1) {
            return ['code' => 0, 'errormsg' => $checkres['errormsg']];
        }
        
        /* 编号规则验证 */
        $checkres   = self::checkOrderRule($_POST['OrderRule']);
        if ($checkres['code'] != 1) {
            return ['code' => 0, 'errormsg' => $checkres['errormsg']];
        }
        
        /* 流程表单添加 */
    }
}

