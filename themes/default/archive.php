<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Archive Template
 *
 *
 * @file           archive.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.1
 * @filesource     wp-content/themes/responsive/archive.php
 * @link           http://codex.wordpress.org/Theme_Development#Archive_.28archive.php.29
 * @since          available since Release 1.0
 */
$args = array(
	"post_type"			=> "idea",
	"meta_key"			=> "votes",
	"meta_query"		=> array(
		array(
			"key"		=> "votes",
		),
	),
	"orderby"			=> "meta_value_num",
	"order"				=> "DESC",
);

query_posts( $args );

get_header(); ?>

<div id="content-archive" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">

	<?php if (have_posts()) : ?>
        
        <?php get_template_part( 'loop-header' ); ?>
                    
        <?php while (have_posts()) : the_post(); ?>
        
			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>       
				<?php responsive_entry_top(); ?>

                <?php get_template_part( 'post-meta' ); ?>


				<div class="grid col-60">
            		<a class="vote-up" data-id="<?php echo $id; ?>">Up</a>
            		<p class="current-votes"><?php echo get_post_meta($id, "votes", true); ?></p>
            		<a class="vote-down" data-id="<?php echo $id; ?>">Down</a>
				</div>

				<div class="grid col-780">
	                
	                <div class="post-entry">
	                    <?php if ( has_post_thumbnail()) : ?>
	                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
	                    <?php the_post_thumbnail('thumbnail', array('class' => 'alignleft')); ?>
	                        </a>
	                    <?php endif; ?>
	                    <?php the_excerpt(); ?>
	                    <?php wp_link_pages(array('before' => '<div class="pagination">' . __('Pages:', 'responsive'), 'after' => '</div>')); ?>
	                </div><!-- end of .post-entry -->

	            </div>
                
                <?php get_template_part( 'post-data' ); ?>
				               
				<?php responsive_entry_bottom(); ?>      
			</div><!-- end of #post-<?php the_ID(); ?> -->       
			<?php responsive_entry_after(); ?>
            
        <?php 
		endwhile; 

		get_template_part( 'loop-nav' ); 

	else : 

		get_template_part( 'loop-no-posts' ); 

	endif; 
	?>  
      
</div><!-- end of #content-archive -->
        
<?php get_sidebar(); ?>
<?php get_footer(); ?>
