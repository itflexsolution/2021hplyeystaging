<?php

$listing_cats = get_the_terms( $post->ID, 'listing_category' );
$listing_region = get_the_terms( $post->ID, 'region' );

$tax_query = array();

if( !empty($listing_cats) && !empty($listing_region) ){
    $tax_query = array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'listing_category',
            'field' => 'term_id',
            'terms' => $listing_cats[0]->term_id,
        ),
        'relation' => 'AND',
        array(
            'taxonomy' => 'region',
            'field' => 'term_id',
            'terms' => $listing_region[0]->term_id,
        ),
    );
}
elseif (!empty($listing_cats)) {
    $tax_query = array(
        array(
            'taxonomy' => 'listing_category',
            'field' => 'term_id',
            'terms' => $listing_cats[0]->term_id,
        ),
    );
}
elseif (!empty($listing_region)){
    $tax_query = array(
        array(
            'taxonomy' => 'listing_category',
            'field' => 'term_id',
            'terms' => $listing_cats[0]->term_id,
        ),
    );
} 

$related_args = array(
    'post_type' => 'listing',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'post__not_in' => array( $post->ID ),
    'orderby' => 'rand',
    'tax_query' => $tax_query,
    'meta_query' => array(
        array(
            'key'       => '_verified',
            'value'     => 'on',
            'compare'   => '=',
        )
    ),
);

$related_query = new wp_query($related_args);
    
if($related_query->have_posts()) {
    ?> 
    <div class="listeo_custom_sec_seprator related_listing_sep"><hr></div>
    <h3 style="margin-left: 5px;"> You may also like </h3> <div class="listings-container grid-layout row"> <div data-grid_columns="3" data-style="grid" id="listeo-listings-container"> <?php
    while ( $related_query->have_posts() ) { $related_query->the_post(); ?>
        <div class="col-lg-4 col-md-6">
            <div class="listing-item-container listing-geo-data listo-main-box-sec">
                <div data-link="<?php the_permalink(); ?>" class="listing-item listeo_grid_view_item listo-list-iteam " style="height: 380.972px;">
                    <a target="_blank" href="<?php the_permalink(); ?>">
                        
                        <?php $gallery = get_post_meta( $post->ID, '_gallery', true ); 
                        foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                            $image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
                            break;
                        }
                        ?>
                        <img class="listeo_liting_single_galary_image listeo_liting_single_image" src="<?php echo $image[0]; ?>">
                    </a>
                    
                    <div class="listing-item-content listo-new-listing-iteam <?php echo "post - ".$post->ID; ?>">
                        
                        <?php
                        if(!get_option('listeo_disable_reviews'))
                        {
                            $rating = get_post_meta($post->ID, 'listeo-avg-rating', true);
                            if(isset($rating) && $rating > 0 ) : $rating_type = get_option('listeo_rating_type','star');
                                if($rating_type == 'numerical')
                                { 
                                    ?>
                                    <div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
                                    <?php
                                }
                                else
                                {
                                    $number = listeo_get_reviews_number($post->ID); ?>
                                    <div class="star-rating listo-new-star-rating" data-rating="1">
                                    <h6> <?php echo number_format( $rating,1 ); ?></h6>
                                        <div class="rating-counter">(&nbsp;<?php echo $number ?>&nbsp;)</div>
                                <?php } ?>
                            </div>
                            <?php else: ?>
                            <div class="star-rating listo-new-star-rating" >
                                
                            </div>
                            <?php endif;
                        } ?>
                        
                        <h3 class="listeo_single_list_title listo-hed-h3-new <?php echo (isset($rating) && $rating > 0)?"":"full_width";?>">
                            <a target="_blank" href="<?php the_permalink(); ?>">
                            <?php the_title(); ?> <?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?><i class="verified-icon"></i><?php endif; ?>
                            </a>
                        </h3>
                        
                        <p class="single_listing_description">
                            <a target="_blank" href="<?php the_permalink(); ?>" style="color: #707070;">
                                <?php
                                    $content = get_the_content();
                                    $content = strip_tags($content);
                                    if(strlen($content)>165) echo $content = substr($content,0,165)."...";
                                    else echo $content;
                                ?>
                            </a>
                        </p>

                    </div>

                    <!-- Start Bookmark -->
                     <?php
                        if( listeo_core_check_if_bookmarked($post->ID) ) {
                           $nonce = wp_create_nonce("listeo_core_bookmark_this_nonce"); ?>
                           <span class="like-icon listeo_core-unbookmark-it liked listo-bookmark-icon-new" data-post_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" ></span>
                        <?php } else {
                        if(is_user_logged_in()){
                           $nonce = wp_create_nonce("listeo_core_remove_fav_nonce"); ?>
                           <span class="save listeo_core-bookmark-it like-icon listo-bookmark-icon-new" data-post_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" ></span>
                        <?php } else { ?>
                           <span class="save like-icon tooltip left listo-bookmark-icon-new"  title="<?php esc_html_e('Login To Bookmark Items','listeo_core'); ?>"  ></span>
                        <?php } ?>
                     <?php } ?>
                     <!-- End Bookmark -->

                     <?php $min_price = (get_the_listing_price_range() && (strpos(get_the_listing_price_range(), 'Starts from') !== false))?substr(get_the_listing_price_range(),11):"$ 0"; ?>
                    
                    <div class="listing-small-badge pricing-badge listo-new-badge">
                        <a style="color: #000;" target="_blank" href="<?php the_permalink(); ?>">
                        <?php
                           echo __('From ', 'listeo_core').' '.$min_price;
                           ?>
                          </a>
                    </div>

                </div>
            </div>
        </div>
    <?php } 
    ?> </div> </div> <?php
}

$post = $backup;
wp_reset_query();

?>