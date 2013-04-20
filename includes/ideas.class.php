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
		add_action( 'init', array( &$this, 'register_idea_post_type') );
		add_action( 'wp_vote_up', array( &$this, 'vote_up' ) );
		add_action( 'wp_vote_down', array( &$this, 'vote_down' ) );
		add_action( 'admin_print_scripts', array( &$this, 'voting_script' ) );
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

		register_taxonomy( 'idea_category', 'idea', $args );

		
	}

	function vote_up() {
		if(! is_numeric( $_POST['id'] ) )
			return false;

		global $wpdb;
		$current_votes = get_post_meta($post_id, 'votes', $single);
		if( ! $current_votes )
			$current_votes = 0;
		$current_votes += 1;
		update_post_meta( $post_id, 'votes', $current_votes );
		echo $current_votes;
		die(); // this is required to return a proper result
	}

	function vote_down() {
		if(! is_numeric( $_POST['id'] ) )
			return false;

		global $wpdb;
		$current_votes = get_post_meta($post_id, 'votes', $single);
		if( ! $current_votes )
			$current_votes = 0;
		$current_votes -= 1;
		update_post_meta( $post_id, 'votes', $current_votes );
		echo $current_votes;
		die(); // this is required to return a proper result
	}

	function voting_script() {
		?>
<script type="text/javascript">
jQuery(document).ready(function($) {

	jQuery(".vote-up").click( function() {
		var data = {
			action: 'vote_up',
			// Post ID
			id: jQuery(this).attr('data-id')
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				alert('New votes: ' + response);
			}
		});
	});

	jQuery(".vote-down").click( function() {
		var data = {
			action: 'vote_down',
			// Post ID
			id: jQuery(this).attr('data-id')
		};

		jQuery.post(ajaxurl, data, function(response) {
			if( response ) {
				alert('New votes: ' + response);
			}
		});
	});
});
</script>
		<?php
	}
}
