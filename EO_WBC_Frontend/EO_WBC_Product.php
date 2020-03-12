<?php
class EO_WBC_Product
{
    public function __construct()
    {   
        $this->att_link =array();

        if (isset($_GET['EO_WBC'])) {
            $this->eo_wbc_render(); //Render View and Routings
            $this->eo_wbc_config();            //Disable 'Add to Cart Button' and Set 'Sold Individually'
            $this->eo_wbc_add_breadcrumb();    //Add Breadcrumb
                        
        } elseif (get_option('eo_wbc_pair_status',false)=='1') {
            $this->eo_wbc_make_pair();
        }     

    }
    //It's just temporary fix so we need strong model to handle this changes.
    public function eo_wbc_make_pair_route()
    {
        global $post;
        $url='';
        $category=$this->eo_wbc_get_category();

        if ($category==get_option('eo_wbc_first_slug')) {
            $url=get_permalink($post->ID)
                .'?EO_WBC=1&BEGIN='.sanitize_text_field($category)
                .'&STEP=1&FIRST='.$post->ID."&SECOND=&REDIRECT=1";
        } elseif ($category==get_option('eo_wbc_second_slug')) {
            $url=get_permalink($post->ID)
                .'?EO_WBC=1&BEGIN='.sanitize_text_field($category)
                .'&STEP=1&FIRST=&SECOND='.$post->ID."&REDIRECT=1";
        } 
        return $url;
    }
    //Show make pair button to only those are available for pairing as per mapping.
    public function eo_wbc_make_pair()
    {        
        $url=$this->eo_wbc_category_link();
        $category=$this->eo_wbc_get_category();

        if(
            !empty($url) 
            && !empty($category) 
            && (
                   get_option('eo_wbc_first_slug')==$category 
                || get_option('eo_wbc_second_slug')==$category
            )
        ){
            //Registering Scripts : JavaScript
            add_action( 'wp_enqueue_scripts',function(){

                global $post;
                wp_register_script(
                    'eo_wbc_add_to_cart_js',
                    plugins_url(
                        'js/eo_wbc_single_add_to_cart.js',
                        __FILE__
                    ),
                    array('jquery')
                );
                
                wp_localize_script(
                    'eo_wbc_add_to_cart_js',
                    'eo_wbc_object',
                    array('url'=>$this->eo_wbc_make_pair_route())
                );            
                wp_enqueue_script('eo_wbc_add_to_cart_js');
            });

            add_action('woocommerce_after_add_to_cart_button',function(){                
                echo "<button href='#' id='eo_wbc_add_to_cart' class='single_add_to_cart_button button alt make_pair btn btn-default'>".get_option('eo_wbc_pair_text',__('Add to pair','woo-bundle-choice'))."</button>";
            });
            //Add css to the head
            add_Action('wp_head',function(){
                ?>
                    <style>
                        .make_pair{
                            margin-left: 5px !important;
                        }
                        @media only screen and (max-width: 600px){
                            .make_pair{
                                margin-top: 1em !important;
                            }
                        }                        
                    </style>
                <?php
            });
            //Add Js to the footer.
            add_action('wp_footer',function(){
                ?>
                <!-- WBC{ WooCommerce Product Bundle Choice wiget STARTS. } -->
                <script>
                    jQuery(document).ready(function(){
                        jQuery('form.cart').prepend("<input type='hidden' name='eo_wbc_target' value='<?php echo $this->eo_wbc_get_category(); ?>'/><input type='hidden' name='eo_wbc_product_id' value='<?php global $post; echo $post->ID; ?>'/>");
                    });
                </script>
                <!-- WBC{ WooCommerce Product Bundle Choice wiget ENDS. } -->
                <?php
            });
        }       
    }

    public function eo_wbc_config()
    {        
        //Remove add to cart button
        remove_action( 
            'woocommerce_after_shop_loop_item',
            'woocommerce_template_loop_add_to_cart'
        );
        add_filter('woocommerce_product_single_add_to_cart_text', function() {
            $category = $this->eo_wbc_get_category();
            if($category == get_option('eo_wbc_first_slug')){
                return get_option('eo_wbc_add_to_cart_text_first', __('Continue', 'woo-bundle-choice'));
            } elseif( $category == get_option('eo_wbc_second_slug') ) {
                return get_option('eo_wbc_add_to_cart_text_second', __('Continue', 'woo-bundle-choice'));
            }            
        });
    }

