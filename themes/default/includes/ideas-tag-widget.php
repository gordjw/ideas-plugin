<?php 

class IdeasTagWidget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'ideas_tag_widget', // Base ID
			'Ideas_Tag_Widget', // Name
			array( 'description' => __( 'Ideas Tags', 'ideas-plugin' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$args = array(
			"orderby"	=> "name",
			"order"		=> "DESC",
			"hide_empty"	=> false,
			"hierarchical"	=> false,
		);
		$sections = get_terms( "ideas_tag", $args );
		?>
		<div class="widget-wrapper widget_idea_tags">
			<div class="widget-title">Tags</div>
			<?php if( empty( $sections ) ) {
				echo "<p>Nobody's used any tags yet. :(</p>";
			} else { ?>
			<ul>
				<?php foreach( $sections as $section ) { ?>
					<li><?php var_dump($section); ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
		</div>
	<?php }

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}