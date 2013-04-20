<?php

class Ideas {
	static function activate() {
		$theme_dir = get_theme_root();
		$theme_path = $theme_dir . "/ideas-plugin-theme";

		// Create a link to the theme
		// Thanks to Digressit (http://digress.it/)
		if( is_link( $theme_path ) ){
			unlink( $theme_path );
		}
	
		// Can we write to the theme directory?
		if( is_writable( $theme_dir ) ){

			// Does the theme already exist in the theme directory?
			if( ! file_exists( $theme_path ) ){

				// Try to create a symlink
				if( symlink( IDEAS_THEME_PATH, $theme_path ) ){
					// All good
				} else{
					die( "Couldn't write to " . $theme_dir . '. Ideas Plugin needs to install a theme to continue.' );
				}
			} else {
				// All good, already exists
			}
		} else {
			die( "Couldn't write to " . $theme_dir . '. Ideas Plugin needs to install a theme to continue.' );
		}

		switch_theme('ideas-plugin-default', 'ideas-plugin-default');	

	}

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, "enqueue_scripts" ) );

		add_action( 'init', array( &$this, 'register_idea_post_type') );
		add_action( 'wp_ajax_vote_up', array( &$this, 'vote_up' ) );
		add_action( 'wp_ajax_vote_down', array( &$this, 'vote_down' ) );
		add_action( 'wp_head', array( &$this, 'voting_script' ) );
	}

	function enqueue_scripts() {
		wp_register_script( "voting", plugin_dir_url(dirname(__FILE__)) . "js/voting.js", array("jquery") );
		wp_enqueue_script( "voting" );
	}

	function register_idea_post_type() {

		$labels = array(
			'name' => 'Ideas',
			'singular_name' => 'Idea',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Idea',
			'edit_item' => 'Edit Idea',
			'new_item' => 'New Idea',
			'all_items' => 'All Ideas',
			'view_item' => 'View Idea',
			'search_items' => 'Search Ideas',
			'not_found' =>  'No ideas found',
			'not_found_in_trash' => 'No ideas found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Ideas'
		);
		$settings = array(
			'labels'	=> $labels,
			'public'	=> true,
			'publicly_queryable'	=> true,
			'show_ui'	=> true,
			'show_in_menu'	=> true,
			'rewrite'	=> array( 'slug' => 'idea' ),
			'capability_type'	=> 'post',
			'has_archive'	=> true,
			'hierarchical'	=> false,
			'supports'	=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
		);

		register_post_type( "idea", $settings );

		$tax_labels = array(
			'name'                         => _x( 'Tags', 'taxonomy general name' ),
			'singular_name'                => _x( 'Tag', 'taxonomy singular name' ),
			'search_items'                 => __( 'Search Tags' ),
			'popular_items'                => __( 'Popular Tags' ),
			'all_items'                    => __( 'All Tags' ),
			'parent_item'                  => null,
			'parent_item_colon'            => null,
			'edit_item'                    => __( 'Edit Tag' ), 
			'update_item'                  => __( 'Update Tag' ),
			'add_new_item'                 => __( 'Add New Tag' ),
			'new_item_name'                => __( 'New Tag Name' ),
			'separate_items_with_commas'   => __( 'Separate tags with commas' ),
			'add_or_remove_items'          => __( 'Add or remove tags' ),
			'choose_from_most_used'        => __( 'Choose from the most used tags' ),
			'not_found'                    => __( 'No tags found.' ),
			'menu_name'                    => __( 'Tags' )
		);

		$args = array(
			'labels'	=> $tax_labels,
			'hierarchical'	=> false,
			'show_ui'	=> true,
			'show_admin_column'	=> true,
			'query_var'	=> true,
			'rewrite'	=> array( 'slug'	=> 'tag' ),
		);

		register_taxonomy( 'ideas_tag', 'idea', $args );


		$tax_labels = array(
			'name'                         => _x( 'Sections', 'taxonomy general name' ),
			'singular_name'                => _x( 'Section', 'taxonomy singular name' ),
			'search_items'                 => __( 'Search Sections' ),
			'popular_items'                => __( 'Popular Sections' ),
			'all_items'                    => __( 'All Sections' ),
			'parent_item'                  => null,
			'parent_item_colon'            => null,
			'edit_item'                    => __( 'Edit Section' ), 
			'update_item'                  => __( 'Update Section' ),
			'add_new_item'                 => __( 'Add New Section' ),
			'new_item_name'                => __( 'New Section Name' ),
			'separate_items_with_commas'   => __( 'Separate sections with commas' ),
			'add_or_remove_items'          => __( 'Add or remove sections' ),
			'choose_from_most_used'        => __( 'Choose from the most used sections' ),
			'not_found'                    => __( 'No sections found.' ),
			'menu_name'                    => __( 'Sections' )
		);

		$args = array(
			'labels'	=> $tax_labels,
			'hierarchical'	=> false,
			'show_ui'	=> true,
			'show_admin_column'	=> true,
			'query_var'	=> true,
			'rewrite'	=> array( 'slug'	=> 'section' ),
		);

		register_taxonomy( 'ideas_category', 'idea', $args );



		flush_rewrite_rules();
	}

	function vote_up() {
		check_ajax_referer( __FILE__, 'nonce', true );

		if(! is_numeric( $_POST['id'] ) )
			return false;
		else
			$post_id = $_POST['id'];

		$return = Ideas::change_votes( $post_id, 1 );

		echo $return;
		die(); // this is required to return a proper result
	}

	function vote_down() {
		check_ajax_referer( __FILE__, 'nonce', true );

		if(! is_numeric( $_POST['id'] ) )
			return false;
		else
			$post_id = $_POST['id'];

		$return = Ideas::change_votes( $post_id, -1 );

		echo $return;
		die(); // this is required to return a proper result
	}

	// Voting is like a toggle switch, repeatedly hitting up will result in the vote being added then removed, then added...etc
	static function change_votes( $post_id, $difference ) {
		$votes = get_post_meta( $post_id, 'votes', true );
		$voters = (array)get_post_meta( $post_id, 'voters', true );
		$user_id = get_current_user_id();

		$response_type = "alert";
		$message = "You can only vote once";

		if( $user_id == 0 ){
			$message = "You need to log in to vote";
		} else {

			if( array_key_exists( $user_id, $voters ) ) {
				if( $voters[$user_id] == $difference ) {
					// Already voted this way before

					// Unvote
					$votes = intval( $votes ) - $difference;

					unset( $voters[$user_id] );

					$message = "You've successfully withdrawn your vote";
				} else {
					// Already voted, but the other way

					// 2 point turnaround
					$votes = intval( $votes ) + (2 * $difference);

					$voters[$user_id] = $difference;

					$message = "Thanks for voting!";
				}

				update_post_meta( $post_id, 'votes', $votes );
				update_post_meta( $post_id, 'voters', $voters );

				$response_type = "success";
			} else {
				$votes = intval( $votes ) + $difference;
				$voters[$user_id] = $difference;

				update_post_meta( $post_id, 'votes', $votes );
				update_post_meta( $post_id, 'voters', $voters );

				$message = "Thanks for voting!";
				$response_type = "success";
			}
		}
		$return = array(
			"votes"		=> $votes,
			"message"	=> $message,
			"result"	=> $response_type
		);

		return json_encode($return);
	}

	function voting_script() {
		?>
<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	var ajaxnonce = '<?php echo wp_create_nonce(__FILE__); ?>';
</script>
		<?php
	}
}