    public function eo_wbc_add_breadcrumb()
    {   
        //Adding Breadcrumb
        add_action( 'woocommerce_before_single_product',function(){
            if(!empty($_GET) && !empty($_GET['STEP']) && !empty($_GET['BEGIN'])){
                echo EO_WBC_Breadcrumb::eo_wbc_add_breadcrumb(
                                                sanitize_text_field($_GET['STEP']),
                                                sanitize_text_field($_GET['BEGIN'])
                                            ).'<br/><br/>';
            }
        }, 15 );
    }
    
    public function eo_wbc_render()
    {   
        $redirect_url = $this->eo_wbc_product_route();
        
        //Registering Scripts : JavaScript
        add_action( 'wp_enqueue_scripts',function() use(&$redirect_url){

            global $post;
            wp_register_script(
                'eo_wbc_add_to_cart_js',
                plugins_url(
                    'js/eo_wbc_single_add_to_cart.js',
                    __FILE__
                ),
                array('jquery')
            );
            
            wp_localize_script(
                'eo_wbc_add_to_cart_js',
                'eo_wbc_object',
                array('url'=>$redirect_url)
            );            
            wp_enqueue_script('eo_wbc_add_to_cart_js');
        });
          
        //Adding own ADD_TO_CART_BUTTON
        add_action('wp_footer',function(){   
            echo "<style>.double-gutter .tmb{ width: 50%;display: inline-flex; }</style>";         
            $category = $this->eo_wbc_get_category();
            $btn_text = '';
            if($category == get_option('eo_wbc_first_slug')){
                $btn_text = get_option('eo_wbc_add_to_cart_text_first', __('Continue', 'woo-bundle-choice'));
            } elseif( $category == get_option('eo_wbc_second_slug') ) {
                $btn_text = get_option('eo_wbc_add_to_cart_text_second', __('Continue', 'woo-bundle-choice'));
            }

            if(empty($btn_text)){
                $btn_text = 'Continue';
            }
        ?>
        <!-- Created with Wordpress plugin - WooCommerce Product bundle choice -->
       	<script type="text/javascript">
    		jQuery(".single_add_to_cart_button.button.alt").ready(function(){
                jQuery('form.cart').prepend("<input type='hidden' name='eo_wbc_target' value='<?php echo $this->eo_wbc_get_category(); ?>'/><input type='hidden' name='eo_wbc_product_id' value='<?php global $post; echo $post->ID; ?>'/>");
    			jQuery(".single_add_to_cart_button.button.alt:not(.disabled)").replaceWith(
    			     "<button href='#' id='eo_wbc_add_to_cart' class='single_add_to_cart_button button alt'>"
                     +"<?php echo $btn_text; ?>"
                     +"</button>"
                    );
    			});
    	</script>
       <?php    
       });
    }
    
