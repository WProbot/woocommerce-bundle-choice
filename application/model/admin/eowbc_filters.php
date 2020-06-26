<?php


/*
*	Woocommerc Category and Attribute Model.
*/

namespace eo\wbc\model\admin;

defined( 'ABSPATH' ) || exit;

class Eowbc_Filters {

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		
	}


	public function get( $form_definition ) {
		
		//loop through form tabs and save 
	    foreach ($form_definition as $key => $tab) {
	    	//loop through form fields and read values from options and store in the form_definition 
			foreach ($tab["form"] as $fk => $fv) {
				if( $fv["type"] == "table" ) {
					// wbc()->options->update_option_group( 'filters_'.$key, serialize(array()) );
					$filter_data = unserialize(wbc()->options->get_option_group('filters_'.$key,"a:0:{}"));
					//wbc()->common->pr($filter_data, false, false);

					$body = array();

					foreach ($filter_data as $rk => $rv) {
						$row = array();

						$row[] =array(
								'val' => '',
								'is_checkbox' => true, 
								'checkbox'=> array('id'=>$key.$rv[$key.'_filter'],'value'=>array(),'options'=>array(/*$rv[$key.'_filter']*/$rk=>''),'class'=>'','where'=>'in_table')
							);
						$disabled = empty($rv[$key.'_add_enabled'])?true:false;
						
						foreach ($rv as $rvk => $rvv) {

							//skip the id
							if( in_array($rvk,array($key."_dependent",$key."_type",$key."_add_help",$key."_add_help_text",$key."_add_enabled")) ) {
								continue;
							}

							if( $rvk == $key."_is_advanced" ) {
								$row[] = array( 'val' => $rvv == 1 ? "Yes" : "No" ,'disabled'=>$disabled);
							}
							else if( $rvk == $key."_add_reset_link" ) {
								$row[] = array( 'val' => $rvv == 1 ? "Yes" : "No" ,'disabled'=>$disabled);
							}
							else if( $rvk == $key."_input_type" || $rvk == $key."_filter" ) {
								$val = wbc()->common->dropdownSelectedvalueText($tab["form"][$rvk], $rvv);
								$row[] = array( 'val' => !is_array($val)?$val:$val["label"] ,'disabled'=>$disabled);	
							}
							else {
								$row[] = array( 'val' => $rvv ,'disabled'=>$disabled);
							}
						}

						$body[] = $row;
					}

					$form_definition[$key]["form"][$fk]["body"] = $body;
				}
				else {
					$form_definition[$key]["form"][$fk]["value"] = wbc()->options->get_option('filters_'.$key,$fk, isset($form_definition[$key]["form"][$fk]["value"]) ? $form_definition[$key]["form"][$fk]["value"] : '');	
				}
			    
			}
	    }

	    return $form_definition;
	}


	public function save( $form_definition ) {
		
		wbc()->sanitize->clean($form_definition);

		$res = array();
		$res["type"] = "success";
	    $res["msg"] = "";
	    //$res['post']=$_POST;
		wbc()->load->model('admin\form-builder');

		$saved_tab_key = !empty($_POST["saved_tab_key"]) ? $_POST["saved_tab_key"] : ""; 
		$skip_fileds = array('saved_tab_key');
		
		if($saved_tab_key == 'altr_filt_widgts') {
						
			if(!empty($_POST['first_category_altr_filt_widgts']) and $_POST['first_category_altr_filt_widgts']!=wbc()->options->get_option('filters_altr_filt_widgts','first_category_altr_filt_widgts') ) {

				$filter_data = unserialize(wbc()->options->get_option_group('filters_d_fconfig',"a:0:{}"));				
				if(!empty($filter_data)){
					$ids = array_keys($filter_data);
					$this->deactivate( $ids,'d_fconfig',1 );
					$ids = array();
					foreach ($filter_data as $filter_key=>$filter) {
						if($_POST['first_category_altr_filt_widgts']==$filter['filter_template']){
							//$ids[] = $filter['d_fconfig_filter'];
							$ids[] = $filter_key;
						}
					}
					if(empty($ids)) {
						wbc()->load->model('admin/sample_data/eowbc_filter_samples');
						$sample = \eo\wbc\model\admin\sample_data\Filter_Samples::instance();
						if(method_exists($sample,$_POST['first_category_altr_filt_widgts'])) {
							$sample->save(call_user_func(array($sample,$_POST['first_category_altr_filt_widgts'])));
						}
						
					} else {
						$this->activate( $ids,'d_fconfig',1);	
					}
					
				} else {
					wbc()->load->model('admin/sample_data/eowbc_filter_samples');
					$sample = \eo\wbc\model\admin\sample_data\Filter_Samples::instance();
					if(method_exists($sample,$_POST['first_category_altr_filt_widgts'])) {
						$sample->save(call_user_func(array($sample,$_POST['first_category_altr_filt_widgts'])));	
					}
				}
			}

			if(!empty($_POST['second_category_altr_filt_widgts']) and $_POST['second_category_altr_filt_widgts']!=wbc()->options->get_option('filters_altr_filt_widgts','second_category_altr_filt_widgts') ) {

				$filter_data = unserialize(wbc()->options->get_option_group('filters_s_fconfig',"a:0:{}"));
				if(!empty($filter_data)){
					$ids =array_keys($filter_data);
					$this->deactivate( $ids,'s_fconfig',1);
					$ids = array();
					foreach ($filter_data as $filter_key=>$filter) {
						if($_POST['second_category_altr_filt_widgts']==$filter['filter_template']){
							//$ids[] = $filter['s_fconfig_filter'];
							$ids[] = $filter_key;							
						}
					}
					if(empty($ids)){
						wbc()->load->model('admin/sample_data/eowbc_filter_samples');
						$sample = \eo\wbc\model\admin\sample_data\Filter_Samples::instance();
						if(method_exists($sample,$_POST['second_category_altr_filt_widgts'])){
							$res['meta'] = call_user_func(array($sample,$_POST['second_category_altr_filt_widgts']));
							$sample->save(call_user_func(array($sample,$_POST['second_category_altr_filt_widgts'])));	
						}
						
					} else {
						$this->activate( $ids,'s_fconfig',1);
					}					
				} else {
					wbc()->load->model('admin/sample_data/eowbc_filter_samples');
						$sample = \eo\wbc\model\admin\sample_data\Filter_Samples::instance();
					if(method_exists($sample,$_POST['second_category_altr_filt_widgts'])){							
						$sample->save(call_user_func(array($sample,$_POST['second_category_altr_filt_widgts'])));	
					}
				}				
			}
		}
		
	    //loop through form tabs and save 
	    
	    foreach ($form_definition as $key => $tab) {
	    	if( $key != $saved_tab_key ) {
	    		continue;
	    	}
	    	//$res['data_form'][]= $tab;
			$is_table_save = ($key != "altr_filt_widgts" and $key != "filter_setting") ? true : false;
			$table_data = array();
			$tab_specific_skip_fileds = $is_table_save ? array('eowbc_price_control_methods_list_bulk','eowbc_price_control_sett_methods_list_bulk') : array();

	    	foreach ($tab["form"] as $fk => $fv) {

			    //loop through form fields, read from POST/GET and save
			    //may need to check field type here and read accordingly only
			    //only for those for which POST is set

			    if( in_array($fv["type"], \eo\wbc\model\admin\Form_Builder::savable_types()) && isset($_POST[$fk]) ) {
			    	//skip fields where applicable
					if( in_array($fk, $skip_fileds) ) {
		    			continue;
		    		}

		    		if( in_array($fk, $tab_specific_skip_fileds) ) {
		    			continue;
		    		}
		    		//save
			    	if( $is_table_save ) {
			    		if( $fk == "d_fconfig_ordering" || $fk == "s_fconfig_ordering" )  {
			    			
			    			if($fk=='d_fconfig_ordering' and !empty($_POST['first_category_altr_filt_widgts'])){
			    				$table_data['filter_template'] = $_POST['first_category_altr_filt_widgts'];
			    			} elseif ($fk == "s_fconfig_ordering" and !empty($_POST['second_category_altr_filt_widgts'])) {
			    				$table_data['filter_template'] = $_POST['second_category_altr_filt_widgts'];
			    			}

				    		$table_data[$fk] = (int)$_POST[$fk]; 	
			    		}
			    		else {
			    			$table_data[$fk] = (empty($_POST[$fk])? $_POST[$fk]: sanitize_text_field( $_POST[$fk] ) ); 
			    		}
			    	}
			    	else {			    		
			    		wbc()->options->update_option('filters_'.$key,$fk,(empty($_POST[$fk])? $_POST[$fk]: sanitize_text_field( $_POST[$fk] ) ) );
			    	}
			    }
			}

			if( $is_table_save ) {

				$filter_data = unserialize(wbc()->options->get_option_group('filters_'.$key,"a:0:{}"));
		        
		        foreach ($filter_data as $fdkey=>$item) {
		            
		            if ($item[$key.'_filter']==$table_data[$key."_filter"] and $item['filter_template']==$table_data['filter_template']) { 
		            	$filter_data[$fdkey][$key.'_add_enabled'] = 1;
		                $res["type"] = "error";
		    			$res["msg"] = eowbc_lang('Filter Already Exists and active');
		    			wbc()->options->update_option_group( 'filters_'.$key, serialize($filter_data) );
		                return $res;
		            }
		        }

		        $filter_data[wbc()->common->createUniqueId()] = $table_data;

		        wbc()->options->update_option_group( 'filters_'.$key, serialize($filter_data));
		        
		        $res["msg"] = eowbc_lang('New Filter Added Successfully'); 
			}

	    }

        return $res;
	}

	public function delete( $ids, $saved_tab_key ,$by_key=false) {
		
		$res = array();
		$res["type"] = "success";
	    $res["msg"] = "";
	    
    	$key = $saved_tab_key;

		$filter_data = unserialize(wbc()->options->get_option_group('filters_'.$key,"a:0:{}"));
		$filter_data_updated = array();
        
        $delete_cnt = 0;
        $res["ids"] = $ids;
        $res['filters'] = $filter_data;
        foreach ($filter_data as $fdkey=>$item) {
            
            if($by_key and !in_array($fdkey, $ids)) {
            	$filter_data_updated[wbc()->common->createUniqueId()] = $item; 
            } elseif ( !$by_key and !in_array($item[$key."_filter"], $ids) ) { 
                $filter_data_updated[wbc()->common->createUniqueId()] = $item; 
            }
            else {
            	$delete_cnt++;
            }
        }

        wbc()->options->update_option_group( 'filters_'.$key, serialize($filter_data_updated) );
        $res["msg"] = $delete_cnt . " " . eowbc_lang('record(s) deleted'); 

        return $res;
	}

	public function activate( $ids, $saved_tab_key ,$by_key=false) {
		
		$res = array();
		$res["type"] = "success";
	    $res["msg"] = "";
	    
    	$key = $saved_tab_key;

		$filter_data = unserialize(wbc()->options->get_option_group('filters_'.$key,"a:0:{}"));
		$filter_data_updated = array();
        
        $delete_cnt = 0;
        foreach ($filter_data as $fdkey=>$item) {
            if($by_key and in_array($fdkey, $ids)){
            	$filter_data[$fdkey][$key."_add_enabled"]=1;
                $delete_cnt++;
            } elseif (in_array($item[$key."_filter"], $ids)) { 
                //$filter_data_updated[] = $item; 
                $filter_data[$fdkey][$key."_add_enabled"]=1;
                $delete_cnt++;
            }            
        }

        wbc()->options->update_option_group( 'filters_'.$key, serialize($filter_data) );
        $res["msg"] = $delete_cnt . " " . eowbc_lang('record(s) activated'); 

        return $res;
	}

	public function deactivate( $ids, $saved_tab_key ,$by_key=false) {
		
		$res = array();
		$res["type"] = "success";
	    $res["msg"] = "";
	    
    	$key = $saved_tab_key;

		$filter_data = unserialize(wbc()->options->get_option_group('filters_'.$key,"a:0:{}"));
		$filter_data_updated = array();
        
        $delete_cnt = 0;
        foreach ($filter_data as $fdkey=>$item) {
            
            if($by_key and in_array($fdkey, $ids)){
            	$filter_data[$fdkey][$key."_add_enabled"]=0;
                $delete_cnt++;
            } elseif (in_array($item[$key."_filter"], $ids) ) { 
                $filter_data[$fdkey][$key."_add_enabled"]=0;
                $delete_cnt++;
            }            
        }

        wbc()->options->update_option_group( 'filters_'.$key, serialize($filter_data) );
        $res["msg"] = $delete_cnt . " " . eowbc_lang('record(s) deactivated'); 

        return $res;
	}
	
}