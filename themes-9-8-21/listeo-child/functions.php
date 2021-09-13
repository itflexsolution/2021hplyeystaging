<?php
add_action( 'wp_enqueue_scripts', 'listeo_enqueue_styles');
function listeo_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('bootstrap','listeo-icons','listeo-woocommerce') );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/css/cristian_style.css');
    //wp_enqueue_style( 'child-style-2', get_stylesheet_directory_uri() . '/css/sahil_style.css');

}


// add_action( 'wp_enqueue_scripts', 'listeo_cristian_behind_scripts', 9999);
add_action( 'wp_head', 'listeo_cristian_behind_scripts', 9999);
function listeo_cristian_behind_scripts() {
	//dequeue frontend js because send message with widget has error
	//wp_dequeue_script('listeo_core-frontend');
    //wp_deregister_script('listeo_core-frontend');
    //wp_register_script( 'listeo_core-frontend', get_stylesheet_directory_uri() . '/js/frontend.js', array( 'jquery' ));
	//wp_enqueue_script('listeo_core-frontend');

	// wp_dequeue_script('daterangerpicker');
 //    wp_deregister_script('daterangerpicker');
    // wp_register_script( 'daterangerpicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array( 'jquery','moment' ) );
	// wp_enqueue_script('daterangerpicker');

	wp_register_script( 'cristian_script', get_stylesheet_directory_uri() . '/js/cristian_script.js', array( 'jquery' ));
	//wp_register_script( 'counterup', get_stylesheet_directory_uri() . '/js/counterup.js', array( 'jquery' ));
	wp_register_script( 'cm_scripts', get_stylesheet_directory_uri() . '/js/cm_scripts.js', array( 'jquery' ), time());

	wp_enqueue_script('cristian_script');
	//wp_enqueue_script('counterup');
	wp_enqueue_script('cm_scripts');
}

function remove_parent_theme_features() {

}
add_action( 'after_setup_theme', 'remove_parent_theme_features', 10 );

function listing_category_slider(){
	$termArray = get_terms( array(
	    'taxonomy' => 'listing_category',
	    'hide_empty' => false,
	) );
	$html  = '<link rel="stylesheet" href="'.site_url() . '/wp-content/themes/listeo-child/css/flexslider.css"/>
				<script type="text/javascript" src="'.site_url(). '/wp-content/themes/listeo-child/js/jquery.flexslider-min.js"></script>
	<div class="flexslider">
  			<ul class="slides">';
	foreach ($termArray as $singleTerm) {
		$metaData = get_term_meta($singleTerm->term_id);
		$coverImageID = $metaData['_cover'][0];
		$coverImage = wp_get_attachment_image_src($coverImageID, array('784','500'));
		if ($coverImage) :
			$html .='<li><img src="'.$coverImage[0].'" /></li>';
		endif;
	}

	$html .='</ul></div>';
	$html .= '<script>
jQuery(window).load(function() {
  jQuery(".flexslider").flexslider({
    animation: "slide",
    controlNav: false
  });
});</script>';
	return $html;
}
add_shortcode( 'listing-category', 'listing_category_slider' );
if (!is_admin()) {
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
              if ( strpos( $tag, "jquery-migrate.min.js" ) || strpos( $tag, "jquery.min.js") ) {
            return $tag;
        }
        return str_replace( ' src', ' defer src', $tag );
    }, 10, 2 );

}

function mz_footer(){

	if(isset($_GET['page_id']) && $_GET['page_id'] == 71){

	?>

	<script>

		jQuery(document).ready(function(){

			setTimeout(function(){

				jQuery('p#_gallery-description').html('Photo are the first thing that guests will see. We recommend adding 10 or more high quality photos.<br>Photo requirments:<br><ul><li>High resolution - Atleast 1,000 pixels wide</li><li>Horizontal orientation - No vertical photos</li><li>Color photos - No block & white</li><li>Mics. - No collages, screenshots, No watermarks</li></ul>');

			},200);

		});

	</script>

<?php

	}

	if(is_page(66)){
	    ?>
	    <script>
	        jQuery(document).ready(function() {
	        	if(jQuery(".message-content").length){
				    jQuery(".message-content").animate({
				        scrollTop: jQuery('.message-content').get(0).scrollHeight
				    }, 2000);
				}
			});
	    </script>
	    <?php
	}
}

add_action('wp_footer','mz_footer');

