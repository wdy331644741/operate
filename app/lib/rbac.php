<?php
namespace Lib;
/*
--------------------------------------------------------------------------------------------------
CREATE TABLE `role` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) NOT NULL DEFAULT '' COMMENT '角色名称',
  `remark` varchar(200) DEFAULT NULL COMMENT '描述',
  `rule` varchar(255) DEFAULT NULL COMMENT '该组所具有的权限',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0：启用，1：禁用',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色表';

CREATE TABLE `node` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `controller` char(80) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(255) DEFAULT NULL COMMENT '方法',
  `remark` char(20) NOT NULL DEFAULT '' COMMENT '描述',
  `pid` int(11) NOT NULL DEFAULT '0',
  `url` varchar(200) DEFAULT NULL COMMENT 'url',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否是显示,1显示 2：隐藏',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='节点表';
-------------------------------------------------------------------------------------------------------------------
 */
class Rbac{
	protected $_config = array(
        'RBAC_ROLE'        => 'rbac_role',        // 用户组数据表名
        'RBAC_NODE'         => 'rbac_node',         // 节点表
        'RBAC_USER'         => 'member'             // 用户信息表
    );

    public function __construct() {
        $prefix = C('DB_PREFIX');
        $this->_config['RBAC_ROLE'] = $prefix.$this->_config['RBAC_ROLE'];
        $this->_config['RBAC_NODE'] = $prefix.$this->_config['RBAC_NODE'];
        $this->_config['RBAC_USER'] = $prefix.$this->_config['RBAC_USER'];
        if (C('AUTH_CONFIG')) {
            //可设置配置项 AUTH_CONFIG, 此配置项为数组。
            $this->_config = array_merge($this->_config, C('AUTH_CONFIG'));
        }
    }
    /**
     * [checkRbac 验证权限]
     * @param  [type] $role_id    [权限组id]
     * @param  [type] $controller [控制器]
     * @param  [type] $action     [方法]
     * @return [type]             [bool]
     */
   	public function checkRbac($role_id,$controller,$action){
   		
   		$getNodeIdString=$this->getNodeIdByGroupId($role_id);
   		$auth=0;
   		foreach ($getNodeIdString as $key => $value) {
   			foreach ($value['child'] as $k => $val) {
   				if(strtolower($controller)==$val['controller']){
   					for ($i=0; $i <count($val['child']) ; $i++) {
   						if($val['child'][$i]['action']==strtolower($action)){
   							$auth=1;
   						}
   					}
   				}
   			}
   		}
   		return $auth;
   	
   	}
	 /**
     * 获得权限id
     * @param integer $group_id  分组id
     * 
     */
    public function getNodeIdByGroupId($group_id){
    	$modelName=self::getModelByTableName(C('RBAC_ROLE'));
    	$roleModel=new $modelName;
    	$getRuleList=$roleModel->whereIn('id',$group_id)->get()->resultArr();
    	$ruleString='';
    	foreach ($getRuleList as $key => $value) {
    		$ruleString .=$value['rule'].",";
    	}
    	//去除重复的节点id
    	$ruleString=implode(',',array_unique(explode(',',rtrim($ruleString,','))));
    	$roleListResult=$this->getNodeInfoByRule($ruleString);
    	//整合成无限极分类
    	$roleListResult=node_merges($roleListResult);
		//dump($roleListResult);die;
    	return $roleListResult;
    }
   
    /**
     * [getRoleNameByRule 获取权限的名称]
     * @return [type] [array]
     */
    private  function getNodeInfoByRule($rule){
    	$getNodeModel=self::getModelByTableName(C('RBAC_NODE'));
    	$nodeModel=new $getNodeModel;
    	$getNodeList=$nodeModel->get()->resultArr();
		if($getNodeList){
			foreach($getNodeList as $key => $node)
			{
				$getNodeList[$key]['url'] = U((C('RBAC_URL')[$node['url_host']]),['c' => $node['controller'] , 'a' => $node['action']]);
				$getNodeList[$key]['base_url'] = $getNodeList[$key]['url'];
			}
		}
    	return $getNodeList;
    }
   

    /**
     * @param $tableName   表名
     * 获得model名称
     */
     static function getModelByTableName($tableName){
    		$preSub=C('DB_PREFIX');
		    $className = '';//class类名
		    $replacePsr = str_replace($preSub, "", $tableName);//去掉表名前缀
		    $psrArr = explode("_", $replacePsr);
		    foreach ($psrArr as $value) {
		        $className .= ucfirst($value);
		    }
		    $modelName="Model\\".$className;
		    return $modelName;
    }
    
}

?>