    public function eo_wbc_product_route(){

        global $post;
        $url=null;        
        $category=$this->eo_wbc_get_category();    

        if(sanitize_text_field($_GET['STEP'])==1) {   

            if(!empty($_GET['CART']) && !empty($_GET['REDIRECT']) && sanitize_text_field($_GET['REDIRECT'])==1) {
                //if redirec signal is set and cart data are ready then
                //relocate user to target path.                
      
                if($category==get_option('eo_wbc_first_slug')) {

                    $category_link=$this->eo_wbc_category_link();
                    $url=get_bloginfo('url').'/index.php/product-category/'.$category_link
                        .'EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=2&FIRST='.$post->ID.'&SECOND='.sanitize_text_field($_GET['SECOND'])
                        ."&CART=".sanitize_text_field($_GET['CART']).'&ATT_LINK='.implode(' ',$this->att_link).'&CAT_LINK='.substr($category_link,0,strpos($category_link,'/'));

                } elseif($category==get_option('eo_wbc_second_slug')) {

                    $category_link=$this->eo_wbc_category_link();
                    $url=get_bloginfo('url').'/index.php/product-category/'.$category_link
                        .'EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=2&FIRST='.sanitize_text_field($_GET['FIRST']).'&SECOND='.$post->ID
                        ."&CART=".sanitize_text_field($_GET['CART']).'&ATT_LINK='.implode(' ',$this->att_link).'&CAT_LINK='.substr($category_link,0,strpos($category_link,'/'));
                } 
                
                return header("Location: {$url}");
                wp_die();
                //wp_safe_redirect($url ,301 );               
            } else {

                if($category==get_option('eo_wbc_first_slug')) {

                    $url=get_permalink($post->ID)
                        .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.$post->ID.'&SECOND='.sanitize_text_field(empty($_GET['SECOND'])?'':$_GET['SECOND'])."&REDIRECT=1";

                } elseif($category==get_option('eo_wbc_second_slug')) {

                    $url=get_permalink($post->ID)
                        .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.sanitize_text_field(empty($_GET['FIRST'])?'':$_GET['FIRST']).'&SECOND='.$post->ID."&REDIRECT=1";
                } else {
                    // well due to some reason could not determine category properly so working based on begin offset recived via _GET.
                    $begin = sanitize_text_field($_GET['BEGIN']);
                    $url = get_permalink($post->ID);
                    if($begin==get_option('eo_wbc_first_slug')){

                        $url.= '?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.$post->ID.'&SECOND='.sanitize_text_field(empty($_GET['SECOND'])?'':$_GET['SECOND'])."&REDIRECT=1";

                    } elseif($begin==get_option('eo_wbc_second_slug')) {

                        $url.= '?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                        .'&STEP=1&FIRST='.sanitize_text_field(empty($_GET['FIRST'])?'':$_GET['FIRST']).'&SECOND='.$post->ID."&REDIRECT=1";
                    }                    
                }
            }            
        }
        
        elseif(sanitize_text_field($_GET['STEP'])==2) {   
            
            $review_page_url = '';

            $review_page = get_page_by_path('eo-wbc-product-review');
            
            if(empty($review_page) or is_wp_error($review_page)){
                $review_page_url = get_bloginfo('url').'/index.php'.get_option('eo_wbc_review_page');
            } else {
                $review_page_url = get_permalink($review_page);
            }           

            if(sanitize_text_field($_GET['FIRST'])==='' OR $category==get_option('eo_wbc_first_slug'))
            {
                $url=$review_page_url
                    .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                    .'&STEP=3&FIRST='.$post->ID.'&SECOND='.sanitize_text_field($_GET['SECOND']);
            }
            elseif (sanitize_text_field($_GET['SECOND'])==='' OR $category==get_option('eo_wbc_second_slug'))
            {
                $url=$review_page_url
                    .'?EO_WBC=1&BEGIN='.sanitize_text_field($_GET['BEGIN'])
                    .'&STEP=3&FIRST='.sanitize_text_field($_GET['FIRST']).'&SECOND='.$post->ID;
            }
            else
            {
                $url='';
            }            
        }        
        return $url;
    }
    
