<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */

get_header(); ?>

<div id="main" class="container" role="main">
	<div class="blog-single">
		<div class="blog-single-inner">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
    <header>
      <h2><?php the_title(); ?></a></h2>
    </header>
	
	<ul class="post-info">
		<li>
			<span class="mono-icon mono-icon-author mono-first"></span> 
			<?php the_author_posts_link(); ?>
		</li>
		<li>
			<span class="mono-icon mono-icon-time"></span>
			<time><?php the_time() ?></time>
		</li>
		<li>
			<span class="mono-icon mono-icon-comments"></span>
			<a href="#comments"><?php comments_number( __("No comments"), __("1 comment"), __("% comments") ); ?></a>
		</li>
		<li class="folder">
			<span class="mono-icon mono-icon-folder"></span>
			<?php the_category(', ') ?>
		</li>
	</ul>
	<div class="clearfix"></div>
	<?php the_tags( '<p class="tags"><span class="mono-icon mono-icon-tags mono-first"></span> ', ', ', '</p>'); ?>
	

	<?php
	
		// If post has a thumbnail
		if(has_post_thumbnail()){
			the_post_thumbnail();
		}
	
	?>

    <?php the_content('Read the rest of this entry &raquo;'); ?>
    <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
    <?php the_tags( '<p class="post-footer-tags"><span class="mono-icon mono-icon-tags mono-first"></span> ', ', ', '</p>'); ?>
    <footer>
      <!-- <p>This entry was posted by <?php the_author() ?>
      on <time datetime="<?php the_time('Y-m-d')?>"><?php the_time('l, F jS, Y') ?></time>
      at <time><?php the_time() ?></time>
      and is filed under <?php the_category(', ') ?>.
      You can follow any responses to this entry through the <?php post_comments_feed_link('RSS 2.0'); ?> feed.

      <?php if ( comments_open() && pings_open() ) {
        // Both Comments and Pings are open ?>
        You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> from your own site.

      <?php } elseif ( !comments_open() && pings_open() ) {
        // Only Pings are Open ?>
        Responses are currently closed, but you can <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> from your own site.

      <?php } elseif ( comments_open() && !pings_open() ) {
        // Comments are open, Pings are not ?>
        You can skip to the end and leave a response. Pinging is currently not allowed.

      <?php } elseif ( !comments_open() && !pings_open() ) {
        // Neither Comments, nor Pings are open ?>
        Both comments and pings are currently closed.

      <?php } edit_post_link('Edit this entry','','.'); ?>
      </p> -->

	<div class="about-the-author">
		<?php echo get_avatar( get_the_author_email(), '80' ); ?>
		<h4>About the author - <?php the_author(); ?></h4>
		<p><?php if ( get_the_author_meta('description') ) : 
		
		// If a user has filled out their decscription show a bio on their entries 
		echo get_the_author_meta('description');
		
		else: echo __("No author bio."); endif; ?></p>
		
		<?php 
		// Grab the google plus account out the URL
		$google_plus = get_the_author_meta( 'google_plus', get_the_author_meta( 'ID' ) );
		
		if($google_plus): ?>
		
		<a href="https://plus.google.com/<?=$google_plus?>?rel=author">Google+</a>
		
		<?php endif; ?>		
		
	</div>

    </footer>
    <nav id="other-posts">
      <div class="previous-link"><?php previous_post_link('&laquo; %link') ?></div>
      <div class="next-link"><?php next_post_link('%link &raquo;') ?></div>
    </nav>

	<div class="clearfix"></div>

    <?php comments_template(); ?>

  </article>

<?php endwhile; else: ?>

  <p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>
		
		</div><!-- End blog single inner-->
	</div><!-- End blog single-->
	
	<!-- Sidebar-->
	<div class="blog-sidebar">
		<?php get_sidebar(); ?>
	</div>
	<!-- End sidebar-->

</div>

<?php get_footer(); ?>
