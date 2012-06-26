<?php

	class Theme {
		
		var $menus = array();
		
		function init($options){
			
			// Load config options
			$this->__theme_config($options);
			
			// Register and load menus
			$this->__registerMenus();
			
		}
		
		private function __theme_config($options){
			
			$this->menus = $options['theme_menus'];
			
		}
		
		private function __registerMenus(){
			
			// Theme support.
			add_action( 'after_setup_theme', array( &$this, 'theme_support' ) );
			
			// Register nav menus
			register_nav_menus( $this->menus );
			
			// Add thumbnail support
			add_theme_support( 'post-thumbnails' ); 
			
		}
		
	}


?>