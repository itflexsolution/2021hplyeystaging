<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Listeo_Core_Listing class
 */
class Listeo_Core_Bookmarks {
	public function __construct() {

		add_action('wp_ajax_listeo_core_bookmark_this', array($this, 'bookmark_this'));
		add_action('wp_ajax_nopriv_listeo_core_bookmark_this', array($this, 'bookmark_this'));

		add_action('wp_ajax_listeo_core_unbookmark_this', array($this, 'remove_bookmark'));
		add_action('wp_ajax_nopriv_listeo_core_unbookmark_this', array($this, 'remove_bookmark'));

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_shortcode( 'listeo_bookmarks', array( $this, 'listeo_bookmarks' ) );

		add_action('wp_ajax_listeo_core_create_bookmark', array($this, 'create_bookmark'));
		add_action('wp_ajax_nopriv_listeo_core_create_bookmark', array($this, 'create_bookmark'));

		add_action('wp_ajax_listeo_core_remove_listing_from_bookmark', array($this, 'remove_listing_from_bookmark'));
		add_action('wp_ajax_nopriv_listeo_core_remove_listing_from_bookmark', array($this, 'remove_listing_from_bookmark'));
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
	
	}


	public function bookmark_this() {

	    // if ( !wp_verify_nonce( $_REQUEST['nonce'], 'listeo_core_bookmark_this_nonce')) {
	    // 	exit('No naughty business please');
	    // }   
	    $post_id = $_REQUEST['post_id'];

	    if(is_user_logged_in()){
		   	$userID = $this->get_user_id();
		   	if($this->check_if_added($post_id)) {
				$result['type'] = 'error';
				$result['message'] = __( 'You\'ve already added that post' , 'listeo_core' );
		   	} 
		   	else {
		   		$bookmarked_posts =  (array) $this->get_bookmarked_posts();
		   		$bookmarked_posts[] = $post_id;
				$action = update_user_meta( $userID, 'listeo_core-bookmarked-posts', $bookmarked_posts );
				
				if($action === false) {
					$result['type'] = 'error';
					$result['message'] = __( 'Oops, something went wrong, please try again' , 'listeo_core' );

				} else {

					$bookmarks_counter = get_post_meta( $post_id, 'bookmarks_counter', true );
			   		$bookmarks_counter++;			   
			   		update_post_meta( $post_id, 'bookmarks_counter', $bookmarks_counter );

			   		$author_id 		= get_post_field( 'post_author', $post_id );
					$total_bookmarks = get_user_meta($author_id,'listeo_total_listing_bookmarks',true);
					$total_bookmarks = (int) $total_bookmarks + 1;
					update_user_meta($author_id, 'listeo_total_listing_bookmarks', $total_bookmarks);

			  		$bookmarked_posts[] = $post_id;
			  		do_action("listeo_listing_bookmarked", $post_id, $userID );
					$result['type'] = 'success';
					$result['message'] = __( 'Listing was bookmarked' , 'listeo_core' );
					
				}
			}
		   
		} 

		wp_send_json($result);
		die();

	}	  	

	public function remove_bookmark() {
		
	   // if ( !wp_verify_nonce( $_REQUEST['nonce'], 'listeo_core_remove_fav_nonce')) {
	   //    exit('No naughty business please');
	   // }   
	   $post_id = $_REQUEST['post_id'];
	   if(is_user_logged_in()){
		   	$userID = $this->get_user_id();
		
	   		$bookmarked_posts = $this->get_bookmarked_posts();
	   		$bookmarked_posts = array_diff($bookmarked_posts, array($post_id));
	        $bookmarked_posts = array_values($bookmarked_posts);

			$action = update_user_meta( $userID, 'listeo_core-bookmarked-posts', $bookmarked_posts, false );
			if($action === false) {
				$result['type'] = 'error';
				$result['message'] = __('Oops, something went wrong, please try again','listeo_core');
			} else {
		   		$bookmarks_counter = get_post_meta( $post_id, 'bookmarks_counter', true );
		   		$bookmarks_counter--;
		   		update_post_meta( $post_id, 'bookmarks_counter', $bookmarks_counter );

		   		$author_id 		= get_post_field( 'post_author', $post_id );
				$total_bookmarks = get_user_meta($author_id,'listeo_total_listing_bookmarks',true);
				$total_bookmarks = (int) $total_bookmarks - 1;
				update_user_meta($author_id, 'listeo_total_listing_bookmarks', $total_bookmarks);
		   		do_action("listeo_listing_unbookmarked", $post_id, $userID );
				$result['type'] = 'success';
				$result['message'] = esc_html__('Listing was removed from the list','listeo_core');
			}
		} 

	   
		wp_send_json($result);
		die();

	}