function whero_limit_image_size($file) {

	// Calculate the image size in KB
	$image_size = $file['size']/1024;

	// File size limit in KB
	$limit = 200;

	// Check if it's an image
	$is_image = strpos($file['type'], 'image');

	if ( ( $image_size > $limit ) && ($is_image !== false) )
        	$file['error'] = 'Your picture is too large. It has to be smaller than '. $limit .'KB';

	return $file;

}
//add_filter('wp_handle_upload_prefilter', 'whero_limit_image_size');


add_action("widgets_init","register_unveryfie_siderbar");
    function register_unveryfie_siderbar()
    {
      register_sidebar(array(
      'name' => 'Single Unveryfie Listing Sidebar',
      'id' => 'single_unveryfie_siderbar',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
       ));
    }
// // keep users logged in for longer in wordpress
// function wcs_users_logged_in_longer( $expirein ) {
//     // 1 month in seconds
//     return 2628000;
// }
// add_filter( 'auth_cookie_expiration', 'wcs_users_logged_in_longer' );



/**
 * Load Core User Class.
 */
//require_once( get_stylesheet_directory(). '/inc/hypley-modify-listeo-core-users.php' );



add_action('fluentform_user_registration_completed', 'send_fluentform_email_after_registration_complete');

function send_fluentform_email_after_registration_complete($user_id){
	$code 				= sha1( $user_id . time() );

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'users',
		array(
			'user_activation_key' => $code,
			//'user_pass' => $hash_password
		),
		array( 'ID' => $user_id ),
		array(
			'%s',   // value1
		),
		array( '%d' )
	);


	update_user_meta($user_id, 'account_activated', 0);
	update_user_meta($user_id, 'activation_code', $code);

	if( function_exists('listeo_core_get_option') && get_option('listeo_submit_display',true) ) {
		 $login_url = get_permalink( listeo_core_get_option( 'listeo_profile_page' ) );
	} else {
		 $login_url = wp_login_url();
	}


	$user = get_user_by( 'id', $user_id );

	$mail_args = array(
		'email'         => $user->user_email,
		'login'         => $user->user_login,
		'password'      => $user->user_pass,
		'first_name' 	=> $user->first_name,
		'last_name' 	=> $user->last_name,
		'display_name' 	=> $user->display_name,
		'login_url' 	=> $login_url,
		'user_id'		=> $user_id,
		'code'			=> $code,
	);

	return hypley_welcome_mail($mail_args);
}


function hypley_welcome_mail($args){
	$email =  $args['email'];
	$code  =  $args['code'];
	$user_id = $args['user_id'];

	$activation_link = get_the_permalink(3438).'?key='.$code.'&user_id='.$user_id;

	$args = array(
		'user_mail'         => $email,
		'login'         => $args['login'],
		'password'      => $args['password'],
		'first_name' 	=> $args['first_name'],
		'last_name' 	=> $args['last_name'],
		'user_name' 	=> $args['display_name'],
		'user_mail' 	=> $email,
		'login_url' 	=> $args['login_url'],

		);

	$subject 	 = get_option('listeo_listing_welcome_email_subject','Welcome to {site_name}!');
	$subject 	 = hypley_replace_shortcode( $args, $subject );

	//$activation_link =  site_url().'/wp-content/webservices/registration-confirmation.php?key='.$code.'&user='.$user_id;

	$body 	 = get_option('listeo_listing_welcome_email_content','Welcome to {site_name}! You can log in {login_url}, your username: "{login}", and password: "{password}".');

	$body 	 = hypley_replace_shortcode( $args, $body );

	hypley_send( $email, $subject, $body, $activation_link  );
}


function hypley_send( $emailto, $subject, $body , $activation_link='', $reply_to=''){

	$from_name 	= get_option('listeo_emails_name',get_bloginfo( 'name' ));
	$from_email = get_option('listeo_emails_from_email', get_bloginfo( 'admin_email' ));
	$headers 	= sprintf( "From: %s <%s>\r\n Content-type: text/html; charset=UTF-8\r\n", $from_name, $from_email );
	if($reply_to != ''){
		$headers .='Reply-To: '.$reply_to.' <cristian@hypley.com>';
	}

	if( empty($emailto) || empty( $subject) || empty($body) ){
		return ;
	}

	$template_loader = new listeo_core_Template_Loader;
	ob_start();

		$template_loader->get_template_part( 'emails/header' ); ?>
		<tr>
			<td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 25px; padding-right: 25px; padding-bottom: 28px; width: 87.5%; font-size: 16px; font-weight: 400;
			padding-top: 28px;
			color: #666;
			font-family: sans-serif;" class="paragraph">
			<?php
				echo $body;
			?>
			<?php
				if($activation_link != '')
				{
					?>
						<p> Your Account Activation Link : <a href="<?php echo $activation_link; ?>">here</a></p>
						<p>If you are facing any problems with verifying link, try copying and pasting the below url to your browser</p>
						<p><?php echo $activation_link; ?></p>
					<?php
				}
			?>
			</td>
		</tr>
	<?php
		$template_loader->get_template_part( 'emails/footer' );
		$content = ob_get_clean();

	wp_mail( @$emailto, @$subject, @$content, $headers );

}



