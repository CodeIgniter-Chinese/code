<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*插件类
*仿照wordpress，给CI的代码加入可以扩展的功能
*@author qixingyue(istrone)
*/	
class CI_Plugin {
	
	public $actions;
	public $filters; 
	
	private $plugin_dir;
	
	public   function __construct() {
		$this->actions = $this->filters = array();
		$this->plugin_dir = APPPATH . 'plugins/';
		$this->init();
	}
	
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
	
	public function add_action($action,$callable) {
		$this->actions[$action][] = $callable;
	}
	
	public function remove_action($action,$callable) {
		
		t_makeit_array($this->actions[$action]);
		$actions = $this->actions[$action];
		
		foreach ($actions as $k=>$v) {
			if($v == $callable) {
				unset($actions[$k]);
			}
		}
		
		$this->actions[$action] = $actions;
	}
	
	public function add_filter($filter,$callable) {
		$this->filters[$filter][] = $callable;
	}
	
	public function remove_filter($filter,$callable) {
		t_makeit_array($this->filters[$action]);
		$filters = $this->filters[$action];
		
		foreach ($filters as $k=>$v) {
			if($v == $callable) {
				unset($filters[$k]);
			}
		}
		
		$this->actions[$filter] = $filters;
	}
	
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
	
}
	
