<?php 

class IdeasCategoryWidget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'ideas_category_widget', // Base ID
			'Ideas_Category_Widget', // Name
			array( 'description' => __( 'Ideas Categories', 'ideas-plugin' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$args = array(
			"orderby"	=> "name",
			"order"		=> "DESC",
			"hide_empty"	=> false,
			"hierarchical"	=> false,
		);
		$sections = get_terms( "ideas_categories", $args );
		?>
		<h2>Sections</h2>
		<?php if( empty( $sections ) ) {
			echo "<p>Looks like you haven't created any sections yet! <a href='#'>Click here to add some.</a></p>";
		} else { ?>
		<ul>
			<?php foreach( $sections as $section ) { ?>
				<li><?php var_dump($section); ?></li>
			<?php } ?>
		</ul>
		<?php }
	}

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}