<?php

/*
*	Template to show icon filters for desktop
*/

?>
<div class="<?php echo $width_class; ?>">
	<div style="display: inline-block;"class="ui three wide field">
		<spna class="ui header"><?php echo($title); ?></spna>
		<span><?php if($help): ?>
		&nbsp; <span class="ui grey text" style="cursor: pointer;">&nbsp;<i class="question circle outline icon" data-help="<?php _e($help); ?>"></i></span>
		<?php endif; ?>
		</span>
	</div>	
	<div style="display: inline-block;" class="field twelve ui wide">
		<div class="ui equal width center aligned grid" style="text-align: center;display: inline-block;width: 100% !important;">				
			<?php foreach ($list as $filter_icon): ?>
				<div style="padding: 0px;" title="<?php $filter_icon["name"]; ?>"
					class="eo_wbc_filter_icon column <?php echo $non_edit ? 'none_editable':'' ?> 
						<?php echo $filter_icon['mark'] ? 'eo_wbc_filter_icon_select':''?> ui image" 
					data-slug="<?php echo $filter_icon['slug']; ?>" 
					data-filter="<?php echo $term->slug; ?>" style="border-bottom: 2px solid transparent;padding-top: 0rem;padding-bottom: 0rem;"
					data-type="<?php echo $type; ?>">
					<div>
						<img src='<?php echo $filter_icon['icon']; ?>' class="ui mini image" style="width:35px !important"/>
					</div>
					<?php if($input=='icon_text'): ?>
						<div style="visibility: hidden;"><?php echo($filter_icon['name']); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>			  	
		</div>	
	</div>	
	
</div>