<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template_loader = new Listeo_Core_Template_Loader;

$listing_cat_url="#";

get_header(get_option('header_bar_style','standard') );

$gallery_style = get_post_meta( $post->ID, '_gallery_style', true );

if(empty($gallery_style)) { $gallery_style = get_option('listeo_gallery_type','top'); }

$count_gallery = listeo_count_gallery_items($post->ID);

if($count_gallery < 4 ){
	$gallery_style = 'content';	
}
if( get_post_meta( $post->ID, '_gallery_style', true ) == 'top' && $count_gallery == 1 ) {
	//$gallery_style = 'none';	
	$gallery_style = 'top';		
}

if ( have_posts() ) :
if( $gallery_style == 'top' ) :
	$template_loader->get_template_part( 'single-partials/single-listing','gallery' );  
else: ?>


<!-- Gradient-->
<div class="single-listing-page-titlebar"></div>
<?php endif; ?>
<?php 
	function isMobile() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
?>
<?php 



// 		$d = DateTime::createFromFormat('d-m-Y', $expires);
// 		echo $d->getTimestamp(); 
		?>
<!-- Content
================================================== -->
<style>
	.mobile-listing-review {
		display: none;
	}
	@media (max-width: 991px) {
		.mobile-listing-review {
			display: block;
			padding: 0 15px;
		}
		.desktop-listing-review {
			display: none;
		}
	}
</style>
<div class="container">
	<div class="row sticky-wrapper">
