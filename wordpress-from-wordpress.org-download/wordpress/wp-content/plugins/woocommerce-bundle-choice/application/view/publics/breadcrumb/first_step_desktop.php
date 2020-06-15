<?php

/*
*	Template to show breadcrumb first step for desktop
*/

?>
    <div class="step <?php echo (($step==$order)?'active ':(($step>$order)?'completed ':'disabled')); ?>" style="">            
        <div class="ui equal width grid" style="width: 100%;margin-top: -1em !important;">
            <div class="ui grid">
                <div class="column"><?php echo $order; ?></div>
                <div class="column" style="text-align: left;">                        
                    <div class="description">Choose a</div>
                    <div class="title"><?php echo wbc()->options->get_option('configuration','first_name','')/*get_option('eo_wbc_first_name','')*/; ?></div>
                </div>
            </div>
            <?php if(empty($first)):?>
            <div class="column ">&nbsp;</div>
            <div class="column" <?php echo empty(wp_get_attachment_url(wbc()->options->get_option('configuration','first_icon')/*get_option('eo_wbc_first_icon')*/))?'style="visibility: hidden;"':""; ?>>
                <img src="<?php echo wp_get_attachment_url(wbc()->options->get_option('configuration','first_icon')/*get_option('eo_wbc_first_icon')*/); ?>">
            </div>
            <?php else: ?>
            <div class="column  product_image_section" style="padding-top: 0px;padding-bottom: 0px;">
                <?php echo $first->get_image(); ?>
            </div>
            <div class="column " style="font-size: x-small;">
                <?php _e(wc_price($first->get_price())); ?>
                <br/>
                <u><a href="<?php echo $view_url; ?>">View</a></u>&nbsp;|&nbsp;<u><a href="<?php echo $change_url; ?>">Remove</a></u>
            </div>                        
            
        <?php endif; ?>
        </div>            
    </div>