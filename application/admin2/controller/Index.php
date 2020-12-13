<?php
namespace app\admin2\controller;
class Index extends Base{
     
    function index(){
        $this->assign('menus', $this->menus());
        return $this->fetch();
    }
    function out()
    {  
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
        $this->redirect('Login/index'); 
    }
    function menus(){
        return [
                [
                    'name' => '会员列表',
                    'childs' => [
                        [
                            'name' => '会员列表',
                            'act' => 'Member/index',
                            'childs' => []
                        ],
                        [
                            'name' => '会员关系',
                            'act' => 'Member/relation',
                            'childs' => []
                        ],
                        [
                            'name' => '加权名单',
                            'act' => 'Member/jq',
                            'childs' => []
                        ],
                        [
                            'name' => '登录日志',
                            'act' => 'Member/loginlog',
                            'childs' => []
                        ],
                    ]
                ],
                [
                    'name' => '系统设置',
                    'act' => 'System/index',
                ],
                [
                    'name' => '节点订单',
                    'act' => 'NodeOrder/index',
                ],
                [
                    'name' => '拼团管理',
                    'childs' => [
                        [
                            'name' => '拼团订单',
                            'act' => 'Pt/order',
                            'childs' => []
                        ],
                        [
                            'name' => '拼团日志',
                            'act' => 'Pt/ptlog',
                            'childs' => []
                        ]
                        
                    ]
                    
                ],
                [
                    'name' => '错误分析',
                    'childs' => [
                        [
                            'name' => '未中/退还',
                            'act' => 'Ptfx/index',
                            'childs' => []
                        ],
                        [
                            'name' => '次数统计',
                            'act' => 'Ptfx/dayc',
                            'childs' => []
                        ]
                        
                    ]
                    
                ],
                [
                    'name'=>'AG交易',
                    'childs'=>[
                        [
                            'name'=>'交易订单',
                            'act'=>'Agorder/orderlist',
                            'childs'=>[]
                        ],
                        [
                            'name'=>'交易统计',
                            'act'=>'Agorder/tj',
                            'childs'=>[]
                        ]
                    ]    
                ]
            
         ];
    }
}