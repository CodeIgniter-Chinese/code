<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*插件类
*仿照wordpress，给CI的代码加入可以扩展的功能
*@author qixingyue(istrone)
*/	
class CI_Plugin {
	
	/*
	* 储存动作的数组
	*/
	public $actions;

	/*
	*储存过滤器的数组
	*/
	public $filters; 
	
	/*
	*插件目录
	*/
	private $plugin_dir;
	
	/**
	*初始化目录，调用初始化函数
	*/
	public   function __construct() {
		$this->actions = $this->filters = array();
		$this->plugin_dir = APPPATH . 'plugins/';
		$this->init();
	}
	
	/*
	*初始化插件，主要完成插件回调的注册
	*/
	public function init(){
		
		$dir_handle = opendir($this->plugin_dir);
		while( $m = readdir($dir_handle)) {
			if($m == "." || $m == "..") {
				continue;
			} else {
				$n = $this->plugin_dir . $m;
				$PL_PATH = $this->plugin_dir ;
				if(is_file($n)) {
					$f = $n;
				}else{
					$PL_PATH = $this->plugin_dir . '/' . $n . '/' ;
					$f = $n . "/" . $m . ".php";
				}
				$PL = $this;
				$PL_URL = base_url() . $PL_PATH;
				include_once  $f;
			}
		}
		closedir($dir_handle);
	}
	
	/**
	*执行一个动作
	*/
	public function do_action() {
		
		$args = func_get_args();
		$action = array_shift($args);
		
		$c = isset($this->actions[$action]) ? $this->actions[$action] : array();
		foreach ($c as $c) {
			if(is_callable($c)){
				call_user_func_array($c, $args);
			}
		}
		
	}
	
	/**
	* 给一个动作加入一个回调
	*/
	public function add_action($action,$callable) {
		$this->actions[$action][] = $callable;
	}
	
	/**
	* 删除一个回调
	*/
	public function remove_action($action,$callable) {
		
		$this->t_makeit_array($this->actions[$action]);
		$actions = $this->actions[$action];
		
		foreach ($actions as $k=>$v) {
			if($v == $callable) {
				unset($actions[$k]);
			}
		}
		
		$this->actions[$action] = $actions;
	}
	
	/**
	*添加一个过滤
	*/
	public function add_filter($filter,$callable) {
		$this->filters[$filter][] = $callable;
	}
	
	/**
	* 删除一个过滤回调
	*/
	public function remove_filter($filter,$callable) {
		$this->t_makeit_array($this->filters[$filter]);
		$filters = $this->filters[$filter];
		
		foreach ($filters as $k=>$v) {
			if($v == $callable) {
				unset($filters[$k]);
			}
		}
		
		$this->actions[$filter] = $filters;
	}
	
	/**
	*应用一个过滤
	*/
	public function apply_filter() {
		$args = func_get_args();
		$filter = array_shift($args);
		$c = isset($this->filters[$filter]) ? $this->filters[$filter] : array();
		$tmp = NULL;
		if(!empty($c)) {
			foreach ($c as $c){
				if(is_callable($c)){
					$tmp = call_user_func_array($c, $args);
				}
			}
			return $tmp;
		} else {
			return array_shift($args);
		}
	}
	
	/**
	* 确保传递的是一个数组
	*/
	public function t_makeit_array(&$obj){
		return is_array($obj) ? $obj : array();
	}
	
}
	
