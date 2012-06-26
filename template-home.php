<?php
// Template name: TPL - Homepage
get_header(); ?>

<section id="body post-<?php the_ID(); ?>" <?php post_class('full-width'); ?>>
    <div id="body-wrap" class="container">
    	<div id="body-content" class="clearfix rtf">
    	
    	<div id="main-content">
			
			<div class="clearbooksLogo">
				<img src="<?=$GLOBALS["TEMPLATE_RELATIVE_URL"]?>images/ClearBooks.png" alt="Clearbooks Ltd" title="Clearbooks Ltd">
			</div>
			<h1 class="homepage">
				<strong>Clear Books Ltd</strong> create simple web-based apps to aid productivity and save time
			</h1>
		
			<?php if (function_exists('the_subheading')) { the_subheading('<h2 class="subheading">', '</h2>'); } ?>		
			
			<section class="home-divider">
				<div class="underline-effect">
					<h2 class="section-heading">Our Business Software</h2>
				</div>
				
				<div class="clearfix product-top-row">					
					<div class="product-wrapper">
						<a href="#" class="product product-blue">
							<div class="mini-logo"></div>
							<span><strong>Clear</strong> Books</span>
							<p>Simple, Time saving Accounting Software</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>
					<div class="product-wrapper">
						<a href="#" class="product product-yellow">
							<div class="mini-logo"></div>
							<span><strong>Open</strong> Books</span>
							<p>Nullam quis risus eget urna mollis ornare vel eu leo.</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>	
					</div>
					<div class="product-wrapper product-end">
						<a href="#" class="product product-red">
							<div class="beta"></div>
							<div class="mini-logo"></div>
							<span><strong>Fluid</strong> CRM</span>
							<p>Nullam quis risus eget urna mollis ornare vel eu leo.</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>		
				</div>
				
				<div class="clearfix product-overflow">
					<div class="product-wrapper">
						<a href="#" class="product product-blue">
							<div class="beta"></div>
							<div class="mini-logo"></div>
							<span><strong>Task</strong> Desk</span>
							<p>Vestibulum id ligula porta felis euismod semper.</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>
					<div class="product-wrapper">
						<a href="#" class="product product-yellow">
							<div class="beta"></div>
							<div class="mini-logo"></div>
							<span><strong>Quick</strong> HR</span>
							<p>Vestibulum id ligula porta felis euismod semper.</p>
						</a>	
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>
					<div class="product-wrapper">
						<a href="#" class="product product-red">
							<div class="beta"></div>
							<div class="mini-logo"></div>
							<span><strong>Query</strong> Box</span>
							<p>Vestibulum id ligula porta felis euismod semper.</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>
					<div class="product-wrapper">
						<a href="#" class="product product-blue">
							<div class="beta"></div>
							<div class="mini-logo"></div>
							<span><strong>PDF</strong> Cubed</span>
							<p>Vestibulum id ligula porta felis euismod semper.</p>
						</a>
						<span class="find-more"><a href="#">Find out more &raquo;</a></span>
					</div>									
				</div>				
					
			</section>
				
			<section>
				<div class="underline-effect">
					<h2 class="section-heading">We've been featured in</h2>
				</div>
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/featured_in.png">
			</section>
			
			<section>
				<div class="underline-effect">
					<h2 class="section-heading">Connect with us</h2>
				</div>
				
				<!-- Connect with us section -->
				
				<div class="connect-with-us">
					<div class="latest_tweet">
						<span>This is what we do when we do it.</span>
					</div>
					<ul class="social-media">
						<li>
							<a href="#" class="twitter">
								Follow us on<span>Twitter</span>
							</a>
						</li>
						<li>
							<a href="#" class="facebook">
								Like us on<span>Facebook</span>
							</a>
						</li>
						<li>
							<a href="#" class="flickr">
								View our pics on<span>Flickr</span>
							</a>
						</li>
						<li>
							<a href="mailto:contact@clearbooks.co.uk" class="email end">
							Drop us an<span>Email</span>
							</a>
						</li>
					</ul>
					
					<div class="clearfix"></div>
					
					<!-- Flickr stuff -->
					<ul class="flickr-feed">
						<li><a href="#"></a></li>
						<li><a href="#"></a></li>
						<li><a href="#"></a></li>
						<li><a href="#"></a></li>
						<li><a href="#"></a></li>
						<li><a href="#" class="end"></a></li>
					</ul>
					
				</div>
				
			</section>		
			
			<?php
				
			// Do not show the what our customers section if no testimonials exist on the backend 
			
			query_posts('posts_per_page=5&post_type=testimonials&post_status=publish');
			if(have_posts()): ?>
			
			<section>
				<div class="underline-effect">
					<h2 class="section-heading">What our customers say</h2>
				</div>
				
				<div class="home-testimonials">
					<a href="#" class="arrow-left"></a>
					<a href="#" class="arrow-right"></a>
					<ul>
		
					<?php
					
					while ( have_posts() ) : the_post();
					$custom = get_post_custom(get_the_ID());
			        $author = $custom["testi_author"][0];
					$author_company = $custom["testi_company"][0]; 
					$author_url = $custom['testi_url'][0];
					$author_segment = $custom['testi_author_segment'][0];
					$gravatar_email = $custom['testi_gravatar_email'][0]; ?>
					
						<li>
							
							<blockquote <?php if($author_url) echo "cite=\"$author_url\""; ?>">
								<?php if($gravatar_email): echo get_avatar($gravatar_email,75); endif; ?>
								<?php echo ($author) ? "<cite>$author" : ''; ?>
								<?php if(!$author_company) echo '</cite>'; ?>
								<?php echo ($author_company) ? "<span>$author_company</span></cite>" : ''; ?>
								
								<strong><?php the_title(); ?></strong>
								<?php the_content(); ?>
								<span></span>
								<?php 
								if($author_segment) echo "<footer>$author_segment"; 
								if($author_url) echo " - <a href=\"$author_url\">$author_url</a>";
								if($author_segment) echo "</footer>"; ?>
							</blockquote>
							
						</li>	
				
					<?php endwhile; ?>		
											
					</ul>
				</div>
				
			</section>	
			
			<?php endif; /* End testimonial section */ wp_reset_query(); ?>
			
			
			<!-- Start happiness section -->
			<section id="happiness">
				<div class="home-happiness">
					<p><span class="happiness-icon"></span>Click <a href="#">here</a> to see how our customers rate us on our Customer Happiness Report</p>
				</div>
			</section>
			<!-- End happiness section -->
					
			
			<section>
				<div class="underline-effect">
					<h2 class="section-heading">More projects from the team</h2>
				</div>
				
				<div class="team-projects">
					<a href="#" class="we-love-clearbooks"></a>
					<a href="#" class="stanley"></a>
					<a href="#" class="accounting-is-boring"></a>
					<a href="#" class="find-a-uk-accountant"></a>
					<a href="#" class="wordpress-timeline"></a>
				</div>
				
			</section>			
			
			
   			<?php 
	   			if( have_posts() ) the_post(); the_content();
	   		?>
    	</div>
    	
    	</div><!-- #body-content -->
    </div><!-- #body-wrap -->
</section><!-- #body -->

<?php get_footer();?>