<?php
namespace example\controller;
use ez\core\Controller;
use ez\core\Route;

/**
 * 示例控制器
 * 
 * @author lxj
 */
class IndexController extends Controller {
    
    
    public function tx() {
        $this->display();
    }
    
    /**
     * 我的申请
     * 
     * @access public
     */
    public function index() {
        $a['撒打算'] = 'asda';
        var_dump($a);
        $this->display();
    }
    
    
    /**
     * 流程类型
     * 
     * @access public
     */
    public function type() {
        $workflowType   = new \example\model\Flowtype();
        $type   = $workflowType->findPage(15);
        $this->display($type);
    }
    
    
    /**
     * 审批流程列表
     * 
     * @access public
     */
    public function flowlist() {
        $workflow   = new \example\model\Workflow();
        $flowlist   = $workflow->findPage(15);
        $this->display($flowlist);
    }
    
    
    /**
     * 添加流程页
     * 
     * @access public
     */
    public function addflow() {
        $this->assign('flowtype', \example\model\Flowtype::select('*'));
        $this->display();
    }
    
    
    /**
     * 执行添加流程
     * 
     * @access public
     */
    public function addflowgo() {
        $workflow   = new \process\Workflow();
//        var_dump($_POST);die;
        $res    = $workflow->createFlow();
        
        var_dump($res);
        if ($res['code'] == 1) {
            
//            die(json_encode(['code' => 1, 'msg' => '添加流程成功']));
        } else {
//            die(json_encode($res));
        }
    }
    
    
    /**
     * 发起申请页
     * 
     * @access public
     */
    public function apply() {
        
    }
    
    
    /**
     * 发起流程
     * 
     * @access public
     */
    public function doapply() {
        $workflow   = new \process\Workflow();
        $res        = $workflow->startNew(1, 2, [
            'Content1'  => '阿斯顿加啊卡机双打刻录机打开asdada按时大大',
            'TimeBetween1Start' => strtotime("2018-03-14 09:00"),
            'TimeBetween1End'   => strtotime('2018-03-15 18:00'),
            'TimeBetween1Total' => 2,
        ]);
        
        dump($res);
    }
    
    
    
    
}
