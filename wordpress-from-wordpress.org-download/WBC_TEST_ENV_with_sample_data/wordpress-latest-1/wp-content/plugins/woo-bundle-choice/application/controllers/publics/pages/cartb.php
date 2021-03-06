<?php
namespace eo\wbc\controllers\publics\pages;
defined( 'ABSPATH' ) || exit;

class Cart {

    private static $_instance = null;

    public static function instance() {
        if ( ! isset( self::$_instance ) ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __construct() {        
    }

    public function init() {
        if(isset($_GET['EO_WBC_REMOVE'])){
            $this->eo_wbc_remove();
        }     

        if(wbc()->session->get('EO_WBC_SETS'))//Destroy EO_WBC_SETS data if session is available
        {
            wbc()->session->set('EO_WBC_SETS',NULL);
        }

        if(isset($_GET['empty_cart']) && wbc()->sanitize->get('empty_cart')==1){
            $this->eo_wbc_empty_cart();
        }        
        
        
        $this->eo_wbc_add_css();
        $this->eo_wbc_render();    
    }    
   
    public function eo_wbc_remove(){
    
        $eo_wbc_maps=wbc()->session->get('EO_WBC_MAPS',array());   
        if(isset($eo_wbc_maps[wbc()->sanitize->get('EO_WBC_REMOVE')])) {
            unset($eo_wbc_maps[wbc()->sanitize->get('EO_WBC_REMOVE')]);
            wbc()->session->set('EO_WBC_MAPS',$eo_wbc_maps);
                        
            //Reload cart data
            WC()->cart->empty_cart();           
            foreach ($eo_wbc_maps as $index=>$set)
            {               
                if($set["FIRST"]){          
                    wc()->cart->add_to_cart(
                        $set["FIRST"][0],
                        $set["FIRST"][1],
                        ($set["FIRST"][2]=='0'?NULL:$set["FIRST"][2]),
                        ($set["FIRST"][2]=='0'?NULL:wbc()->wc->eo_wbc_get_product_variation_attributes($set["FIRST"][2]))
                      );
                }

                if($set["SECOND"])
                {
                    wc()->cart->add_to_cart(
                        $set["SECOND"][0],
                        $set["SECOND"][1],
                        ($set["SECOND"][2]=='0'?NULL:$set["SECOND"][2]),
                        ($set["SECOND"][2]=='0'?NULL:wbc()->wc->eo_wbc_get_product_variation_attributes($set["SECOND"][2]))
                      );
                }
            }   
        }                
    } 
    
    public function eo_wbc_empty_cart(){
        //empty cart on user request
        wbc()->session->set('EO_WBC_SETS',NULL);
        wbc()->session->set('EO_WBC_MAPS',NULL);
        wbc()->session->set('EO_WBC_CART',NULL);
        WC()->cart->empty_cart();
        exit(wp_redirect(wbc()->wc->eo_wbc_get_cart_url()));
    }
    
    public function eo_wbc_add_css()
    {
        //Adding JQuery Library....
        add_action( 'wp_enqueue_scripts',function(){
            // wp_enqueue_script('JQuery');
            // wp_register_script('eo_wbc_cart_js',plugins_url('/js/eo_wbc_cart.js',__FILE__));
            // wp_enqueue_script('eo_wbc_cart_js');
            wbc()->load->asset('js','publics/eo_wbc_cart',array('jquery'));
        });
    }
    
    public function eo_wbc_cart_service()
    {       
        $eo_wbc_maps=wbc()->session->get('EO_WBC_MAPS',array());
        foreach (wc()->cart->cart_contents as $cart_key=>$cart_item)
        {
            $product_count=0;
            $single_count=0;
            foreach ($eo_wbc_maps as $map)
            {
                if($map["FIRST"][0]==$cart_item["product_id"] && $map["FIRST"][2]==$cart_item["variation_id"]){
                    $product_count+=$map["FIRST"][1];
                    if (!$map["SECOND"]){
                        $single_count+=$map["FIRST"][1];
                    }
                }    
                if ($map["SECOND"] && $map["SECOND"][0]==$cart_item["product_id"] && $map["SECOND"][2]==$cart_item["variation_id"])
                {
                    $product_count+=$map["SECOND"][1];
                }
            }
            
            if ($product_count>0)
            {
                if ($product_count<$cart_item["quantity"])
                {
                    if($single_count>0)
                    {
                        foreach ($eo_wbc_maps as $map_key=>$map)
                        {
                            if($map["FIRST"][0]==$cart_item["product_id"] && $map["FIRST"][2]==$cart_item["variation_id"])
                            {
                                unset($eo_wbc_maps[$map_key]);
                            }
                        }                       
                        $eo_wbc_maps[]=array(
                            "FIRST"=>array(
                                (string)$cart_item["product_id"],
                                (string)($cart_item["quantity"]-$product_count)+$single_count,
                                (string)$cart_item["variation_id"]
                            ),
                            "SECOND"=>FALSE
                        );
                    }
                    else
                    {
                        $eo_wbc_maps[]=array(
                            "FIRST"=>array(
                                (string)$cart_item["product_id"],
                                (string)($cart_item["quantity"]-$product_count),
                                (string)$cart_item["variation_id"]
                            ),
                            "SECOND"=>FALSE
                        );
                    }
                }
            }
            else
            {
                $eo_wbc_maps[]=array(
                    "FIRST"=>array(
                        (string)$cart_item["product_id"],
                        (string)$cart_item["quantity"],
                        (string)$cart_item["variation_id"]
                    ),
                    "SECOND"=>FALSE
                );
            }
        }
        wbc()->session->set('EO_WBC_MAPS',apply_filters('eowbc_cart_render_maps',$eo_wbc_maps));      

    }
    
    public function eo_wbc_render()
    {
        //Removing Cart Table data.....
        //Adding Custome Cart Table Data.......        
        add_action('woocommerce_before_cart_contents',function(){
            $this->eo_wbc_cart_service();
            ?>
                <!-- Created with Wordpress plugin - WooCommerce Product bundle choice -->
                <style>
                    tr.cart_item
                    {
                        display: none;
                    }
                    
                    [name="update_cart"]
                    {
                        display: none !important;   
                    }

                    .shop_table td{
                        font-size: medium;                         
                        vertical-align: middle !important;
                    }



                    .woocommerce table.shop_table th
                    {                        
                        padding-right: 2em !important;                        
                    }
                    
                    #eo_wbc_extra_btn a{
                        margin-bottom: 2em;
                    }
                    #eo_wbc_extra_btn::after{
                        content: '\A';
                        white-space: pre;                         
                    }
                    [data-title="Price"],[data-title="Quantity"],[data-title="Cost"]{
                        text-align: right !important;
                    }
                    @media screen and (max-width: 720px) {
                        td[data-title="Thumbnail"] {
                            display: flex !important;
                        }
                        span.column::before{
                            content: '\A\A';
                            white-space: pre;
                        }
                        #eo_wbc_extra_btn{
                            display: grid;
                        }                                             
                    }                    
                </style>
            <?php 
            $maps=wbc()->session->get('EO_WBC_MAPS');
            foreach ($maps as $index=>$map){
                
                $this->eo_wbc_cart_ui($index,$map);               
            }
        });
            
            // Adding Buttons
            // 1 Continue Shopping
            // 2 Empty Cart
          /*  add_action('woocommerce_after_cart_table',function(){
                echo '<div style="float:right;" id="eo_wbc_extra_btn"><a href="'.get_bloginfo('url').'" class="checkout-button button alt wc-backword">Continue Shopping</a><br style="display:none;" />
              <a href="./?EO_WBC=1&empty_cart=1" class="checkout-button button alt wc-backword">Empty Cart</a></div><div style="clear:both;"></div>';
            });*/
    }

    public function eo_wbc_cart_ui($index,$cart)
    {  
        
        $first=wbc()->wc->eo_wbc_get_product($cart['FIRST'][0]);

        $second=$cart['SECOND']?wbc()->wc->eo_wbc_get_product($cart['SECOND'][0]):FALSE;

        if(empty($first) or (!empty($cart['SECOND']) and empty(wbc()->wc->eo_wbc_get_product($cart['SECOND'][0])))) return false;
        
        wbc()->load->template('publics/cart', array("cart"=>$cart,"first"=>$first,"second"=>$second,"index"=>$index)); 
    }    

}