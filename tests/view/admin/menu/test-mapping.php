<?php

class  Admin_Mapping_Test extends WP_UnitTestCase {
	
	function test_save_options() {
		$_POST['_wpnonce'] = wp_create_nonce('eowbc_mapping');
		$_POST['resolver'] = 'eowbc_mapping';
		//$_POST['eo_wbc_action'] = 'save_jpc_data';

		$tab_specific_skip_fileds = array('saved_tab_key','eowbc_price_control_methods_list_bulk');

		//options 
		$expected = array(); //serialize( array( "example_rule"=>"example_value" ) );
		wbc()->load->model('admin\form-builder');
		require_once constant('EOWBC_DIRECTORY').'application/controllers/admin/menu/page/mapping.php';
		$form_definition = eo\wbc\controllers\admin\menu\page\Mapping::get_form_definition( true );
		//loop through form tabs and set random values from samples available for each fieled  
		foreach ($form_definition as $key => $tab) {
	    	foreach ($tab["form"] as $fk => $fv) {
	    		if( !in_array($fv["type"], eo\wbc\model\admin\Form_Builder::savable_types()) || in_array($fk, $tab_specific_skip_fileds) ) {
	    			continue;
	    		}

			    $random = "";

			    //here we can override any particular field which needs specific sample values 
			    if( $fv["type"] == "text" || $fv["type"] == "color" || $fv["type"] == "hidden" || $fv["type"] == "textarea" ) {	
			    	if( isset($fv["sample_values"]) && sizeof($fv["sample_values"]) > 0 ) {
			    		$random = $fv["sample_values"][array_rand($fv["sample_values"],1)];
			    	}
			    } 
			    else if( $fv["type"] == "checkbox" || $fv["type"] == "radio" || $fv["type"] == "select" ) {	
					$random = array_rand($fv["options"],1);
			    } 

			    //post
				$_POST[$fk] = $random;

				//expected
				if( !isset($expected[$key]) ) {
					$expected[$key] = array();
				}
				$expected[$key][$fk] = $random;
			}
	    }

	    //save all three tabs
	    $_POST["saved_tab_key"] = "prod_mapping_pref";
	    include constant('EOWBC_DIRECTORY').'application/controllers/ajax/'.sanitize_text_field($_POST['resolver']).'.php';

	    $_POST["saved_tab_key"] = "map_creation_modification";
	    include constant('EOWBC_DIRECTORY').'application/controllers/ajax/'.sanitize_text_field($_POST['resolver']).'.php';

		foreach ($expected as $key => $value) {
			$is_table_save = $key != "prod_mapping_pref" ? true : false;

			$result = get_option('eowbc_option_mapping_'.$key, serialize( array() ) ); 

			if( $is_table_save ) {
				$result = unserialize( $result ); 
				$result = $result[0];
				unset($result["id"]);

				wbc()->common->pr($value);
				wbc()->common->pr(($result));

				$this->assertEquals( wbc()->common->consistsOfTheSameValues( $value, $result ), true );
			}
			else {
				wbc()->common->pr($value);
				wbc()->common->pr(unserialize($result));

				$this->assertEquals( wbc()->common->consistsOfTheSameValues( $value, unserialize($result) ), true );
			}
		}

		//here test delete action as well, for the last two tabs

	}	
}