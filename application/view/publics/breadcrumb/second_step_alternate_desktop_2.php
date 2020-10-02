<?php

/*
*	Template to show breadcrumb second step for desktop
*/

?>
<div class="step <?php echo (($step==$order)?'active ':(($step>$order)?'completed ':(!empty(\eo\wbc\model\publics\component\EOWBC_Breadcrumb::$clickable_breadcrumb)?'':'disabled'))); ?>" style="" >            
    <div class="ui equal width middle aligned grid" style="width: 100%;padding-top: 0px;text-transform:none;font-family: 'ZapfHumanist601BT-Roman';">        

        <div class="ui column left aligned"><?php echo $order; ?></div>
        <div class="ui column left aligned">
            <?php if(empty($second)){ ?>
                <div class="title" <?php _e((!empty(\eo\wbc\model\publics\component\EOWBC_Breadcrumb::$clickable_breadcrumb) and !empty(\eo\wbc\model\publics\component\EOWBC_Breadcrumb::$second_url))?'data-clickable_breadcrumb="'.\eo\wbc\model\publics\component\EOWBC_Breadcrumb::$second_url.'"':''); ?>><?php _e(wbc()->options->get_option('appearance_breadcrumb','appearance_breadcrumb_choose_prefix_text',__('Choose a','woo-bundle-choice'),true,true)); ?> <?php _e($second_name); ?></div>
            <?php } else { ?>
                <!-- /* Language function - comment */  -->
                <div class="title"><?php _e($second_name,'woo-bundle-choice'); ?></div>
                <div class="description"><?php _e($second->get_name(),'woo-bundle-choice'); ?> - <?php _e(wc_price($second->get_price(),'woo-bundle-choice')); ?></div>
                <div class="ui small blue text">                
                    <u><a href="<?php echo !empty(wbc()->sanitize->get('SECOND')) ? eo\wbc\model\publics\component\EOWBC_Breadcrumb::eo_wbc_breadcrumb_view_url(wbc()->sanitize->get('SECOND'),$order):'#'; ?>"><?php _e('View','woo-bundle-choice'); ?></a></u>&nbsp;|&nbsp;<u><a href="<?php echo !empty(wbc()->sanitize->get('SECOND'))?eo\wbc\model\publics\component\EOWBC_Breadcrumb::eo_wbc_breadcrumb_change_url($order,wbc()->sanitize->get('SECOND')):'#'; ?>"><?php _e(wbc()->options->get_option('appearance_breadcrumb','appearance_breadcrumb_change_action_text','Change',true,true)); ?></a></u>
                </div>    
                
            <?php } ?>
        </div>        
        <div class="ui column mini image left aligned" style="padding-top: 0px;padding-bottom: 0px;">
            <?php if(empty($second)){ ?>
                <img src = '<?php echo $second_icon; ?>' class='ui mini image'/>
            <?php } else { ?>

                <img src = '<?php _e(wp_get_attachment_url($second->get_image_id())); ?>' class='ui mini image'/>
            <?php } ?>
        </div>
    </div>        
</div>