<?php while ( have_posts() ) : the_post();  ?>
		<div class="col-md-8 padding-right-30">
			
			<!-- Titlebar -->
			<div id="titlebar" class="listing-titlebar">
				<div class="listing-titlebar-title">
					<h2><?php the_title(); ?>
					<?php
					$terms = get_the_terms( get_the_ID(), 'listing_category' );
					if ( $terms && ! is_wp_error( $terms ) ) : 
					    $categories = array();
					    foreach ( $terms as $term ) {
					        
					        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                    			esc_url( get_term_link( $term->slug, 'listing_category' ) ),
                    			esc_html( $term->name )
                			);

                			$listing_cat_url = $term->slug;
					    }

					    $categories_list = join( ", ", $categories );
					    ?>
					    <span class="listing-tag">
					        <?php  echo ( $categories_list ) ?>
					    </span>
					<?php endif; ?>
					<?php $listing_type = get_post_meta( get_the_ID(), '_listing_type', true);
					switch ($listing_type) {
					 	case 'service':
					 		$type_terms = get_the_terms( get_the_ID(), 'service_category' );
					 		$taxonomy_name = 'service_category';
					 		break;
					 	case 'rental':
					 		$type_terms = get_the_terms( get_the_ID(), 'rental_category' );
					 		$taxonomy_name = 'rental_category';
					 		break;
					 	case 'event':
					 		$type_terms = get_the_terms( get_the_ID(), 'event_category' );
					 		$taxonomy_name = 'event_category';
					 		break;
					 	
					 	default:
					 		# code...
					 		break;
					 } 
					 if( isset($type_terms) ) {
					 	if ( $type_terms && ! is_wp_error( $type_terms ) ) : 
					    $categories = array();
					    foreach ( $type_terms as $term ) {
					        $categories[] = sprintf( '<a href="%1$s">%2$s</a>',
                    			esc_url( get_term_link( $term->slug, $taxonomy_name ) ),
                    			esc_html( $term->name )
                			);
					    }

					    $categories_list = join( ", ", $categories );
					    ?>
					    <span class="listing-tag">
					        <?php  echo ( $categories_list ) ?>
					    </span>
						<?php endif;
					 }
					 ?>
					</h2>
					<?php if(get_the_listing_address()): ?>
						<span>
							<a href="#listing-location" class="listing-address">
								<i class="fa fa-map-marker"></i>
								<?php the_listing_address(); ?>
							</a>
						</span>
					<?php endif; ?>
					
					<?php
						if(!get_option('listeo_disable_reviews')){

					    $place_id = get_post_meta($post->ID,'place_id', true);

                        $greviews  = array();
                        if(!empty($place_id)){
	                     $place_data = listeo_get_google_reviews($place_id,$post);
	                    if(empty($place_data['result']['reviews'])){
		                 $greviews  = array();
	                   } else {
		                 $greviews = $place_data['result']['reviews'];	
	                   }
                     }

	                  if(isset($greviews) && !empty($greviews) && count($greviews) > 0){
                       
                       $google_reviews_count = $place_data['result']['user_ratings_total'];
		            }

							 $rating = get_post_meta($post->ID, 'listeo-avg-rating', true); 
								if(isset($rating) && $rating > 0 ) : 
									$rating_type = get_option('listeo_rating_type','star');
									if($rating_type == 'numerical') { ?>
										<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
									<?php } else { ?>
										<div class="star-rating" data-rating="<?php echo $rating; ?>">
									<?php } 
									 $number = listeo_get_reviews_number($post->ID); 
									$totalreviews=$number+$google_reviews_count; ?>
									<div class="rating-counter"><a href="#listing-reviews">(<?php printf( _n( '%s review', '%s reviews', $totalreviews,'listeo_core' ), number_format_i18n( $totalreviews ) );  ?>)</a></div>
								</div>
							<?php endif; 
						}
					?>

					<!-- FB message style -->
					<?php
						$owner_id = get_the_author_meta( 'ID' );
						$owner_data = get_userdata( $owner_id );
						$direct_fb_style_referral = "listing_".get_the_ID(); 
						if( get_post_meta($post->ID,'_verified',true ) == 'on') {
							$fb_style_input_val = "Hi ".$owner_data->first_name.", is this available?";
							$fb_style_send_btn = '<a href="#" id="direct_fb_style_msg_btn"> Send </a>';
						}
						else{
							$fb_style_input_val = "Hi, is this available?";
							$fb_style_send_btn = '<a href="#" id="listeo_fb_style_unverify_msg"> Send </a>';
						}
					?>

					<div class="listeo_direct_fb_style_msg">
						<h4> <?php esc_html_e('Send seller a message','listeo_core'); ?> </h4>	
						<input value="<?php echo $owner_id; ?>" type="hidden" id="direct_fb_style_recipient">
						<input value="<?php echo $direct_fb_style_referral; ?>" type="hidden" id="direct_fb_style_referral">
						<input value="<?php echo get_the_ID(); ?>" type="hidden" id="direct_fb_style_listing_id">
						<div class="listeo_direct_fb_style_msg_input">
							<input placeholder="Enter your message" value="<?php echo $fb_style_input_val; ?>" type="text" id="direct_fb_style_msg">
						</div>
						<div class="listeo_direct_fb_style_msg_btn">
							<?php
								if(is_user_logged_in()){
									echo $fb_style_send_btn;
								}
								else{
									?>
										<a href="#sign-in-dialog" class="popup-with-zoom-anim book-now-notloggedin sign_in_link" id="direct_fb_style_msg_btn"> <?php esc_html_e('Send','listeo_core'); ?> </a>
									<?php	
								}
							?>
						</div>
						<p id="listeo_direct_fb_style_msg_success"></p>
					</div>
				</div>
			</div>
			<!-- Content
			================================================== -->
			<?php 
			if($gallery_style == 'none' ) :
				$gallery = get_post_meta( $post->ID, '_gallery', true );
				if(!empty($gallery)) : 

					foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
						$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
						echo '<img src="'.esc_url($image[0]).'" class="single-gallery margin-bottom-40" style="margin-top:-30px;"></a>';
					}
					
				 endif;
			 endif; ?>

			<!-- Listing Nav -->
			<div id="listing-nav" class="listing-nav-container">
				<ul class="listing-nav">
					<li><a href="#listing-overview" class="active"><?php esc_html_e('Overview','listeo_core'); ?></a></li>
					<?php if($count_gallery > 0 && $gallery_style == 'content') : ?><li><a href="#listing-gallery"><?php esc_html_e('Gallery','listeo_core'); ?></a></li>
					<?php endif; 
					$_menu = get_post_meta( get_the_ID(), '_menu_status', 1 ); 

					if(!empty($_menu)) {
						$_bookable_show_menu =  get_post_meta(get_the_ID(), '_hide_pricing_if_bookable',true);
						if(!$_bookable_show_menu){ ?>
							<li><a href="#listing-pricing-list"><?php esc_html_e('Pricing','listeo_core'); ?></a></li>
						<?php } ?>
						
					<?php } 

					$video = get_post_meta( $post->ID, '_video', true ); 
					if(!empty($video)) :  ?>
						<li><a href="#listing-video"><?php esc_html_e('Video','listeo_core'); ?></a></li>
					<?php endif;
					$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
					if(!empty($latitude)) :  ?>
					<li><a href="#listing-location"><?php esc_html_e('Location','listeo_core'); ?></a></li>
					<?php 
					endif;
                    // echo 'tetxt asd here';
					if(get_post_meta( $post->ID, '_cancellation_policy', true) != '') {
					?>
						<li><a href="#listing-cancellation_policy"><?php esc_html_e('Cancellation policy','listeo_core'); ?></a></li>	
					<?php
                    
					}
                    
					if(!get_option('listeo_disable_reviews')){
						$reviews = get_comments(array(
						    'post_id' => $post->ID,
						    'status' => 'approve' //Change this to the type of comments to be displayed
						)); 
						if ( $reviews ) : ?>
						<li><a href="#listing-reviews"><?php esc_html_e('Reviews','listeo_core'); ?></a></li>
						<?php endif; ?>
						<li><a href="#add-review"><?php esc_html_e('Add Review','listeo_core'); ?></a></li>
					<?php } ?>
				</ul>
			</div>

			<!-- Overview -->
			<div id="listing-overview" class="listing-section">
				<?php $template_loader->get_template_part( 'single-partials/single-listing','main-details' );  ?>
				
				<!-- Description -->
				
				<?php the_content(); ?>
				<?php $template_loader->get_template_part( 'single-partials/single-listing','socials' );  ?>
				<?php $template_loader->get_template_part( 'single-partials/single-listing','features' );  ?>
			</div>

			<?php
			
			if( $count_gallery > 0 && $gallery_style == 'content') : $template_loader->get_template_part( 'single-partials/single-listing','gallery-content' ); endif; ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','pricing' );  ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','opening' );  ?>
			<?php $template_loader->get_template_part( 'single-partials/single-listing','video' );  ?>
			<?php 
				if(!isMobile()) {
			?>
				<div class="desktop-listing-review">
					<?php $template_loader->get_template_part( 'single-partials/single-listing','location' );  ?>
				</div>
			<?php } ?>
			<?php
				if(get_post_meta( $post->ID, '_cancellation_policy', true) != '') {
					$template_loader->get_template_part( 'single-partials/single-listing','cancellation_policy' );  
				}
			?>
			<div class="desktop-listing-review">
				<?php if(!get_option('listeo_disable_reviews')){ 
					$template_loader->get_template_part( 'single-partials/single-listing','reviews' ); } ?>
			</div>
		</div>
		<?php endwhile; // End of the loop. ?>
		<!-- Sidebar
		================================================== -->
		<div class="col-md-4 margin-top-75 sticky">

				<?php if( get_post_meta($post->ID,'_verified',true ) == 'on') : ?>
					<!-- Verified Badge -->
					<div class="verified-badge with-tip" data-tip-content="<?php esc_html_e('Listing has been verified and belongs to the business owner or manager.','listeo_core'); ?>">
						<i class="sl sl-icon-check"></i> <?php esc_html_e('Verified Vendor','listeo_core') ?>
					</div>
				<?php else:
					if(get_option('listeo_claim_page_button')){
					$claim_page = get_option('listeo_claim_page');?>
					<!-- <div class="claim-badge with-tip" data-tip-content="<?php esc_html_e('Click to claim this listing.','listeo_core'); ?>">
						<a href="<?php echo get_permalink($claim_page); ?>"><i class="sl sl-icon-question"></i> <?php esc_html_e('Not verified. Claim this listing!','listeo_core') ?></a>
					</div> -->
					<?php }

					endif; ?>

				<?php if( get_post_meta($post->ID,'_verified',true ) == 'on') {
					get_sidebar('listing');
				}
				else{ 
					?>
					<div id="unverifiedcontact"><h3>Message Vendor</h3>
<?php echo do_shortcode('[fluentform id="17"]'); ?></div>
					<div id="un_verified_listing_widget" class="listing-widget widget listeo_core widget_listing_owner boxed-widget my_widget1">	
					<div>
								<h4><b>Contact Vendor:</b></h4>
								<?php 
									if( is_user_logged_in() ) { 
										?>
											<div id="small-dialog" class="zoom-anim-dialog mfp-hide">
												<div class="small-dialog-header">
													<h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
												</div>
												<div id="unverify_listing_msg_form" class="message-reply margin-top-0">
													<form>
													
														<textarea 
														required
														cols="40" id="contact-message" class="custommessage" name="message" rows="3" placeholder="<?php esc_attr_e('Your message','listeo_core'); ?>"></textarea>
														<button data-listing_id="<?php echo esc_attr(get_the_ID()); ?>" id="send_unverify_listing_msg_btn" class="button">
														<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>	
														<div style="display: none;" class="notification closeable success margin-top-20"></div>
										 			
										 			</form>
								  
												</div>
											</div>
											<div style="margin-top:5px;" class="new-list-btn unverify_listing_btn">
												<a id="unverify_listing_msg_btn" href="#small-dialog" class="send-message-to-owner button popup-with-zoom-anim listeo_list_provider_meg_btn">
													<?php esc_html_e('Contact', 'listeo'); ?>
												</a>
											</div>
										<?php
									}
									else{
										?>
										<div style="margin-top:5px;" class="new-list-btn unverify_listing_btn">
											<a id="unverify_listing_msg_btn" href="#sign-in-dialog" class="send-message-to-owner button popup-with-zoom-anim listeo_list_provider_meg_btn" style="display:inline-block;">
												Contact				</a>
										</div>
										<?php
									}	
								?>
								<p>To protect your payment, never exchange money or communicate outside of the Hypley website.</p>
								<!-- <p> If this is your business <a href="<?php echo site_url(); ?>/claim-listing/">click here</a> to verify today.</p> -->
								<?php
									if(get_option('listeo_claim_page_button')){

										$claim_page = get_option('listeo_claim_page');?>
										<div style="margin-top:80px;" class="claim_link"><p> Is this your Business ? <a href="<?php echo get_permalink($claim_page); ?>"><?php esc_html_e('Claim this listing!','listeo_core') ?></a> </p></div>

										<?php
									}
								?>
						</div>
					</div>

					<!-- User profile status start -->

					<div class="boxed-widget margin-top-30 margin-bottom-50 verification-section bad-sec" id="unverified-status-listing">
					<?php					
					global $wpdb;

					$udata = get_userdata($owner_id);
					$registered = $udata->user_registered;
					?>
					
					<p class="mem-bdg">Joined on <?php echo date( 'F d Y', strtotime($registered));?></p>	
						<?php								
							if (  $udata->user_status == 1  ) 
								echo '<p class="em-ic">Email Verified</p>';	
												
							$total_visitor_reviews_args = array(
									'post_author' 	=> $udata->ID,
									'parent'      	=> 0,
									'status' 	  	=> 'approve',
									'post_type'   	=> 'listing',
									'orderby' 		=> 'post_date' ,
		            				'order' 		=> 'DESC',
								);

								$total_visitor_reviews = get_comments( $total_visitor_reviews_args ); 
								$review_total = 0;
								$review_count = 0;
								foreach($total_visitor_reviews as $review) {
									if( get_comment_meta( $review->comment_ID, 'listeo-rating', true ) ) {
									 $review_total = $review_total + (int) get_comment_meta( $review->comment_ID, 'listeo-rating', true );
									 $review_count++;
									}
								}

					            $twenty=20;
		                        if($review_count > $twenty){
								echo '<div id="high_rate_div"><p class="high_rate"><i class="fa fa-star" aria-hidden="true"></i>Highly Rated</p></div>';
		                        }

		                        $selectCon= $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}listeo_core_conversations WHERE user_2 = {$owner_id}");

	       $alltime = [];
	       foreach($selectCon as $con){
		  $time = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeo_core_messages WHERE conversation_id = {$con->id} AND sender_id = {$owner_id} LIMIT 1");
		   if(!empty($time)){
			$alltime[]= $time;
		   }

	      }
	      if((int) count($alltime) > 0 && (int) count($selectCon) > 0){
		    $rate =  15;//(int) count($alltime)/ (int) count($selectCon) * 100;
	      }else{
		    $rate = null;
	       }

	
	if((80 <= $rate) && ($rate <= 100)){
		echo '<div id="very_responsive_section">
            <p class="very_response"><i class="fa fa-smile-o" aria-hidden="true"></i>Very Responsive</p></div>';
	}
							 ?>
							
												
					</div>

					<!-- User profile status end -->


					<div>
						<?php if ( is_active_sidebar( 'single_unveryfie_siderbar' ) ) : ?>
								<?php dynamic_sidebar( 'single_unveryfie_siderbar' ); ?>
						<?php endif; ?>
					</div>
					<?php
				} ?>

		</div>
		<!-- Sidebar / End -->
		

		<?php 
			if(isMobile()) {
		?>
			<div class="mobile-listing-review">
				<?php $template_loader->get_template_part( 'single-partials/single-listing','location' );  ?>
			</div>
		<?php } ?>
		<div class="mobile-listing-review">
			<?php if(!get_option('listeo_disable_reviews')){ 
				$template_loader->get_template_part( 'single-partials/single-listing','reviews' ); } ?>
		</div>

		<!-- Google Reviews start -->