	function get_user_id() {
	    global $current_user;
	    wp_get_current_user();
	    return $current_user->ID;
	}

	function get_bookmarked_posts() {
		return get_user_meta($this->get_user_id(), 'listeo_core-bookmarked-posts', true);
	}

	function check_if_added($id) {
		$bookmarked_post_ids = $this->get_bookmarked_posts();
		if ($bookmarked_post_ids) {
            foreach ($bookmarked_post_ids as $bookmarked_id) {
                if ($bookmarked_id == $id) { 
                	return true; 
                }
            }
        } 
        return false;
	}
	

	/**
	 * User bookmarks shortcode
	 */
	public function listeo_bookmarks( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your bookmarks.', 'listeo_core' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

		ob_start();
		$template_loader = new Listeo_Core_Template_Loader;

		
		$template_loader->set_template_data( array( 'ids' => $this->get_bookmarked_posts() ) )->get_template_part( 'account/bookmarks' ); 


		return ob_get_clean();
	}

	public function create_bookmark(){

		if(is_user_logged_in()){
			$userID = $this->get_user_id();
			$bookmark_name = $_POST['bookmark_name'];
			$listing_id = $_POST['listing_id'];

			$this->remove_listing_from_wishlist($userID,$listing_id);

			$current_user_wishlist = get_user_meta( $userID, 'listeo_user_wishlist',true);
			if( !empty($current_user_wishlist) ){
				if( array_key_exists($bookmark_name, $current_user_wishlist) ){
					$listing_ids = $current_user_wishlist[$bookmark_name];
					if ( !in_array($listing_id, $listing_ids) ) {
						array_push($listing_ids, $listing_id);
					}
					$current_user_wishlist[$bookmark_name] = $listing_ids;
				}
				else{
					$listing_id_arr = array($listing_id);
					$current_user_wishlist[ $bookmark_name ] = $listing_id_arr;
				}
				update_user_meta( $userID, 'listeo_user_wishlist', $current_user_wishlist );
			}
			else{
				$listing_id_arr = array($listing_id);
				$user_wishlist = array( $bookmark_name => $listing_id_arr );
				update_user_meta( $userID, 'listeo_user_wishlist', $user_wishlist );
			}

			$current_user_wishlist = get_user_meta( $userID, 'listeo_user_wishlist',true);
			$current_user_wishlist_main = "";
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
				 	}
					$current_user_wishlist_main.= '<div class="current_user_wishlist">
						<a href="javascript:;">
							<img width="50" height="50" src="'.$temp_list_get_first_img_url.'">
							<span class="current_user_wishlist_name">'.$key.'</span>
							<span data-bookmark_name="'.$key.'" class="save_listing_wishlist_name_btn" style=""> save </span>
						</a>
					</div>';
				}
			}

			$result['success'] = 1;
			$result['current_user_wishlist_main'] = $current_user_wishlist_main;
		}
		else{
			$result['success'] = 0;
		}
		
		wp_send_json($result);
		die();
	}

	public function remove_listing_from_bookmark(){

		if(is_user_logged_in()){
			$userID = $this->get_user_id();
			$listing_id = $_POST['listing_id'];
			$this->remove_listing_from_wishlist($userID,$listing_id);
			$result['success'] = 1;
		}
		else{
			$result['success'] = 0;
		}
		wp_send_json($result);
		die();
	}

	function remove_listing_from_wishlist( $userID, $listing_id){
		if( $userID != "" && $listing_id != "" ){
			$current_user_wishlist = get_user_meta( $userID, 'listeo_user_wishlist',true);
			if( !empty( $current_user_wishlist ) ){
				foreach( $current_user_wishlist as $keyy => $user_wishlist ){
					if (($key = array_search($listing_id, $user_wishlist)) !== false) {
					    unset($user_wishlist[$key]);
					    $current_user_wishlist[$keyy] = $user_wishlist;
					}
				}
				update_user_meta( $userID, 'listeo_user_wishlist', $current_user_wishlist );
			}
		}
	}

}