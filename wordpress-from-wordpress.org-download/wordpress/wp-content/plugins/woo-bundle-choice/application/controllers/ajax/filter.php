<?php
namespace eo\wbc\controllers\ajax;
class Filter
{

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		
	}

	function range($term,$min,$max,$numeric_range=FALSE) {
        $list=array();        
        if($numeric_range) {	

        	$list=get_terms(array('taxonomy'=>$term,'hide_empty'=>FALSE));  

        	$min = str_replace(',', '.', $min);
        	$max = str_replace(',', '.', $max);

        	$list=array_filter($list,function($element) use($min,$max){

				return ( (float)str_replace(',','.',$element->name) >= (float)$min AND (float)str_replace(',','.',$element->name) <= (float)$max );
			});			
			$list_slug = array();
			array_walk($list,function($e) use(&$list_slug){
            	array_push($list_slug,$e->term_id);
            });
			$list = $list_slug;					
			
        } else {

            $list=get_terms(array('taxonomy'=>$term,'hide_empty'=>FALSE));                
            
            $list_slug = array();
            array_walk($list,function($e) use(&$list_slug){
            	$list_slug[$e->term_id]=$e->slug;
            });
			$_min=array_search($min,array_values($list_slug));
			$_max=array_search($max,array_values($list_slug));								
			$list = array_slice(array_keys($list_slug),$_min,($_max-$_min)+1);
			/*$list=array_filter($list_slug,function($index) use($_min,$_max){
				return !($index >= $_min AND $index <= $_max);
			},ARRAY_FILTER_USE_KEY);*/				
			/*$list=array_column($list,'term_id');*/				
        }
        return $list;
    }

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//  Enable non table based filter that loads whole page at front :)
	//////////////////////////////////////////////////////////////////////////////////////////////////
	public function filter() {			
		//die();
		if(!empty($_GET['eo_wbc_filter'])){
			
		    add_filter('pre_get_posts',function($query ) {		    		

		        if( $query->is_main_query() ) {

		        	if(isset($_GET['products_in']) AND !empty($_GET['products_in']) ){
		        		$query->set('post__in',explode(',',$_GET['products_in']));			        	
			        }

		        
		        	if( isset($_GET['_category']) OR isset($_GET['_current_category']) ){

		        		$tax_query=array('relation' => 'AND');
		                if(!empty($_GET['_category'])) {

		                    foreach( array_unique(array_filter(explode(',', $_GET['_category']))) as $_category){
		                    	
		                        if(isset($_GET['cat_filter_'.$_category]) && (!empty($_GET['cat_filter_'.$_category])) ) {                           
		                            $tax_query[]=array(
		                                'taxonomy' => 'product_cat',
		                                'field' => 'slug',
		                                'terms' =>array_filter(explode(',',$_GET['cat_filter_'.$_category])),
		                                'compare'=>'EXISTS IN'
		                            );                    
		                        }
		                    }  
		                }
		                elseif(!empty($_GET['_current_category'])) {

		                    $tax_query[]=array(
		                        'taxonomy' => 'product_cat',
		                        'field' => 'slug',
		                        'terms' => explode(',',$_GET['_current_category']),
		                        'compare'=>'EXISTS IN'
		                    );
		                }	
		                //$query->set('tax_query',$tax_query);	                
		                /*$query->set('tax_query',$tax_query);*/

		                ///////////////////////////////////////////////
	                    //Filter section for attributes
	                    ///////////////////////////////////////////////  
		                if(!empty($_GET['_attribute'])) {

			                foreach (array_filter(explode(',', $_GET['_attribute'])) as $attr) {

			                    if(isset($_GET['min_'.$attr]) && isset($_GET['max_'.$attr])){
			                        
			                        if ( is_numeric($_GET['min_'.$attr]) && is_numeric($_GET['max_'.$attr]) ) {

			                            $tax_query[]=array(
			                                'taxonomy' => $attr,
			                                'field' => 'term_id',
			                                'terms' => $this->range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr],true),
			                                'compare'=>'EXISTS IN'
			                            );
			                        }
			                        else {

			                            $tax_query[]=array(
			                                'taxonomy' => $attr,
			                                'field' => 'term_id',
			                                'terms' => $this->range($attr,$_GET['min_'.$attr],$_GET['max_'.$attr]),
			                                'compare'=>'EXISTS IN'
			                            );
			                        }                   
			                    }
			                    elseif (isset($_GET['checklist_'.$attr]) && !empty($_GET['checklist_'.$attr])) {
			                        $tax_query[]=array(
			                            'taxonomy' => $attr,
			                            'field' => 'slug',
			                            'terms' => array_filter(explode(',',$_GET['checklist_'.$attr])),
			                            'compare'=>'EXISTS IN'
			                        );     
			                    } 
			                }
			                
			            }

			            //$query->set('tax_query',$tax_query);
			            $old_tax_query = $query->get('tax_query');
			            $old_tax_query_taxonomy = array();
			            
			            if(is_array($old_tax_query) and !empty($old_tax_query)){
			            	$old_tax_query_taxonomy = array_column($old_tax_query,'taxonomy');
			            }
			            
			            if(is_array($old_tax_query_taxonomy) AND !empty($old_tax_query_taxonomy)){
			            	if(in_array('product_visibility',$old_tax_query_taxonomy) and count($old_tax_query_taxonomy)==1) {
			            		$query->set('tax_query',$tax_query);
				            } else {
				            	$query->add('tax_query',$tax_query);
				            }	
			            } else {
			            	$query->add('tax_query',$tax_query);
			            }
			            
			            //if(is_array($old_tax_query))
			            /*echo "<pre>";			            
			            print_r($query->get('tax_query'));
		                echo "</pre>";
		                die();*/
		            }		            
		        }		       
		    });		   
		}
	}	
}