<?php

  $place_id = get_post_meta($post->ID,'place_id', true);

$greviews  = array();
if(!empty($place_id)){
	$place_data = listeo_get_google_reviews($place_id,$post);
	if(empty($place_data['result']['reviews'])){
		$greviews  = array();
	} else {
		$greviews = $place_data['result']['reviews'];	
	}
}

?>

    <?php
	if(isset($greviews) && !empty($greviews) && count($greviews) > 0){?>

<div id="listing-google-reviews" class="listing-section">
	<h3 class="listing-desc-headline margin-top-75 margin-bottom-20"><?php esc_html_e('Google Reviews','listeo_core'); ?></h3>
	
	<div class="google-reviews-summary">
	    <div class="google-reviews-summary-logo"></div>
	    <div class="google-reviews-summary-avg">
	        <strong><?php echo number_format_i18n($place_data['result']['rating'],1); ?></strong>
	        <div class="star-rating" data-rating="<?php echo $place_data['result']['rating']; ?>"></div>
	        <span>
<?php
		$google_reviews_count = $place_data['result']['user_ratings_total'];
		printf( // WPCS: XSS OK.
			esc_html( _nx(  'Review %1$s','%1$s reviews', $google_reviews_count, 'comments title', 'listeo_core' ) ),
			 number_format_i18n(  $google_reviews_count )
		);
	?>
	        </span>
	    </div>
	    <?php /*
	    <div class="google-reviews-read-more">
	        <a href="https://search.google.com/local/writereview?placeid=<?php echo $place_id; ?>" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/themes/listeo-child/img/google-reviews-button-icon.svg" alt=""><?php esc_html_e('Add Review','listeo_core'); ?></a>
	    </div>
	    */?>
	</div>
	
    <section class="comments listing-reviews">
    	<ul class="comment-list">
    		<?php  foreach ($greviews as $key => $review) { ?>
    			<li>
    
                   	<div class="avatar"><img src="<?php echo esc_attr($review['profile_photo_url']); ?>" alt="<?php echo $review['author_name'];  ?>"></div>
            		<div class="comment-content"><div class="arrow-comment"></div>
            		
            			<div class="comment-by">
            				
            				<h5><a href="javascript:void(0);"> <?php echo $review['author_name'];  ?></a></h5> 
            		        <span class="date"><?php echo esc_attr($review['relative_time_description']); ?></span>
    			        	<div class="star-rating" data-rating="<?php echo esc_attr($review['rating']); ?>"></div>
            			</div>
            			<p>	<?php echo $review['text']; ?></p>
                    </div>
    
    			</li>
     		<?php } ?>
    	</ul>
    
      
    </section>
	  <?php /*  <div class="google-reviews-read-more bottom">
	        <a href="https://search.google.com/local/reviews?placeid=<?php echo $place_id ?>" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/themes/listeo-child/img/google-reviews-logo.svg" alt=""><?php esc_html_e('Read More Reviews','listeo_core'); ?></a>
	    </div> */?>
	  </div>
<?php } ?>


