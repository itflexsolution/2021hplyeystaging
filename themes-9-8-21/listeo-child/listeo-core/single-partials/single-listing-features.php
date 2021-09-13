<?php
	
	$terms = get_the_terms( $post->ID, 'listing_category' ); 

	if( $terms ){
		$selected = false;
		$selected_check = wp_get_object_terms( $post->ID, 'listing_feature', array( 'fields' => 'ids' ) ) ;
		if ( ! empty( $selected_check ) ) {
			if ( ! is_wp_error( $selected_check ) ) {
				$selected = $selected_check;
			}
		}

		$categories = array();
		foreach( $terms as $term ){
			$categories[] = $term->term_id;
		}

		foreach ($categories as $category) {
			$cat_object = get_term_by('id', $category, 'listing_category');
			if($cat_object){
				$features = array();
				$features_temp = get_term_meta($cat_object->term_id,'listeo_taxonomy_multicheck',true);
				if($features_temp) {
					$features += $features_temp;
				}
			}
			$features = array_unique($features);
		}
	}

	if($features){
		?> <div class="listeo_custom_sec_seprator"><hr></div> <h3 class="listing-desc-headline"><?php esc_html_e('Features','listeo_core'); ?></h3> <?php
		echo '<ul class="listing-features checkboxes margin-top-0">';
		$is_listing_feature_checked = "listing_feature_not_checked";
		foreach ($features as $feature) {
			$feature_obj = get_term_by('slug', $feature, 'listing_feature');
			if( !$feature_obj ){
				continue;
			}
			if($selected){
				if( in_array(  $feature_obj->term_id, $selected ) ){
					$is_listing_feature_checked = "listing_feature_checked";
				}
				else{
					$is_listing_feature_checked = "listing_feature_not_checked";
				}
			}
			echo '<li class="'.$is_listing_feature_checked.'">'. $feature_obj->name .'</li>';
		}
		echo '</ul>';
	}
?>