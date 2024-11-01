<?php
	/*
	Plugin Name: WP Jump  
	Description: Jump to another WordPress site. 
	Plugin URI: 
	Version: 1.0.1
	Author: Goran Petrovic	
	Author URI: https://wpgodev.com	
	Copyright 2017 WP Jump 
	License: GPL2+
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: wp-jump 
	Domain Path: /i18/
	*/

	namespace WPJump;
	

	include_once('framework/classes/class-config.php');

	global $config;
	$config 			   = Config::getInstance();	
	$config->slug 		   = 'wpjump_';
	$config->namespace 	   = 'WPJump';
	$config->DIR           = plugin_dir_path( __FILE__ );
	$config->URL  		   = plugin_dir_url( __FILE__ );
	$config->basename      = plugin_basename( dirname( __FILE__ ) );
	$config->FILE  		   = __FILE__;	

	//include helpers
	foreach (glob( 	$config->DIR .'/framework/helpers/*', GLOB_NOSORT ) as $dir_path) :
		include_once( $dir_path );
	endforeach;

	//incude classes
	foreach (glob( 	$config->DIR .'/framework/classes/*', GLOB_NOSORT ) as $dir_path) :
		include_once( $dir_path );
	endforeach;

	//include parts
	foreach (glob( 	$config->DIR .'/parts/*', GLOB_ONLYDIR ) as $dir_path) :
		$dir = explode('/', $dir_path);	
		$name =  end($dir);
		include_once( $dir_path.'/'.$name.'.php' );
	endforeach;

	//include widgets
	foreach (glob( $config->DIR.'/widgets/*', GLOB_ONLYDIR ) as $dir_path) :
		$dir = explode('/', $dir_path);	
		$name =  end($dir);
		include_once( $dir_path.'/'.$name.'.php' );
	endforeach;
	
	include_once('framework/class-bootstrap.php');
	
	
	if(!defined('WPJUMP_PARENT_DOMAIN')) :
		$wpjump_parent_domail =  get_option('wpjump_parent_domail', site_url() );		
		$wpjump_parent_domail = ($wpjump_parent_domail!="") ? $wpjump_parent_domail : site_url();
		define('WPJUMP_PARENT_DOMAIN', $wpjump_parent_domail );		
	endif;
	

	global $WPJump;					
	$WPJump = new Bootstrap();
	
?>