<!-- Google Reviews end -->
		
		<div class="listeo-related-listing">
			<?php $template_loader->get_template_part( 'single-partials/single-listing','related' ); ?>
		</div>
	</div>


	


</div>



<?php else : ?>

<?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>


<!-- Google snippet / start -->

<?php
global $post;
$rating = get_post_meta($post->ID, 'listeo-avg-rating', true); 
$roundrating= round($rating,1); 
$number = listeo_get_reviews_number($post->ID);
$posttitle=get_the_title($post->ID);
$posturl=get_permalink($post->ID);
$gallery = get_post_meta( $post->ID, '_gallery', true );

            foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                $image[] = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
              foreach ($image[0] as $key) {
                $img[]=$key;
              }

        }

  $id = sanitize_text_field(trim($post->ID));
    $current_user = wp_get_current_user();

       $total_visitor_reviews = get_comments(
        array(
          'orderby'   => 'post_date',
                'order'   => 'DESC',
                'status'  => 'approve',
                'post_author' => $owner_id,
          'parent'    => 0,
          'post_id'    => $id,
          'post_type' => 'listing',
              )
      );
    
    $visitor_reviews_args = array(

      'post_author'   => $owner_id,
      'parent'        => 0,
      'status'    => 'approve',
      'post_type'   => 'listing',
      'post_id'     => $id,
   
    );
    $visitor_reviews_pages = ceil(count($total_visitor_reviews)/$limit);
    
    $visitor_reviews = get_comments( $visitor_reviews_args ); 

