<?php

/*
*	Template to show breadcrumb second step for desktop
*/

?>
<div class="step <?php echo (($step==$order)?'active ':(($step>$order)?'completed ':'disabled')); ?>" style="">            
    <div class="ui equal width middle aligned grid" style="width: 100%;padding-top: 0px;text-transform:none;font-family: 'ZapfHumanist601BT-Roman';">        

        <div class="ui column center aligned"><?php echo $order; ?></div>
        <div class="ui column">
            <?php if(empty($second)){ ?>
                <div class="title">Choose a <?php _e($second_name); ?></div>
            <?php } else { ?>
                <div class="title"><?php _e($second_name); ?></div>
                <div class="description"><?php _e($second->get_name()); ?> - <?php _e(wc_price($second->get_price())); ?></div>
                <div class="ui small blue text">                
                    <u><a href="<?php echo !empty($_GET['SECOND']) ? eo\wbc\model\publics\component\EOWBC_Breadcrumb::eo_wbc_breadcrumb_view_url(sanitize_text_field($_GET['SECOND']),$order):'#'; ?>">View</a></u>&nbsp;|&nbsp;<u><a href="<?php echo !empty($_GET['SECOND'])?eo\wbc\model\publics\component\EOWBC_Breadcrumb::eo_wbc_breadcrumb_change_url($order,sanitize_text_field($_GET['SECOND'])):'#'; ?>">Remove</a></u>
                </div>    
                
            <?php } ?>
        </div>        
        <div class="ui column mini image" style="padding-top: 0px;padding-bottom: 0px;">
            <?php if(empty($second)){ ?>
                <img src = '<?php echo $second_icon; ?>' class='ui mini image'/>
            <?php } else { ?>

                <img src = '<?php _e(wp_get_attachment_url($second->get_image_id())); ?>' class='ui mini image'/>
            <?php } ?>
        </div>
    </div>        
</div>
