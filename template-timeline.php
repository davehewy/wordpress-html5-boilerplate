<?php
// Template name: TPL - Timeline
get_header(); ?>

<section id="body" class="full-width">
    <div id="body-wrap" class="container">
    	<div id="body-content" class="clearfix rtf">

			<?php echo $mf_timeline->get_timeline(); ?>
		
		</div>
	</div>
</section>

<?php get_footer(); ?>