    /**
     * @return string
     *  string of mapped category to current category item
     */
    public function eo_wbc_category_link($variable_status=FALSE){        
        global $post;

        $variation=FALSE;//status if product is varaible in nature.
        $cart=NULL;//storage variable for cart data if redirected from 'Add to cart' action.
        if(isset($_GET['CART']))
        {
            $cart=str_replace("\\",'',base64_decode(sanitize_text_field($_GET['CART']),TRUE));
            $cart=(array)json_decode($cart);
                        
            if(!empty($cart['variation_id']))
            {
                $variation=$cart['variation_id'];
            }    
        }                

        // Get all category and attributes.        
        $non_var_terms = array();
        $product_terms = wc_get_product($post->ID)->get_attributes();

        if(!is_wp_error( $product_terms )  and !empty($product_terms)) {
            foreach ($product_terms as $product_taxonomy => $product_term) {
                if($product_term['variation']===false and !empty($product_term['options']) and is_array($product_term['options'])) {

                    $non_var_terms = array_merge($non_var_terms,$product_term['options']);    
                }
            }
        }
                
        $terms=wp_get_post_terms($post->ID,get_taxonomies());        
        $maps = wp_cache_get( 'cache_maps', 'eo_wbc');        
        $product_in = array();        
        // Gathering all terms for the product that is added to the cart.
        if(!is_wp_error($terms) and !empty($terms) and is_array($terms) and is_array($maps) ){
            if($variation){
                
                $variation_attributes = wc_get_product($variation)->get_attributes();                
                $variation_terms = array();                
                array_walk($terms,function($term,$index) use(&$variation_attributes,&$variation_terms){
                    if( $term->taxonomy=='product_cat' or (array_key_exists($term->taxonomy, $variation_attributes) and $variation_attributes[$term->taxonomy] == $term->slug) ) {
                        array_push($variation_terms,$term->term_taxonomy_id);
                    }
                });    
                $terms = $variation_terms;
                if (!empty($non_var_terms) and !is_wp_error($non_var_terms)) {
                    $terms = array_merge($terms,$non_var_terms);
                }                                
            } else {
                $terms = array_walk($terms,function($term,$index){
                    $terms[$index] = $term->term_taxonomy_id;
                });    
            } 

            // Gather all target of the maps           
            $map_column = 0;
            if($this->eo_wbc_get_category()==get_option('eo_wbc_first_slug')) { $map_column = 0; }
            elseif($this->eo_wbc_get_category()==get_option('eo_wbc_second_slug')) { $map_column = 1; }            
            
            $product_code = "pid_{$post->ID}";
                        
            if(!empty($terms) and is_array($terms)){
                $terms =array_filter(array_map(function($map) use(&$terms,&$map_column,&$product_code,&$product_in){
                    
                    if(array_intersect($terms,$map[$map_column])){
                        if($map_column == 0) return $map[1];
                        else return $map[0];
                    } elseif(in_array( $product_code, $map[$map_column] )) {                    
                        if($map_column == 0){
                            $product_in = array_merge( $product_in, $map[1] );
                        } else {
                            $product_in = array_merge( $product_in, $map[0] );
                        }
                        return false;
                    } else {
                        return false;
                    }                
                },$maps));
            }
        }         

        $category=array();//array to hold category slugs
        $taxonomy=array();//array to hold taxonomy slugs
        if(!is_wp_error($terms) and !empty($terms)) {
            array_walk($terms,function($term) use(&$category,&$taxonomy){
                $_term_ = null;
                if(is_array($term)) {
                    foreach ($term as $_term_) {
                        $_term_ = get_term_by('term_taxonomy_id', $_term_);
                        if(!is_wp_error($_term_) and !empty($_term_)) {
                            $_taxonomy_ = $_term_->taxonomy;                            
                            if($_taxonomy_==='product_cat') {

                                $category[]= $_term_->slug;

                            } elseif( substr($_taxonomy_,0,3) =='pa_' ) {

                                $taxonomy[substr($_term_->taxonomy,3)][] = $_term_->slug;
                            }
                        }
                    }
                } else {
                    $_term_ = get_term_by('term_taxonomy_id', $_term_);

                    if(!is_wp_error($_term_) and !empty($_term_)) {
                        $_taxonomy_ = $_term_->taxonomy;                        
                        if($_taxonomy_==='product_cat') {

                            $category[]= $_term_->slug;

                        } elseif( substr($_taxonomy_,0,3) =='pa_' ) {
                            
                            $taxonomy[substr($_term_->taxonomy,3)][] = $_term_->slug;
                        }
                    }
                }
            });
        }
        
        $link='';        
        //if category maping is available then make url filter to category
        //else add default category to the link.
        if(is_array($category) && !empty($category)) {
            $link=implode( (get_option('eo_wbc_map_cat_pref','and')==='and'?'+':',') , $category );                  
        }
        else
        {
            $link.=($this->eo_wbc_get_category()==get_option('eo_wbc_first_slug'))
                        ?
                    get_option('eo_wbc_second_slug')
                        :
                    get_option('eo_wbc_first_slug');                    
        }

        $link.="/?";           
        if(is_array($taxonomy) && !empty($taxonomy)){            
            
            $filter_query=array();
            $attr_pref=get_option('eo_wbc_map_attr_pref','or');
            $glue=($attr_pref === 'or' ? ',' : '+' );           

            foreach ($taxonomy as  $_tax => $_tems) {
                $filter_query["query_type_{$_tax}"] = $attr_pref;
                $filter_query["filter_{$_tax}"] = implode($glue,array_unique(array_filter($_tems)));
            }
            $link.=http_build_query($filter_query).'&';            
        }    

        if(!empty($product_in) && is_array($product_in)) {
            $product_in = array_filter($product_in);
            $product_in = array_map(function($product_in){ return substr($product_in,4); },$product_in);

            $link.='products_in='.implode(',',$product_in).'&';
        }             
        return $link;
    }

