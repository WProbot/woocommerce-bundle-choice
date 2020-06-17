<?php

if(!class_exists('WBC_Sanitize')) {

	class WBC_Sanitize {

		private static $_instance = null;

		public static function instance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self;
				self::$_instance->methods = array(
												'sanitize_email',
												'sanitize_file_name',
												'sanitize_html_class',
												'sanitize_key',
												'sanitize_meta', 
												'sanitize_mime_type',
												'sanitize_option',
												'sanitize_sql_orderby',
												'sanitize_text_field',
												'sanitize_title',
												'sanitize_title_for_query',
												'sanitize_title_with_dashes',
												'sanitize_user',
												'esc_url_raw',
												'wp_filter_post_kses',
												'wp_filter_nohtml_kses'
											);
			}

			return self::$_instance;
		}

		private function __construct() {
			
		}

		public function clean($form) {

			foreach ($form as $key => $tab) {
		    	foreach ($tab["form"] as $fk => $fv) {		    
				    if(!empty($fv['sanitize'])) {
				    	if(is_string($fv['sanitize']) and in_array($fv['sanitize'],$this->methods)){
				    		$_POST[$fk] = call_user_func_array($fv['sanitize'],$_POST[$fk]);
				    	} elseif(is_array($fv['sanitize'])) {
				    		foreach ($fv['sanitize'] as $sanitize_method) {
				    			if(in_array($sanitize_method,$this->methods)) {
				    				$_POST[$fk] = call_user_func_array($sanitize_method,$_POST[$fk]);
				    			}				    			
				    		}
				    	}
				    }
				}
		    }			
		}
	}
}
