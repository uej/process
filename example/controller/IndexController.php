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
    
    
    /**
     * 我的申请
     * 
     * @access public
     */
    public function index() {
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
     * 添加流程
     * 
     * @access public
     */
    public function addflow() {
        $this->assign('flowtype', \example\model\Flowtype::select('*'));
        $this->display();
    }
    
    
}