function hypley_replace_shortcode( $args, $body ) {

	$tags =  array(
		'user_mail' 	=> "",
		'user_name' 	=> "",
		'booking_date' => "",
		'listing_name' => "",
		'listing_url' => '',
		'listing_address' => '',
		'site_name' => '',
		'site_url'	=> '',
		'payment_url'	=> '',
		'expiration'	=> '',
		'dates'	=> '',
		'children'	=> '',
		'adults'	=> '',
		'user_message'	=> '',
		'tickets'	=> '',
		'service'	=> '',
		'details'	=> '',
		'login'	=> '',
		'password'	=> '',
		'first_name'	=> '',
		'last_name'	=> '',
		'login_url'	=> '',
		'sender'	=> '',
		'conversation_url'	=> '',
		'client_first_name' => '',
		'client_last_name' => '',
		'client_email' => '',
		'client_phone' => '',
		'billing_address' => '',
		'billing_postcode' => '',
		'billing_city' => '',
		'billing_country' => '',
		'price' => '',
		'expiring' => '',
	);
	$tags = array_merge( $tags, $args );

	extract( $tags );

	$tags 	 = array( '{user_mail}',
					  '{user_name}',
					  '{booking_date}',
					  '{listing_name}',
					  '{listing_url}',
					  '{listing_address}',
					  '{site_name}',
					  '{site_url}',
					  '{payment_url}',
					  '{expiration}',
					  '{dates}',
					  '{children}',
					  '{adults}',
					  '{user_message}',
					  '{tickets}',
					  '{service}',
					  '{details}',
					  '{login}',
					  '{password}',
					  '{first_name}',
					  '{last_name}',
					  '{login_url}',
					  '{sender}',
					  '{conversation_url}',
					'{client_first_name}',
					'{client_last_name}',
					'{client_email}',
					'{client_phone}',
					'{billing_address}',
					'{billing_postcode}',
					'{billing_city}',
					'{billing_country}',
					'{price}',
					'{expiring}',
					);

	$values  = array(   $user_mail,
						$user_name ,
						$booking_date,
						$listing_name,
						$listing_url,
						$listing_address,
						get_bloginfo( 'name' ) ,
						get_home_url(),
						$payment_url,
						$expiration,
						$dates,
						$children,
						$adults,
						$user_message,
						$tickets,
						$service,
						$details,
						$login,
						$password,
						$first_name,
						$last_name,
						$login_url,
						$sender,
						$conversation_url,
						$client_first_name,
						$client_last_name,
						$client_email,
						$client_phone,
						$billing_address,
						$billing_postcode,
						$billing_city,
						$billing_country,
						$price,
						$expiring,
	);

	$message = str_replace($tags, $values, $body);

	$message = nl2br($message);
	$message = htmlspecialchars_decode($message,ENT_QUOTES);

	return $message;
}

/* Fix CMB2 Remove Icon Issue */
function add_cmb2_css_admin(){
	?>
		<style>
			.cmb2-media-status .embed-status .cmb2-remove-file-button,
			.cmb2-media-status .img-status .cmb2-remove-file-button {
				background: url(../wp-content/plugins/cmb2/images/ico-delete.png) !important;
			}
		</style>

	<?php
}

add_action('admin_head', 'add_cmb2_css_admin');




