<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package listeo
 */

?>

<!-- Footer
================================================== -->
<?php
$sticky = get_option('listeo_sticky_footer') ;
$style = get_option('listeo_footer_style') ;

if(is_singular()){

	$sticky_singular = get_post_meta($post->ID, 'listeo_sticky_footer', TRUE);

	switch ($sticky_singular) {
		case 'on':
		case 'enable':
			$sticky = true;
			break;

		case 'disable':
			$sticky = false;
			break;

		case 'use_global':
			$sticky = get_option('listeo_sticky_footer');
			break;

		default:
			$sticky = get_option('listeo_sticky_footer');
			break;
	}

	$style_singular = get_post_meta($post->ID, 'listeo_footer_style', TRUE);
	switch ($style_singular) {
		case 'light':
			$style = 'light';
			break;

		case 'dark':
			$style = 'dark';
			break;

		case 'use_global':
			$style = get_option('listeo_footer_style');
			break;

		default:
			$sticky = get_option('listeo_footer_style');
			break;
	}
}

$sticky = apply_filters('listeo_sticky_footer_filter',$sticky);
?>
<div id="footer" class="<?php echo esc_attr($style); echo esc_attr(($sticky == 'on' || $sticky == 1 || $sticky == true) ? " sticky-footer" : ''); ?> ">
	<!-- Main -->
	<div class="container">
		<div class="row">
			<?php
			$footer_layout = get_option( 'pp_footer_widgets','6,3,3' );

	        $footer_layout_array = explode(',', $footer_layout);
	        $x = 0;
	        foreach ($footer_layout_array as $value) {
	            $x++;
	             ?>
	             <div class="col-md-<?php echo esc_attr($value); ?> col-sm-6 col-xs-12">
	                <?php
					if( is_active_sidebar( 'footer'.$x ) ) {
						dynamic_sidebar( 'footer'.$x );
					}
	                ?>
	            </div>
	        <?php } ?>

		</div>
		<!-- Copyright -->
		<div class="row">
			<div class="col-md-12">
				<div class="copyrights"> <?php $copyrights = get_option( 'pp_copyrights' , '&copy; Theme by Purethemes.net. All Rights Reserved.' );

		            echo wp_kses($copyrights,array( 'a' => array('href' => array(),'title' => array()),'br' => array(),'em' => array(),'strong' => array(),));
		         ?></div>
			</div>
		</div>
	</div>
</div>

<!-- Back To Top Button -->
<div id="backtotop"><a href="#"></a></div>

<?php if(is_singular('listing')) :
	$_booking_status = get_post_meta($post->ID, '_booking_status',true);
	$verify = (get_post_meta($post->ID,'_verified', true));
	if($_booking_status) : ?>
		<!-- Booking Sticky Footer -->
		<div class="booking-sticky-footer">
			<div class="container">
				<div class="bsf-left">
					<?php
					$price_min = get_post_meta( $post->ID, '_price_min', true );
					if (is_numeric($price_min)) {
					    $price_min_raw = number_format_i18n($price_min);
					}
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

					if($price_min) : ?>
					<h4><?php esc_html_e('Starting from','listeo') ?> <?php if($currency_postion == 'after') { echo $price_min_raw . $currency_symbol; } else { echo $currency_symbol . $price_min_raw; } ?></h4>
					<?php else : ?>
						<h4><?php esc_html_e('Select dates to see prices','listeo') ?></h4>
					<?php endif; ?>

						<?php
						if(!get_option('listeo_disable_reviews')){
							$rating = get_post_meta($post->ID, 'listeo-avg-rating', true);
							if(isset($rating) && $rating > 0 ) :
								$rating_type = get_option('listeo_rating_type','star');
								if($rating_type == 'numerical') { ?>
									<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
								<?php } else { ?>
									<div class="star-rating" data-rating="<?php echo $rating; ?>">
								<?php } ?>

								</div>
						<?php endif;
						}?>

				</div>
				<div class="bsf-right">
					<a href="<?=($verify) ? '#booking-widget-anchor' : '#un_verified_listing_widget'?>" class="button"><?php if($verify){ esc_html_e('Book Now','listeo'); } else { echo "Message Now";} ?></a>
				</div>
			</div>
		</div>
	<?php endif;
	endif; ?>

