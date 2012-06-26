<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="main" role="main" class="container">
	<div class="blog-loop">

  <?php get_search_form(); ?>

  <section>
    <h2>Archives by Month:</h2>
    <ul>
      <?php wp_get_archives('type=monthly'); ?>
    </ul>
  </section>

  <section>
    <h2>Archives by Subject:</h2>
    <ul>
      <?php wp_list_categories(); ?>
    </ul>
  </section>

	</div><!-- End blog loop-->
	
	<!-- Sidebar-->
	<div class="blog-sidebar">
		<?php get_sidebar(); ?>
	</div>
	<!-- End sidebar-->	

</div>

<?php get_footer(); ?>