    /**
    *
    */
    public function eo_wbc_sub_categories($slug) {        
        
        $map_base = get_categories(array(
            'hierarchical' => 1,
            'show_option_none' => '',
            'hide_empty' => 0,
            'parent' => !empty(get_term_by('slug',$slug,'product_cat')) ?get_term_by('slug',$slug,'product_cat')->term_id : '',
            'taxonomy' => 'product_cat'
        ));
        
        $category=array();
        if(!empty($map_base)){
            foreach ($map_base as $base) {            
                $category=array_merge(array($base->slug),$this->eo_wbc_sub_categories($base->slug));            
            }    
        }        
        return $category;
    } 

    /**
     * @method Returns Current-Product's top level catgory
     * @return string
     */    
    public function eo_wbc_get_category() {

        global $post;
        $__category = false;

        $terms=wc_get_product_terms( $post->ID, 'product_cat', array( 'fields' => 'slugs' ));
        $first_cat=get_option('eo_wbc_first_slug');
        $second_cat=get_option('eo_wbc_second_slug');

        $first_cat_list=array();
        $second_cat_list=array();
        
        $first_cat_list=array_merge(array($first_cat),$this->eo_wbc_sub_categories($first_cat));
        
        $second_cat_list=array_merge(array($second_cat),$this->eo_wbc_sub_categories($second_cat));        

        
        if(@count(array_intersect($terms,$first_cat_list))>0)
        {      
            if(!empty($_GET['BEGIN']) && $_GET['BEGIN']==$first_cat){

                if(!empty($_GET['STEP']) && $_GET['STEP']==1){

                    $__category = $first_cat;

                } elseif(!empty($_GET['STEP']) && $_GET['STEP']==2){

                    $__category = $second_cat;

                } else{

                    $__category = FALSE;
                }

            } elseif(!empty($_GET['BEGIN']) && $_GET['BEGIN']==$second_cat) {

                if(!empty($_GET['STEP']) && $_GET['STEP']==1){

                    $__category = $second_cat;

                } elseif(!empty($_GET['STEP']) && $_GET['STEP']==2){

                    $__category = $first_cat;
                } else{

                    $__category = FALSE;
                }               
            }     

        } elseif(count(array_intersect($terms,$second_cat_list))>0) {

            if(!empty($_GET['BEGIN']) && $_GET['BEGIN']==$first_cat){

                if(!empty($_GET['STEP']) && $_GET['STEP']==1){

                    $__category = $first_cat;

                } elseif(!empty($_GET['STEP']) && $_GET['STEP']==2){

                    $__category = $second_cat;

                } else{

                    $__category = FALSE;
                }

            } elseif(!empty($_GET['BEGIN']) && $_GET['BEGIN']==$second_cat) {

                if(!empty($_GET['STEP']) && $_GET['STEP']==1){

                    $__category = $second_cat;

                } elseif(!empty($_GET['STEP']) && $_GET['STEP']==2){

                    $__category = $first_cat;
                } else{

                    $__category = FALSE;
                }               
            }
        }

        if(empty($__category) or ($__category!=get_option('eo_wbc_first_slug') and $__category!=get_option('eo_wbc_second_slug'))) {
            if(!empty($_GET['BEGIN']) and !empty($_GET['STEP'])){
                
                $__begin = sanitize_text_field($_GET['BEGIN']);
                $__step = sanitize_text_field($_GET['STEP']);

                if($__begin == get_option('eo_wbc_first_slug')) {
                    
                    if ($__step == 1) {
                        $__category = get_option('eo_wbc_first_slug');
                    } elseif($__step == 2) {
                        $__category = get_option('eo_wbc_second_slug');
                    }

                } elseif( $__begin == get_option('eo_wbc_second_slug')) {
                    
                    if ($__step == 1) {
                        $__category = get_option('eo_wbc_second_slug');
                    } elseif($__step == 2) {
                        $__category = get_option('eo_wbc_first_slug');
                    }
                }
            }
        }

        return $__category;
    }
}