$reviewsarr=array();
      foreach($visitor_reviews as $review) {
             $authorname=$review->comment_author;
             $datecomment=$review->comment_date;
             $publisheddate='Last edited'.' '.date('M d,Y',strtotime($datecomment));
             $reviewcontent=$review->comment_content;

    $star_rating = get_comment_meta( $review->comment_ID, 'listeo-rating', true );  
    $reviewRating=array('@type'=>"Rating","bestRating"=> "5",  "worstRating"=> "0","ratingValue"=> $roundrating);

$reviewsarr[]=array('@type'=>'Review','author'=>$authorname,'datePublished'=>$publisheddate,"reviewBody"=>$reviewcontent,"reviewRating"=>$reviewRating);
      
}

$reviews_snippet= json_encode($reviewsarr);
?>
<script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "LocalBusiness",
    "name": "<?php echo $posttitle;?>",
    "url": "<?php echo $posturl;?>",
    "image": "<?php echo $img[0];?>",
      "aggregateRating": {
        "@type": "AggregateRating",
        "bestRating": "5",
        "worstRating": "0",
        "ratingValue": "<?php echo $roundrating;?>",
        "ratingCount": "<?php echo $number;?>"
      },
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "315 Brunswick Street",
      "addressLocality": "brisbane",
      "addressRegion": "QLD"
    },
    "reviews": 
       
        <?php echo $reviews_snippet;?>
    
  }