function get_vendor_average_response_time($user_id){
	global $wpdb;

	$selectCon= $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}listeo_core_conversations WHERE user_2 = {$user_id}");
	$countCon= $wpdb->get_results("SELECT COUNT(id) as count  FROM {$wpdb->prefix}listeo_core_conversations WHERE user_2 = {$user_id} ");

	$alltime = NULL;
	foreach($selectCon as $con){
		$time = $wpdb->get_results("SELECT con.id, con.user_1, con.user_2, m.message, FROM_UNIXTIME(con.timestamp),FROM_UNIXTIME( m.created_at), AVG(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(con.timestamp), FROM_UNIXTIME(m.created_at))) AS DiffInMinute FROM {$wpdb->prefix}listeo_core_conversations AS con JOIN {$wpdb->prefix}listeo_core_messages AS m ON con.id = m.conversation_id AND con.id = {$con->id} AND con.user_2 = {$user_id} AND m.sender_id = {$user_id} LIMIT 1");
		$alltime += (int)$time[0]->DiffInMinute;
	}

	if($alltime > 0 && $countCon[0]->count > 0){
		return secondsToResponseTime($alltime / $countCon[0]->count);
	}else{
		return "within 24 hours";
	}
}

function get_vendor_average_response_rate($user_id){
	global $wpdb;
	$selectCon= $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}listeo_core_conversations WHERE user_2 = {$user_id}");

	$alltime = [];
	foreach($selectCon as $con){
		$time = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeo_core_messages WHERE conversation_id = {$con->id} AND sender_id = {$user_id} LIMIT 1");
		if(!empty($time)){
			$alltime[]= $time;
		}

	}
	if((int) count($alltime) > 0 && (int) count($selectCon) > 0){
		$rate =  15;//(int) count($alltime)/ (int) count($selectCon) * 100;
	}else{
		$rate = null;
	}

	if((0 <= $rate) && ($rate <= 60)){
		return '<span style="color:#f00;">Average</span>';
	}
	if((60 <= $rate) && ($rate <= 90)){
		return '<span style="color:#ffc107;">Very good</span>';
	}
	if((90 <= $rate) && ($rate <= 100)){
		return '<span style="color:#8bc34a;">Excellent </span>';
	}

}


function secondsToResponseTime($ss) {
	$s = $ss%60;
	$m = floor(($ss%3600)/60);
	$h = floor(($ss%86400)/3600);
	$d = floor(($ss%2592000)/86400);
	$M = floor($ss/2592000);

	$time = "within 24 Hours";
	if($M == 0){
		$time = $d > 1 ? "within 24 Hours" : 'within 24 Hours';
	}
	if($d == 0){
		$time = $h > 1 ? "within $h hours" : "within $h hour";
	}
	if($h == 0){
		$time = $m > 1 ? "within $m minutes" : "within $m minute";
	}
	if($m == 0){
		$time = $s > 1 ? "within $s seconds" : "within $s second";
	}
	return $time;
}


class itf_widget extends WP_Widget {
  
function __construct() {
parent::__construct(
  
'itf_widget', 
  
__('User status'), 
  
array( 'description' => __( 'Used to display the user email status and joined date' ), ) 
);
}
  
  
public function widget( $args, $instance ) {
   global $post;
?>
	<div class="boxed-widget margin-top-30 margin-bottom-50 verification-section bad-sec">
				<?php				
					$udata = get_userdata($post->post_author);
					$registered = $udata->user_registered;
				?>
				
				<p class="mem-bdg">Joined on <?php echo date( 'F d Y', strtotime($registered));?></p>	
					<?php				
						
						if (  $udata->user_status == 1  ) {
						echo '<p class="em-ic">Email Verified</p>';
					}  
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

		                       global $wpdb;
	$selectCon= $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}listeo_core_conversations WHERE user_2 = {$udata->ID}");

	$alltime = [];
	foreach($selectCon as $con){
		$time = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeo_core_messages WHERE conversation_id = {$con->id} AND sender_id = {$udata->ID} LIMIT 1");
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
		echo '	<div id="very_responsive_section">
            <p class="very_response"><i class="fa fa-smile-o" aria-hidden="true"></i>Very Responsive</p></div>';
	}
							 ?>
	 </div>
					

<?php
     
echo $args['after_widget'];
}
 
} 
  
function itf_load_widget() {
    register_widget( 'itf_widget' );
}
add_action( 'widgets_init', 'itf_load_widget' );

function listeo_get_google_reviews($place_id,$post){
	
		$api_key = 'AIzaSyBYyQNdgo1GZ-f8x9ntJrZ1RWDrHjIo4Rk';
		
		$url = "https://maps.googleapis.com/maps/api/place/details/json?key={$api_key}&placeid={$place_id}";
		$resp_json = wp_remote_get($url);
		
		$reviews = json_decode( wp_remote_retrieve_body( $resp_json ), true );		
		
	return $reviews;
}
