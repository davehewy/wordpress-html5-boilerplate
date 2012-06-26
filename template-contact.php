<?php get_header(); ?>
	<div id="title" class="content single">
		<h1><?php the_title(); ?></h1>
		<?php if (function_exists('the_subheading')) { the_subheading('<h2 class="subheading">', '</h2>'); } ?>
	</div>
	
	<?php if (function_exists('has_post_thumbnail') && has_post_thumbnail()) : ?>
		<div id="featured">
			<?php the_post_thumbnail('featured-page-single', array("class" => "post_thumbnail")); ?>
		</div>
	<?php endif;?>
	
	<div class="content single clear">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div id="envelope">
				<div id="letter" class="clear">
					<?php the_content(); ?>
				</div>
			</div>
			<div class="clear">
				<?php edit_post_link('Edit Page'); ?>
			</div>
		<?php endwhile; else: ?>

		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>