</script>
<!-- Google snippet / end -->
<?php
echo do_shortcode( '[elementor-template id="29949"]' );?>
<?php get_footer(); ?>

<script>
	jQuery('#send_unverify_listing_msg_btn').on('click',function(e) {
        
        jQuery('#send_unverify_listing_msg_btn').addClass('loading').prop('disabled', true);

        var message = jQuery("#unverify_listing_msg_form").find("#contact-message").val();
        var listing_id = jQuery(this).data("listing_id");
        var ajax_file_url = '<?php echo admin_url('admin-ajax.php'); ?>';

        jQuery.ajax({
	        type: 'POST',
	        dataType: 'json',
	        url: ajax_file_url,
	        data: {"action": "send_unverify_listing_msg", message: message, listing_id: listing_id },
	        success: function(data){
				jQuery('#unverify_listing_msg_form .custommessage').val('');
	        	if(data.success == 1)
	        	{
	        		jQuery('#send_unverify_listing_msg_btn').removeClass('loading');
                    jQuery('#unverify_listing_msg_form .notification').removeClass('error').addClass('success').show().html('Your message is successfully sent.');
					
	        	}
	        	else {
	        		jQuery('#unverify_listing_msg_form .notification').removeClass('success').addClass('error').show().html("Your message couldn't send, please try again.");
                    jQuery('#send_unverify_listing_msg_btn').removeClass('loading').prop('disabled', false);

	        	}
	        }
	    });

        e.preventDefault();
       });
</script>
