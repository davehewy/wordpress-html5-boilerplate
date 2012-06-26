<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */

get_header(); ?>

<div id="main" role="main" class="container">

	<h1>Clear books blog</h1>
	<?php if (function_exists('the_subheading')) { the_subheading('<h2 class="subheading">', '</h2>'); } ?>
	<div class="blog-loop">
			
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

      <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
        <header>
          <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<ul class="post-info">
				<li>
					<span class="mono-icon mono-icon-author mono-first"></span> 
					<?php the_author_posts_link(); ?>
				</li>
				<li>
					<span class="mono-icon mono-icon-time"></span>
					<time datetime="<?php the_time('Y-m-d')?>"><?php the_time('F jS, Y') ?></time>
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
        </header>
		<?php if(has_post_thumbnail()) : ?>
			<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
			<?php the_post_thumbnail(); ?>
			</a>
		<?php endif; ?>	
		<div class="entry-excerpt">
        	<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
		</div>
        <footer>
          <?php the_tags('Tags: ', ', ', '<br />'); ?> 
          Posted in <?php the_category(', ') ?>
          | <?php edit_post_link('Edit', '', ' | '); ?>
          <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
        </footer>
      </article>

    <?php endwhile; ?>

    <nav>
      <div><?php next_posts_link('&laquo; Older Entries') ?></div>
      <div><?php previous_posts_link('Newer Entries &raquo;') ?></div>
    </nav>

  <?php else : ?>

    <h2>Not Found</h2>
    <p>Sorry, but you are looking for something that isn't here.</p>
    <?php get_search_form(); ?>

  <?php endif; ?>

	</div><!-- End blog-loop-->
	
	<!-- Sidebar-->
	<div class="blog-sidebar">
		<?php get_sidebar(); ?>
	</div>
	<!-- End sidebar-->	

</div>



<?php get_footer(); ?>


