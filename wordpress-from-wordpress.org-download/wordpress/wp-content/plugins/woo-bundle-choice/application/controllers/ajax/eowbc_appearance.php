<?php
/**
*	Ajax handler to handle ajax save request for eowbc_appearance form.	
*
*/

$res = array( "type"=>"success", "msg"=>"" );

if(wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']),'eowbc_appearance')){                

	wbc()->load->model('admin/eowbc_appearance');
    wbc()->load->model('admin\form-builder');
    $res = eo\wbc\model\admin\Eowbc_Appearance::instance()->save( eo\wbc\controllers\admin\menu\page\Appearance::get_form_definition() );
    
}
else {
	$res["type"] = "error";
	$res["msg"] = "Nonce validation failed";
}


// echo json_encode($res);
wbc()->rest->response($res);