</div> <!-- weof wrapper -->
<?php if(( is_page_template('template-home-search.php') || is_page_template('template-home-search-video.php') || is_page_template('template-home-search-splash.php')) && get_option('listeo_home_typed_status','enable') == 'enable') {
	$typed = get_option('listeo_home_typed_text');
	$typed_array = explode(',',$typed);
	?>
						<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>
						<script>
						var typed = new Typed('.typed-words', {
						strings: <?php echo json_encode($typed_array); ?>,
						typeSpeed: 80,
						backSpeed: 80,
						backDelay: 4000,
						startDelay: 1000,
						loop: true,
						showCursor: true
						});
						</script>
					<?php } ?>
<?php wp_footer(); ?></body>
</html>
<script type="text/javascript">
jQuery(function($){
  $('div #media-uploader a').click(function(){
    alert($(this).attr('href'));
  });
});
</script>
<script>
	jQuery(function($){
		 $(".mo-openid-app-icons a").removeAttr("style");
	});

</script>

<div id="listeo_wishlist_popup" class="zoom-anim-dialog mfp-hide listeo-dialog ">
	<div class="small-dialog-header">
		<center><h3 class="listeo_wishlist_header_title"> Your board </h3></center>
	</div>
	<div class="listeo_wishlist_popup_body margin-top-0">
		<div class="listeo_wishlist_popup_right">
			<img class="listeo_wishlist_popup_right_img" width="250" height="300" src="https://52.63.85.48/wp-content/uploads/2021/05/181620074_311797347013359_950217712658517380_n-scaled.jpg">
		</div>
		<div class="listeo_wishlist_popup_left">
			<div class="current_user_wishlist_main">
				<?php
					if(is_user_logged_in()){
						$userID = get_current_user_id();
						$current_user_wishlist = get_user_meta( $userID, 'listeo_user_wishlist',true);

						if( !empty( $current_user_wishlist ) ){
							foreach( $current_user_wishlist as $key => $user_wishlist ){
               					if( !empty($user_wishlist) ){
					               	foreach( $user_wishlist as $list_id ){
								 		$temp_list_get_first_img_url = "";
						       			$temp_list_get_first_img = (array) get_post_meta( $list_id, '_gallery', true );
					      				foreach ( (array) $temp_list_get_first_img as $attachment_id => $attachment_url ) {
					         				$list_img = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
					            			$temp_list_get_first_img_url = esc_attr($list_img[0]);
					            			break;
					      				}
					            		break;
								 	} ?>
									<div class="current_user_wishlist">
										<a href="javascript:;">
											<img width="50" height="50" src="<?php echo $temp_list_get_first_img_url; ?>">
											<span class="current_user_wishlist_name"> <?php echo $key; ?> </span>
											<span data-bookmark_name="<?php echo $key; ?>" class="save_listing_wishlist_name_btn" style=""> save </span>
										</a>
									</div>
									<?php
								}
							}
						}
					}
				?>
			</div>
			<div class="create_new_wishlist_btn_main">
				<button class="button listeo_create_new_wishlist_btn">
					<i class="fa fa-plus" aria-hidden="true"></i> Create board
				</button>
			</div>
			<div style="display:none;" class="listeo_save_new_wishlist_sec">
				<input placeholder="Name" type="text" name="listeo_new_wishlist_name" class="listeo_new_wishlist_name" style="margin-bottom:0px;">
				<p class="listeo_new_wishlist_name_err" style="display:none;color: red;">Please enter name</p>
				<button style="width:100%;margin-top: 30px;" class="button listeo_save_new_wishlist_btn">Create</button>
			</div>
		</div>
	</div>
	<button title="Close (Esc)" type="button" class="mfp-close mfp_close_wishlist_defualt"></button>
	<button style="display:none;" title="Close (Esc)" type="button" class="mfp_close_wishlist listeo_close_new_wishlist_sec"></button>
</div>