<?php
/**
*	Ajax handler to handle ajax save request for eowbc_mapping form.	
*
*/

$res = array( "type"=>"success", "msg"=>"" );

if(wp_verify_nonce(wbc()->sanitize->post('_wpnonce'),'sample_data_jewelry')){                
	wbc()->load->model('admin/sample_data/eowbc_pair_maker');
	wbc()->load->model('admin\form-builder');
	    
	// if( isset($_POST["sub_action"]) && $_POST["sub_action"] == "bulk_delete" ) {
	// 	$res = eo\wbc\model\admin\Eowbc_Mapping::instance()->delete( $_POST["ids"], $_POST["saved_tab_key"] );
	// }
	// else {
		//$res = eo\wbc\model\admin\Eowbc_Mapping::instance()->save( eo\wbc\controllers\admin\menu\page\Mapping::get_form_definition() );
		//\eo\wbc\model\admin\sample_data\Eowbc_Pair_Maker::instance()->CatAtData__construct();
		$res /*echo*/ = \eo\wbc\model\admin\sample_data\Eowbc_Pair_Maker::instance()->create_product(intval(wbc()->sanitize->post('product_index')));
		// wp_die();
    // }
	
}
else {
	$res["type"] = "error";
	/* Language function - comment */
	$res["msg"] = __('Nonce validation failed','woo-bundle-choice');
}


echo json_encode($res);