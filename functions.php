<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */

// Call in the theme config.

require_once( TEMPLATEPATH.'/base/theme.php' );
require_once( TEMPLATEPATH.'/base/theme-config.php' );

$theme = new Theme();
$theme->init( $theme_options );

//require_once(TEMPLATEPATH.'/global_vars.php'); // Global variables
    
    /* Functions */
    
    /**
     * Excerpt Read More Link
     * Customised the read more link for page excerpts
     *
     * @param $output string the output to prepend to the link
     *
     * @return $output string the formatted link
     * @author Matt Fairbrass
     **/
    function excerpt_read_more_link($output) {
        global $post;
        return $output.' <a href="'.get_permalink($post->ID).'" class="post-more-link">Read More...</a>';
    }
    
    /**
     * Excerpt Length
     * Override the default excerpt length
     *
     * @param $length int the length of the excerpt
     *
     * @return $length int the length of the excerpt
     * @author Matt Fairbrass
     **/
    function excerpt_length($length) {
        return 20;
    }
    
    /**
     * Debug Dump
     * Formats var dump with <xmp> tags for a more readable output.
     *
     * @param $var mixed the variable to dump
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function debug_dump($var) {
        echo '<xmp>';
        var_dump($var);
        echo '</xmp>';
    }
    
    /**
     * What Template
     * Development function to help identify what page template wordpress is serving up. For pre-production use only.
     *
     * @return string the page template name
     * @author Matt Fairbrass
     **/
    function what_template() { 
        global $template;
        return basename($template);
    }
    
    /**
     * Find Pages
     * Recursively find files, optionally of a specific type and return array of paths.
     *
     * @param $dir string the directory to begin search at
     * @param $pattern regex the pattern to match on. eg: '.php'
     * @param $replace array the part of the path to replace and what to replace it with
     *
     * @return $file_paths array an array of file paths
     * @author Matt Fairbrass
     **/
    function find_files($dir = '.', $pattern = '/./', $replace = null, &$file_paths) {
        $prefix = $dir . '/';
        $dir = dir($dir);

        while (false !== ($file = $dir->read())) {
            if ($file === '.' || $file === '..') continue;

            $file_name = $file;
            $file = $prefix . $file;

            if (is_dir($file)) {
                find_files($file, $pattern,  $file_paths, $replace);
            }

            if (preg_match($pattern, $file)){
                if($replace != null && is_array($replace)) {
                    $file_paths[] = str_replace($replace[0], $replace[1], $file);
                }
                else {
                    $file_paths[] = $file;
                }   
            }
        }

        return $file_paths;
    }
    
    /**
     * Get Video Player
     * Returns the video player object depending on what video we are displaying.
     * 
     * @param $the_video string the url or id to a video. For YouTube and Vimeo videos, only pass the ID.
     * @param $width int the width of the video player
     * @param $height int the height of the video player
     * @param $service string the video service provider, ie: youtube/vimeo.
     * @param $title string custom title of the video. Only works with futube player.
     *
     * @return $video_html string the video object html
     * @author Matt Fairbrass
     **/
    function get_video_player($the_video, $width, $height, $service, $title = "") {
        do_action('get_video_player');
        
        switch($service) {
            case 'youtube' :
                $video_html = do_shortcode('[futube video="'.$the_video.'" align="center" author="'.get_the_title().'" bgcolor="#FFFFFF" color="#058FC9" hd="true" width="'.$width.'" height="'.$height.'" title="'.$title.'"]');
            break;
            
            case 'vimeo' :
                $video_html = '<iframe src="http://player.vimeo.com/video/'.$the_video.'?title=0&amp;byline=0&amp;portrait=0" width="'.$width.'" height="'.$height.'" frameborder="0" class="cb_video"></iframe>';
            break;
        }
        
        return $video_html;
    }
    
    /**
     * Get Page Title
     * Called in header.php to create the page titles for each page
     * 
     * @see header.php
     *
     * @return void
     * @author Matthew Fairbrass
     **/
    function get_page_title() {
        global $wp_query;

        switch( $wp_query->post->ID ) {
            case '1664' : // Business Directory
                if( $wp_query->get( 'industry' ) ) {
                    $cbdb = new wpdb(CBDB_USER, CBDB_PSWD, CBDB_NAME, CBDB_HOST);
                    
                    // Directory Listings
                    $sql = $cbdb->prepare( "SELECT type FROM industry WHERE id = %d", $wp_query->get( 'industry' ) );
                    $industry = $cbdb->get_var( $sql );
                    
                    $title .= htmlentities( str_replace( array('Or', 'And'), array('or', 'and'), ucwords( strtolower( $industry ) ) ) ) . ' Accounting Software  &raquo; ';
                    
                }
                else if( $wp_query->get( 'business' ) ) {
                    $cbdb = new wpdb(CBDB_USER, CBDB_PSWD, CBDB_NAME, CBDB_HOST);
                    
                    // Firm Specific Page
                    $sql = $cbdb->prepare("SELECT b.firm_name, i.type FROM business_directory AS b LEFT JOIN industry AS i ON i.id = b.industry LEFT JOIN groups AS g ON g.ID = b.db_id WHERE g.date_closed IS NULL AND g.date_paid IS NOT NULL AND b.link_name = %s", $wp_query->get('business') );
                    $business = $cbdb->get_results( $sql );
                    
                    $title .= htmlentities( str_replace( array('Or', 'And'), array('or', 'and'), ucwords( strtolower( $business[0]->type ) ) ) ) . ' Accounting Software &raquo; '.$business[0]->firm_name;
                    
                }
                else {
                    $title .= 'Directory of Businesses using Clear Books Accounting Software  &raquo; ';
                }
            break;
            
            case '1649' : // Accounting & Bookpeeing Partners Directory
                if( $wp_query->get( 'business' ) ) {
                    $cbdb = new wpdb(CBDB_USER, CBDB_PSWD, CBDB_NAME, CBDB_HOST);
                    
                    // Specific business
                    $sql = $cbdb->prepare( "SELECT firm_name FROM `accountants` WHERE `link_name` = %s AND `link_town` = %s AND `link_postcode` = %s", $wp_query->get('business'), $wp_query->get('town'), $wp_query->get('postcode') );
                    $business = $cbdb->get_results( $sql );
                    $title .= "{$business[0]->firm_name} - Online Accountants  &raquo; ";
                }
                else {
                    // Directory listing
                    $title .= 'Directory of Accountants &amp; Bookkeepers using Clear Books  &raquo; ';
                }
            break;
            
            default:
                if ( is_post_type_archive() ) {
                    $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                    
                    switch( $term->taxonomy ) {
                        case 'support' :
                            $title .= ( !empty( $term ) ) ? $term->name . ' Guides &raquo;' : 'Clear Books Help Guides';
                            break;

                        default :
                            $title .= $term->name;
                            break;
                    }
                    
                }
                else if ( function_exists( 'meta_title' ) ) {
                    $title .= meta_title( '&raquo;', false, 'right' );
                }
                else {
                    $title .= wp_title( '&raquo;', false, 'right' ); 
                }
            break;
        }
        
        $title .= get_bloginfo('name') . ' | ' . get_bloginfo('description');
        
        return $title;
    }
    
    /**
     * Check Video Support
     * Executes client side Javascript to check whether the user supports either Flash or HTML5 video.
     * 
     * @see add_action('get_video_player', 'check_video_support', 1);
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function check_video_support() { ?>
        <script type="text/javascript" class="check_video_support">
            jQuery(document).ready(function() {
                var _flash_installed = ((typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object") || (window.ActiveXObject != undefined && (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) != false));
                
                var no_video = '<div class="no_video"><p>Your browser does not support HTML5 video or Flash video playback.</p> <p><a href="http://get.adobe.com/flashplayer/" target="_blank">Install Adobe Flash Player</a> or upgrade to a modern web browser to watch this video.</p></div>';
                
                if (Modernizr.video.h264 == "" && _flash_installed == false){
                    jQuery('.cb_video, object[id^="fubravideo_"]').after(no_video);
                    jQuery('.cb_video, object[id^="fubravideo_"]').remove();
                    
                    var height = $('.no_video').parent().height() - 30;
                    jQuery('.no_video').css('height', height);
                    
                    jQuery('.check_video_support').remove();
                }
            });
        </script>
    <?php }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function check_cb_landing_featured() { ?>
        <script type="text/javascript">
            Modernizr.load({
                test: Modernizr.geolocation,
                both : [ 'app.js', 'extra.js' ],
                nope: 'geo-polyfill.js'
            });
        </script>
    <?php }
    
    /**
     * Format Social Network Comments
     * Override the default JS comment formatting of the Simple Facebook Connect plugin for comments.
     * Ideally the plugins should provide better hooks and customisation options.. but alas no. So this is a bit of a hack.
     *
     * @see add_action('comment_form_after_fields', 'format_social_network_comments', 40); // Called at 40 so it outputs after the plugin JS scripts.
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function format_social_network_comments() { 
        global $sfc_comm_comments_form, $stc_comm_comments_form;
    ?>  
        <?php if($sfc_comm_comments_form == true) : // Simple Facebook Connect?>
            <script type="text/javascript">
                // Override the default SFC plugin javascript output with our own.
                sfc_update_user_details = function() {
                    if (!jQuery('#fb-user').length) {
                        jQuery('#comment-user-details').hide().after("<p>Logged in as <fb:name uid='loggedinuser' useyou='false'></fb:name> via Facebook. <a href='#' onclick='FB.Connect.logoutAndRedirect(\"<?php the_permalink() ?>\"); return false;'> <?php echo addslashes(__('Logout', 'sfc')); ?></a></p>");

                        jQuery('#comment .avatar').hide().after('<span class="avatar"><fb:profile-pic uid="loggedinuser" facebook-logo="false" size="normal" height="34"></fb:profile-pic></span>');

                        jQuery('#sfc_comm_send').html('<input style="width: auto;" type="checkbox" id="sfc_comm_share" /><label for="sfc_comm_send"><fb:intl>Share Comment on Facebook</fb:intl></label>');
                    }

                    // Refresh the DOM
                    FB.XFBML.Host.parseDomTree();
                };
            </script>
        <?php endif;?>
    <?php
    }
    
    /**
     * Format Text To HTML
     * Formats given text to be wrapped with paragraph tags by looking at newline character breaks
     *
     * @param $text string the text to format
     *
     * @return string the formatted text
     * @author Matt Fairbrass
     **/
    function format_text_to_html($text) {
        if(empty($text)) {
            return null;
        }
        
        return str_replace('<p></p>', '', '<p>'.preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $text. '</p>'));
    }
    
    /**
     * Format Text To Links
     * Transforms text urls into valid html hyperlinks
     * 
     * @param string $text the text to format
     *
     * @return string $text the formatted text
     * @author Matt Fairbrass
     **/
    function format_text_to_links($text) {
        if(empty($text)) {
            return null;
        }
        
        $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);
        
        return $text;
    }
    
    /**
     * Format Text To Twitter
     * Transforms text to twitter users (@someone) links and hastag (#something) to links
     * 
     * @param string $text the text to format
     *
     * @return string $text the formatted text
     * @author Matt Fairbrass
     **/
    function format_text_to_twitter($text) {
        if(empty($text)) {
            return null;
        }
        
        $text = preg_replace("/@(\w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $text);
        $text = preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>',$text);
        
        return $text;
    }
    
    /**
     * Format Text To Address
     * Formats a given string to an address output
     *
     * @param string $text the text to format
     *
     * @return string $text the formatted text
     * @author Matt Fairbrass
     **/
    function format_text_to_address($address) {
        if(empty($address)) {
            return null;
        }
        
        if(!is_array($address)) {
            $address = explode('\n', $address);
        }
        
        if(preg_match('^[0-9]', $address[0])) { // Building starts with a number, so append on the same line as address1
            $text .= $address[0].' ';
        }
        else if(!empty($address[0])) {
            $text .= $address[0].'<br/>';
        }

        $address = array_splice($address, 1);
        return '<p>'.$text.implode('<br/>', array_filter($address)).'</p>';
    }
    
    /**
     * Format Shorten Text
     * Truncates text with ellipsis if greater than the specified chars.
     *
     * @param string $text the text to truncate
     * @param int $chars the number of chars to truncate to. Default = 60.
     *
     * @return string $text the formatted text.
     * @author Fubra
     **/
    function format_shorten_text($text, $chars = 60) {
        if(strlen($text) > $chars) {
            $text = $text." ";
            $text = substr($text,0,$chars);
            $text = substr($text,0,strrpos($text,' '));
            $text = $text."...";
        }
        
        return $text;
    }
    
    /**
     * Is Marketing View
     * Shows content relevant to the marketing view
     *
     * @return boolean true if cookie is not found
     * @author Matt Fairbrass
     **/
    function is_marketing_view() {
        if (!isset($_COOKIE['tailored_greetings']) || empty($_COOKIE['tailored_greetings'])) {
            return true;
        }

        return false;
    }
    
    /**
     * Is Customer View
     * Shows content relevant to the customer view (opposite to marketing view)
     *
     * @return boolean true if cookie is found
     * @author Matt Fairbrass
     **/
    function is_customer_view() {
        if (isset($_COOKIE['tailored_greetings']) && $_COOKIE['tailored_greetings'] == "1") {
            return true;
        }

        return false;
    }

    /**
     * Just logged out view
     * Shows custom logged out header message
     *
     * @return boolean true if cookie is found
     * @author Edward Halls
     **/
    function just_loggedout_view() {
        if (isset($_COOKIE['logged_out']) || !empty($_COOKIE['logged_out'])) {
            return true;
        }

        return false;
    }

    
    /**
     * Switch View
     * Switches between the marketing and customer view by setting and destroying the cookie.
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function switch_view() {
        // Current view is marketing, switch to customer
        if (!isset($_COOKIE['tailored_greetings'])) {
            $timeframe = time()+(60*60*24*100) ; $val = "1";
            $_COOKIE['tailored_greetings'] = "1";
        } // Current view is customer, switch to marketing
        else {
            $timeframe = time()-10000 ; $val = "";
            $_COOKIE['tailored_greetings'] = "";
        }

        setcookie("tailored_greetings", $val, $timeframe , '/', '.clearbooks.co.uk' );
    }
    
    /**
     * Is Child
     * Check if a page is a direct child
     *
     * @param $page_id the id of the page to check
     *
     * @return boolean true if page is a direct child
     * @author Matt Fairbrass
     **/
    function is_child($page_id) { 
        global $post;
        
        if(is_page() && ($post->post_parent == $page_id)) {
            return true;
        }
        else { 
            return false; 
        }
    }

    /**
     * Is Ancestor
     * Check if a page is an ancestor
     *
     * @param $page_id the id of the page to check
     *
     * @return boolean true if page is an ancestor
     * @author Matt Fairbrass
     **/
    function is_ancestor($post_id) {
        global $wp_query;
        $ancestors = $wp_query->post->ancestors;
        
        if (in_array($post_id, $ancestors)) {
            return true;
        } 
        else {
            return false;
        }
    }
    
    /**
     * Admin Init
     * Hook function for when the admin page is initiated
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function admin_init() {
        global $post;
        
        wp_register_script('wp_page_attributes', get_bloginfo("template_url").'/scripts/js/jquery.wp_page_attributes.min.js', array('jquery'));
        wp_enqueue_script('wp_page_attributes');
        
        wp_register_script('wp_case_study_meta', get_bloginfo("template_url").'/scripts/js/jquery.wp_case_study_meta.min.js', array('jquery'));
        wp_enqueue_script('wp_case_study_meta');
        
        wp_register_script('wp_api_doc', get_bloginfo("template_url").'/scripts/js/jquery.wp_api_doc.min.js', array('jquery'));
        wp_enqueue_script('wp_api_doc');
        
        wp_register_script('wp_user_profile', get_bloginfo("template_url").'/scripts/js/jquery.wp_user_profile.min.js', array('jquery'));
        wp_enqueue_script('wp_user_profile');
        
        //wp_register_script('add-widget-field', get_bloginfo("template_url").'/scripts/js/jquery.addwidgetfield.min.js', array('jquery'));
        //wp_enqueue_script('add-widget-field');
        add_meta_box("guide_cpt_meta", "Help Guide Meta", "guide_cpt_meta", "guide", "side", "low");
        add_meta_box("guide_cpt_custom", "Help Guide Options", "guide_cpt_custom", "guide", "normal", "low");
		add_meta_box("testimonials_cpt_custom", "Testimonial Options", "testimonials_cpt_custom", "testimonials", "normal", "low");
        add_meta_box("case_study_cpt_meta", "Company Information", "case_study_cpt_meta", "case_study", "side", "low");
        add_meta_box("case_study_cpt_custom", "Case Study", "case_study_cpt_custom", "case_study", "normal", "low");
        add_meta_box("api_doc_cpt_soap", 'SOAP API Documentation', 'api_doc_cpt_service', 'api_doc', 'normal', 'low', array('service'=>'soap'));
        add_meta_box("api_doc_cpt_rest", 'REST API Documentation', 'api_doc_cpt_service', 'api_doc', 'normal', 'low', array('service'=>'rest'));
        
        // Remove the standard page attributes metabox and add our own custom override
        remove_meta_box('pageparentdiv', 'page', 'side');
        add_meta_box("pageparentdiv", 'Page Attributes', "page_attributes_meta_box_custom", "page", "side", "low");
    }
    
    /**
     * Theme Styles
     * Registers and enqueues CSS styles in head.
     *
     * @see add_action('wp_print_styles', 'theme_styles');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function theme_styles()
    {
        wp_register_style('clearbooks2011_reset', get_stylesheet_directory_uri().'/styles/reset.css');
        wp_register_style('clearbooks2011_fonts', get_stylesheet_directory_uri().'/styles/fonts.css');
        wp_register_style('clearbooks2011_template', get_stylesheet_directory_uri().'/styles/template.css');
        wp_register_style('clearbooks2011_style', get_bloginfo('stylesheet_url'));

        wp_enqueue_style('clearbooks2011_reset');
        wp_enqueue_style('clearbooks2011_fonts');
        wp_enqueue_style('clearbooks2011_template');
        wp_enqueue_style('clearbooks2011_style');

    }
    
    /**
     * Theme Scripts
     * Registers and enqueues JS scripts in head.
     *
     * @see add_action('wp_print_styles', 'theme_styles');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function theme_scripts() {
        wp_register_script('jquery-ui', ("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"), array('jquery'), '1.8.16');
        wp_enqueue_script('jquery-ui');

        wp_register_style('jquery-ui-clearbooks', get_bloginfo("template_url").'/styles/jquery-ui/jquery-ui-1.8.10.clearbooks.min.css', false, '1.8.10', 'all');
        wp_enqueue_style('jquery-ui-clearbooks');

        if(!is_admin()){
            //Include the jQuery Hosted library from Google
            wp_deregister_script('jquery'); 
            wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"), false, '1.6.2');
            wp_enqueue_script('jquery');

            // Modernizr
            wp_register_script('modernizr', get_bloginfo("template_url").'/scripts/js/modernizr.min.js', false, false, false);
            wp_enqueue_script('modernizr');
			
            // .cdn suffix is a temporary measure around a CloudFront problem where the files aren't being updated
			wp_register_script('cb_functions', get_bloginfo("template_url").'/scripts/js/functions.cdn.min.js', null);
            wp_enqueue_script('cb_functions');

			wp_register_script('cb_tracking', get_bloginfo("template_url").'/scripts/js/tracking.cdn.min.js', array('cb_functions'), false, true);
            wp_enqueue_script('cb_tracking');
			
            wp_register_script('cb_testimonials', get_bloginfo("template_url").'/scripts/js/testimonials.min.js', array('jquery'));
            wp_enqueue_script('cb_testimonials');

            wp_register_script('slideTo', get_bloginfo("template_url").'/scripts/js/jquery.slideTo.min.js', array('jquery'));
            wp_enqueue_script('slideTo');

            wp_register_script('tools_vat', get_bloginfo("template_url").'/scripts/js/jquery.tools.vat.min.js', array('jquery'), false, true);
            wp_enqueue_script('tools_vat');
            
            wp_register_script('cb_register', get_bloginfo("template_url").'/scripts/js/register.min.js', array('jquery'), false, true);
            wp_enqueue_script('cb_register');
            wp_localize_script( 'cb_register', 'CB_Register', array( 'uri' => get_option('home') . '/register/?signup=1' ) );
            
            if( is_page_template( 'template-landing-page.php' ) ) {
                wp_register_script('cb_landing_featured_check', get_bloginfo("template_url").'/scripts/js/edge/cb_landing_featured/check.min.js', array('modernizr'), false, false);
                wp_enqueue_script('cb_landing_featured_check');
                
                wp_localize_script( 'cb_landing_featured_check', 'Adobe_Edge', array( 'uri' => get_bloginfo("template_url").'/scripts/js/edge/' ) );
            }
        }
    }
    
    /**
     * Save Custom Post Meta
     * Called when saving a post to process custom meta fields defined by all custom post types
     *
     * @see add_action('save_post', 'save_custom_post_meta');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function save_custom_post_meta(){
        global $post;
        $custom_meta_fields = array('video', 'video_service', 'requirements','steps','level','time',
                                    'list_priority', 'sidebar', 'sidebar_position', 'fav_feature', 'customer_type',
                                    'testimonial', 'testi_author', 'testi_company', 'testi_url', 'testi_author_segment', 'testi_gravatar_email', 'include_testimonial_home', 'include_video_home',
                                    'company_name', 'contact_name', 'address', 'website', 'phone',
                                    'email', 'api');
        
        // Return if we're doing an auto save  
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return; 

        // if our nonce isn't there, or we can't verify it exit. 
        if(!isset( $_POST['cb_nonce']) || !wp_verify_nonce($_POST['cb_nonce'], 'cb_validate_nonce')) return; 

        // if our current user can't edit this post exit.  
        if(!current_user_can('edit_post')) return;
        
        foreach($custom_meta_fields as $custom_meta_field) {
            if(isset($_POST[$custom_meta_field])) {
                switch($custom_meta_field) {
                    case 'video':
                        $parts = array();
                        
                        // If the video is from YouTube, just grab the ID.                  
                        if (stristr($_POST[$custom_meta_field], 'youtube.')){
                            $video_service = 'youtube';
                            
                            parse_str(parse_url($_POST[$custom_meta_field], PHP_URL_QUERY), $parts);
                            $_POST[$custom_meta_field] = esc_attr($parts['v']);
                        }
                        else if(stristr($_POST[$custom_meta_field], 'vimeo.')) {
                            $video_service = 'vimeo';
                            
                            preg_match("/[^\/]+$/", $_POST[$custom_meta_field], $parts);
                            $_POST[$custom_meta_field] = esc_attr($parts[0]);
                        }
                        
                        $_POST['video_service'] = esc_attr($video_service); // Set the video service provider so we know what plugin to use to render the video.
                    break;
                    
                    case 'include_testimonial_home' :
                    case 'include_video_home' :
                        if($_POST[$custom_meta_field] != 1) {
                            $_POST[$custom_meta_field] = null;
                        }
                        else {
                            $_POST[$custom_meta_field] = esc_attr($_POST[$custom_meta_field]);
                        }
                    break;
                    default:
                        if(!is_array($_POST[$custom_meta_field])) {
                            $_POST[$custom_meta_field] = esc_attr($_POST[$custom_meta_field]);
                        }
                    break;
                }
                
                // Update the post meta with the cleaned values.
                update_post_meta($post->ID, $custom_meta_field, $_POST[$custom_meta_field]);
            }
        }
    }
    
    /**
     * Tracking
     * Track the entry points in Clear Books and the refers
     *
     * @return void
     * @author Fubra
     **/
    function tracking() {
        if(isset($_GET['track'])&&!isset($_COOKIE['track'])){ // general track code (adwords)
            setcookie("track", $_GET['track'], time()+(60*60*24*30), "/",  ".clearbooks.co.uk".$_SERVER['VIRTUAL_DOMAIN']);   // expire in 1 month
        }

        if((isset($_SERVER['HTTP_REFERER']) && !isset($_COOKIE['cbREFERER']))){
            setcookie("cbREFERER", $_SERVER['HTTP_REFERER'], time()+(60*60*24*30), "/", ".clearbooks.co.uk".$_SERVER['VIRTUAL_DOMAIN']);  // expire in 1 month
        }

        if(isset($_GET['a'])&&!isset($_COOKIE['affiliate'])){ // affiliate tracking
            setcookie("affiliate", $_GET['a'], time()+(60*60*24*30), "/",  ".clearbooks.co.uk".$_SERVER['VIRTUAL_DOMAIN']);  // expire in 1 month
            $path=preg_replace('#\?a=.*#','',$_SERVER['REQUEST_URI']);
            Header( "HTTP/1.1 301 Moved Permanently" );
            Header( "Location: ".$path);
            exit;
        }

    }

    /**
     * Action hook called by the guides sidebar
     * to enqueue the JQuery plugin stuff. Not sure if is best practice :/
     */
    function guides_sidebar()
    {
        wp_enqueue_style('treeview', get_stylesheet_directory_uri().'/styles/jquery.treeview.css');
        wp_enqueue_script('trview', get_bloginfo("template_url").'/scripts/js/jquery.treeview.js', array('jquery'), false, true);
    }

    /**
     * Get Tweets
     * 
     * Returns the latest twitter statuses as an a Array
     *
     * @param $user string The username or userid of the twitter user.
     * @param $show_count integer The number of statuses to retrieve. Default is 1
     * @param $feed string The feed you want to pull, eg: statuses, favorites. Default = statuses. 
     *
     * @return $tweets An a array of tweets
     * @author Matt Fairbrass
     **/
    function get_tweets( $user, $show_count = 1, $feed = 'statuses/user_timeline' )
    {
        // TODO this is a terrible hack, a plugin should be integrated into the theme by a more competent wordpress dev

        $filepath = TEMPLATEPATH . '/tweets/' . preg_replace( '/[^A-Za-z0-9-]/', '_', $feed ) . "-$show_count.xml";
        if ( !is_dir( dirname( $filepath ) ) ) {
            mkdir( dirname( $filepath ) );
        }

        $localXml = @simplexml_load_file( $filepath ) ?: new \SimpleXMLElement( '<feed />' );

        if ( !$localXml->lastUpdate || ( strtotime( $localXml->lastUpdate ) + 3600 ) < time() ) {
            $xml = simplexml_load_file( "http://twitter.com/$feed/$user.xml?count=$show_count" );

            if ( $xml && count( $xml->status ) ) {
                $localXml->statuses = new \SimpleXMLElement( '<statuses />' );
                $localXml->lastUpdate = date( 'Y-m-d H:i:s' );

                foreach ( $xml->status as $status ) {
                    $element = $localXml->statuses->addChild( 'status' );
                    $element->text = $status->text;
                    $element->user = $status->user->screen_name;
                    $element->created_at = $status->created_at;
                }

                file_put_contents( $filepath, $localXml->asXML() );
            }
        }

        $tweets = array();

        foreach ( $localXml->statuses->status as $status ) {
            $formatted_text = format_text_to_twitter( $status->text  );
            $formatted_text = format_text_to_links( $formatted_text );
            $tweets[] = array( 'text' => $formatted_text,
                               'user' => $status->user,
                               'created_at' => $status->created_at );
        }

        return $tweets;
     }
    
    /**
     * Get Random Testimonial
     * Returns a random testimonial from the DB.
     *
     * @return $testimonial object the random testimonial
     * @author Matt Fairbrass
     **/
    function get_random_testimonial() {
        $cbdb = new wpdb(CBDB_USER, CBDB_PSWD, CBDB_NAME, CBDB_HOST);
        $testimonial = $cbdb->get_row('SELECT testimonial_snippet, firm_name , link_name, link_postcode, link_town FROM business_directory WHERE testimonial_snippet <> "" ORDER BY RAND() LIMIT 1');
        
        if(!empty($testimonial)) {
            return $testimonial;
        }
        else {
            return false;
        }
    }
    
    /**
     * Remove Empty Read More Span
     * Removes the empty read more span inserted by wordpress into a post's content when using "more".
     *
     * @return string an empty string
     * @author Matt Fairbrass
     **/
    function remove_empty_read_more_span($content) {
        return eregi_replace("(<p><span id=\"more-[0-9]{1,}\"></span></p>)", "", $content);
    }

    /**
     *
     */
    function subscribe_change_log()
    {
        $result = false;

        // Issue the API request
        try {
            // Instantiate the SOAP client to communicate with the Clear Books API
            $client = new \SoapClient( BASE_SYSTEM_URL.'/api/wsdl/' );

            // Authenticate with the Clear Books API
            $client->__setSoapHeaders(array( new SoapHeader( BASE_SYSTEM_URL . '/api/soap/',
                                                             'authenticate',
                                                             array( 'apiKey' => CBDB_APIKEY ) ) ) );

            $result = $client->mailSubscribe( 1, $_POST['email'] );
        } catch ( Exception $e ) {
        }
    }

    /**
     * Nice Date
     * Convert a given date to (x) minutes/hours/days ago/from now
     *
     * @param $date string the date to convert
     *
     * @return $difference $periods[$j] {$tense}
     * @author Matt Fairbrass
     **/
    function nicedate($date) {
        if(empty($date)) {
            return "No date provided";
        }

        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        $now = time();
        $unix_date = strtotime($date);

        // check validity of date
        if(empty($unix_date)) {   
            return "Bad date";
        }

        // is it future date or past date
        if($now > $unix_date) {   
            $difference = $now - $unix_date;
            $tense = "ago";

        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }

        return "$difference $periods[$j] {$tense}";
    }
    
    /**
     * Syntax Highlight
     * Adds a filter hook to function call for WP-Syntax Geshi code highlighting.
     *
     * @param $code string the code to highlight (wrapped in <pre lang=""></pre> tags)
     *
     * @uses WP-Syntax Plugin
     * 
     * @return void
     * @author Matt Fairbrass
     **/
    function syntax_highlight($code) {
        global $wp_syntax_token, $wp_syntax_matches;
        $code = apply_filters('syntax_highlight', $code);
        
        return $code;
    }
    
    /**
     * Pagination
     * Paginates the loop
     *
     * @param $pages int the max number of posts to a page..
     * @param $range integer The number of pagination numbers to show. Default = 4.
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function pagination($pages = '', $range = 4) {
         $showitems = ($range * 2)+1; 

         global $paged;
         if(empty($paged)) $paged = 1;

         if($pages == '')
         {
             global $wp_query;
             $pages = $wp_query->max_num_pages;
             if(!$pages)
             {
                 $pages = 1;
             }
         }  

         if(1 != $pages)
         {
            echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
                echo '<div class="page_numbers alignright">';
                    if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
                    if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";

                    for ($i=1; $i <= $pages; $i++)
                    {
                        if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                        {
                            echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
                        }
                    }

                    if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
                    if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
                echo '</div>';
             echo "</div>\n";
         }
    }
    
    /**
     * Display Social Share
     * Show the social media sharing buttons
     *
     * @param $twitter boolean show Twitter tweet button?
     * @param $facebook bolean show Facebook like button?
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function get_social_share($class = '') { ?>
        <div class="social_share <?=$class?>">
            <?php if(function_exists('rp_add_social_share')) rp_add_social_share(); ?>
        </div>
    <?php 
    }
    
    /**
     * Get Pricing Grid
     * Outputs the pricing grid to the page
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function get_pricing_grid() {
    ?>
        <ol class="pricing">
            <li>
                <h2>Premium</h2>
                <p>£15 per month<sup>*</sup></p>
                <h3>Heavy use</h3>
                
                <ul>
                    <li><strong>Unlimited</strong> transactions</li>
                    <li><strong>Unlimited</strong> users</li>
                    <li><strong>Free</strong> updates</li>
                    <li><strong>VAT</strong> reporting</li>
                    <li><a class="button green" onclick="javascript: pageTracker._trackPageview('/register/plan_premium/');" href="https://secure.clearbooks.co.uk/static/register/?i=2"><span>Sign Up</span></a></li>
                </ul>
            </li>
            <li class="recommended">
                <h2>Established</h2>
                <p>£10 per month<sup>*</sup></p>
                <h3>Medium use</h3>
                
                <ul>
                    <li><strong>200</strong> transactions</li>
                    <li><strong>Unlimited</strong> users</li>
                    <li><strong>Free</strong> updates</li>
                    <li><strong>VAT</strong> reporting</li>
                    <li class="note">Recommended Package</li>
                    <li><a class="button green" onclick="javascript: pageTracker._trackPageview('/register/plan_established/');" href="https://secure.clearbooks.co.uk/static/register/?i=3"><span>Sign Up</span></a></li>
                </ul>
            </li>
            <li>
                <h2>Growth</h2>
                <p>£5 per month<sup>*</sup></p>
                <h3>Light use</h3>
                
                <ul>
                    <li><strong>80</strong> transactions</li>
                    <li><strong>Unlimited</strong> users</li>
                    <li><strong>Free</strong> updates</li>
                    <li><strong>No VAT reporting</strong></li>
                    <li><a class="button green" onclick="javascript: pageTracker._trackPageview('/register/plan_growth/');" href="https://secure.clearbooks.co.uk/static/register/?i=4"><span>Sign Up</span></a></li>
                </ul>
            </li>
        </ol>
        
        <script type="text/javascript">
            $(document).ready(function(){
                $('ol.pricing > li').mouseenter(function(e) {
                    $(this).filter(':not(.recommended)')
                        .stop(true, true)
                        .addClass('recommended')
                        .hide()
                        .fadeIn('fast')
                        .siblings()
                        .removeClass('recommended');
                });
            });
        </script>
        
        <div class="clear"><small class="alignright"><strong>* All prices exclude VAT</strong></small></div>
    <?php
    }
    
    global $menu_is_extended;
    $menu_is_extended = false; //Hack: To prevent extend_nav_menu_items being run at evey depth level.
    
    /**
     * Extend Nav Menu Items
     * Adds additional menu items to wordpress menus
     * 
     * @param $items string the menu items as created in the wordpress admin panel
     * @param $args object arguments passed during the callback
     * 
     * @see add_filter('wp_nav_menu_items', 'extend_nav_menu_items');
     * @return $items string the complete set of menu items
     * @author Matt Fairbrass
     **/
    function extend_nav_menu_items($items, $args) {
        global $menu_is_extended;
        
        if($args->menu == 'secondary_nav' && $menu_is_extended === false) {
            // Get the search form from the object buffer
            ob_start();
            get_search_form(false);
            $search_form = ob_get_contents();
            ob_end_clean();
            
            $search_input = '<li>'.$search_form.'</li>';
            $login_button = '<li><a href="'.LOGIN_URL.'" onclick="recordOutboundLink(this, \'Outbound Links\', \'Login Header\');return false;" class="button white"><span>Login<img src="'.get_bloginfo('template_url').'/images/icons/fubra_passport.png" class="right" width="17" height="18" /></span></a></li>';
            
            $items = $search_input . $items . $login_button;
            $menu_is_extended = true;
        }
        return $items;
    }
    
    /**
     * Extend Nav Menu Arguments
     * Adds additional arguments to the wp_nav_menu.
     *
     * @param $args array an array of args.
     *
     * @see add_filter('wp_nav_menu_args', 'extend_nav_menu_args'); 
     *
     * @return $args array the array of args.
     * @author Matt Fairbrass
     **/
    function extend_nav_menu_args($args = array()){
        $args['start_depth'];
        return $args;
    }
    
    /**
     * Guide Permalink
     * Replaces the %topic% taxonomy wild card with the given guide's taxonomy.
     * 
     * @param $permalink string the permalink for the current page/post.
     * @param $post_id int the post id
     *
     * @see custom_rewrite_rules()
     *
     * @return string the formatted string
     * @author Matt Fairbrass
     **/
    function guide_permalink($permalink, $post_id, $leavename) {
        if (strpos($permalink, '%topic%') === FALSE) return $permalink;

            // Get post
            $post = get_post($post_id);
            if (!$post) return $permalink;

            // Get taxonomy terms
            $terms = wp_get_object_terms($post->ID, 'support'); 
            if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
            else $taxonomy_slug = 'uncategorised';

        return str_replace('%topic%', $taxonomy_slug, $permalink);
    }
    
    /**
     * API Doc Permalink
     * Replaces the %service% taxonomy wild card with the given api_doc's taxonomy.
     * 
     * @param $permalink string the permalink for the current page/post.
     * @param $post_id int the post id
     *
     * @see custom_rewrite_rules()
     *
     * @return string the formatted string
     * @author Matt Fairbrass
     **/
    function api_doc_permalink($permalink, $post_id, $leavename) {
        if (strpos($permalink, '%service%') === FALSE) return $permalink;

            // Get post
            $post = get_post($post_id);
            if (!$post) return $permalink;

            // Get taxonomy terms
            $terms = wp_get_object_terms($post->ID, 'service'); 
            if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
            else $taxonomy_slug = 'uncategorised';

        return str_replace('%service%', $taxonomy_slug, $permalink);
    }
    
    /**
     * Custom Query Variables
     * Pushes custom variables into the query_vars array.
     *
     * @param $public_query_vars array the public_query_vars array
     *
     * @return $public_query_vars array the query_vars array
     * @author Matt Fairbrass
     **/
    function custom_query_vars($public_query_vars) {
        array_push($public_query_vars, 'business', 'town', 'postcode', 'industry', 'version');
        return $public_query_vars;
    }
    
    /**
     * Custom Rewrite Rules
     * Creates custom permalink rewrite rules for custom post types so that we can have the format: {page}/{taxonomy}/{post}/
     *
     * @see add_action( 'init', 'custom_rewrite_rules' );
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function custom_rewrite_rules() {
        add_filter('post_link', 'guide_permalink', 10, 3);
        add_filter('post_type_link', 'guide_permalink', 10, 3);
        add_rewrite_tag('%topic%','([^&]+)');
        
        add_filter('post_link', 'api_doc_permalink', 10, 3);
        add_filter('post_type_link', 'api_doc_permalink', 10, 3);
        add_rewrite_tag('%service%','([^&]+)');

        add_rewrite_tag('%post_type%', '()[^&]+)');
        
        // Support Guides
        add_rewrite_rule('^support/guides(/page/?([0-9]{1,}))?/?$','index.php?post_type=guide&page=$matches[2]','top'); 
        add_rewrite_rule('^support/guides/([^/]+)(/page/?([0-9]{1,}))?/?$','index.php?support=$matches[1]&post_type=guide&page=$matches[3]','top');
        add_rewrite_rule('^support/guides/([^/]+)/([^/]+)/?$','index.php?support=$matches[1]&post_type=guide&guide=$matches[2]','top');
        
        // API Docs 
        add_rewrite_rule('^support/api/docs/([^/]+)(/page/?([0-9]{1,}))?/?$','index.php?page_id=9589&service=$matches[1]&page=$matches[3]','top');

        // Change Log
        add_rewrite_rule( '^about/change-log/page/([0-9]+)/?$', 'index.php?post_type=change&page=$matches[1]', 'top' );

        // Business Directory
        add_rewrite_rule('^partners/directory/business/([\w-]+)-([\w\s]+)/(.*)/?', 'index.php?page_id=1664&town=$matches[1]&postcode=$matches[2]&business=$matches[3]', 'top');
        add_rewrite_rule('^partners/directory/([\w-]+)-([\w\s]+)/(.*)/?', 'index.php?page_id=1649&town=$matches[1]&postcode=$matches[2]&business=$matches[3]', 'top');
    }
    
    /**
     * Custom Post Types Icons
     * Outputs CSS to style custom post type icons in the Wordpress Admin menu.
     *
     * @see add_action('admin_head', 'cpt_icons')
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function cpt_icons() { ?>
        <style type="text/css" media="screen">
            #menu-posts-guide .wp-menu-image {
                    background: url(<?php bloginfo('template_url') ?>/images/icons/cpt/help_guides.png) no-repeat 6px -17px !important;
            }
            #menu-posts-guide.wp-has-current-submenu .wp-menu-image, 
            #menu-posts-guide:hover .wp-menu-image, #menu-posts-POSTTYPE.wp-has-current-submenu .wp-menu-image {
                    background-position:6px 7px!important;
            }
            
            #menu-posts-case_study .wp-menu-image {
                    background: url(<?php bloginfo('template_url') ?>/images/icons/cpt/case_studies.png) no-repeat 6px -17px !important;
            }
            #menu-posts-case_study.wp-has-current-submenu .wp-menu-image, 
            #menu-posts-case_study:hover .wp-menu-image, #menu-posts-POSTTYPE.wp-has-current-submenu .wp-menu-image {
                    background-position:6px 7px!important;
            }
        </style>
    <?php }
    
    
    /**
     * Get Taxonomy Links
     * 
     * Returns an unordered list of links for a given taxonomy
     *
     * @param $taxonomy string The name of the registered taxonomy to query
     * @param $hide_empty integer hide taxonomies which have no posts associated with them. Default = 0.
     * @param $show_count boolean show in brackets the number of posts in a given taxonomy
     *
     * @return $links_list The html output of the unordered list.
     * @author Matt Fairbrass
     **/
    function get_taxonomy_links($taxonomy, $hide_empty = 0, $show_count = false) {
        global $post; global $wp_query;
        $post_obj = $wp_query->get_queried_object();
        
        $args = array(
            'hide_empty' => $hide_empty
        );
        
        $links = get_terms($taxonomy, $args);
        $links_list = null;
        
        // Get the parent of the page depending on which child level we are on
        $ancestors = get_post_ancestors($post);
        $selected = (empty($post->post_parent)) ? $post->post_name : end($ancestors);
        
        if(!empty($links)) {
            $links_list = '<ul class="sidebar_nav menu">';
                foreach($links as $link) {
                    $links_list .= ($post_obj->term_id == $link->term_id) ? '<li class="selected">' : '<li>';
                        $links_list .= '<a href="'.get_term_link($link).'">';
                            $links_list .= $link->name;
                            $links_list .= ($show_count == true) ? ' ('.$link->count.')' : null;
                        $links_list .= '</a>';
                    $links_list .= '</li>';
                }
            $links_list .= '</ul>';
        }

        return $links_list;
    }


    /**
     * Convert a multidimensional array to a nested list.
     * This is purely for the JQuery Tree thing for support
     * @param array $array The array to convert, obv
     * @param array $keymaps to map array keys to text
     * @param string $id the ID of the top level list
     * @return string the HTML
     */
    function array_to_list($array, $keymaps, $id='tax_tree')
    {
        if (isset($id)) {
            $html = '<ul id="'.$id.'" class="filetree">';
        } else {
            $html = '<ul>';
        }

        foreach ($array as $id=>$value) {
            if (is_array($value)) {
                $html .= '<li><span class="folder">'.$keymaps[$id].'</span>'.array_to_list($value,$keymaps,
                    null).'</li>';
            } else {
                $html .= '<li><span class="file">'.$value.'</span></li>';
            }
        }
        return $html . '</ul>';
    }

    /**
     * Get a nested taxonomy tree
     * @param string $taxonomy The taxonomy
     * @return string The tree
     */
    function get_taxonomy_tree_better($taxonomy)
    {
        $names = array();
        $termFlat = array(); //flattish id=>link map for simple population
        $termTree = array(); //nice heirarchical tree for pretty printing.
        foreach (get_categories(array('taxonomy'=>$taxonomy, 'hierarchical'=>true)) as $term)
        {
            //init an entry in flat lookup array
            if (!isset($termFlat[$term->term_id])) {
                $termFlat[$term->term_id] = array();
            }

            //save a mapping of term IDs => names
            $names[$term->term_id] = $term->name;

            if (!empty($term->category_parent)) { //if we have a parent, then add us to our parent's children
                $termFlat[$term->category_parent][$term->term_id] = &$termFlat[$term->term_id];
            } else {
                //no parents? This is a root node so add it to the tree
                $termTree[$term->term_id] = &$termFlat[$term->term_id];
            }
        }

        //query args
        $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'operator' => 'IN',
                    'field' => 'ids',
                    'terms' => array_keys($names)
                )
            ),
            'orderby'=>'meta_value_num title',
            'meta_key'=>'list_priority',
            'nopaging'=>1,
        );

        //run query, assign posts to tree
        $termQuery = new WP_Query( $args );
        while ( $termQuery->have_posts() ) {
             $termQuery->the_post();
             foreach (get_the_terms(get_the_ID(),$taxonomy) as $term) {
                 $termFlat[$term->term_id][] = '<a href="'.get_permalink().'">'.esc_html(get_the_title()).'</a>';
             }
        }

        //now pass the tree to our pretty-print function
        return array_to_list(array_filter($termTree),$names);
    }

    /**
     * Should we show the new help?
     * @return bool
     */
    function show_new_help()
    {
        return isset($_GET['tom']) && $_GET['tom'] == 'inc' && current_user_can('manage_options');
    }

    /**
     * Get Post Meta List
     * Output a list of post meta data in an unordered list
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function get_post_meta_list() {
        global $post; global $wp_query;
        
        // Check the type of post
        switch(get_post_type($post)) {
            case 'guide':
                $cats = get_the_terms($post->ID, 'support');
                $customs = get_post_custom($post->ID);
                $comments = false;
            break;
            case 'case_study':
                $cats = get_the_terms($post->ID, 'type');
                $customs = get_post_custom($post->ID);
                $comments = false;
            break;
            case 'change':
                $cats = get_the_terms( $post->ID, 'about' );
                $comments = false;
                break;
            case 'post':
            case 'page':
                $cats = get_the_category($post->ID);
                $tags = get_the_tags($post->ID);
                $comments = comments_open($post->ID);
            break;
        }
    ?>  
        <ul class="post_meta">  
            <li><div class="icon mono-16 contactcard"></div><a href="<?=get_bloginfo('url')?>/author/<?=get_the_author_meta('nicename')?>/"><?php the_author();?></a></li>
            <li><div class="icon mono-16 clock"></div><?=the_time('l, j F Y')?></a>
            <?php if($comments == true):?>
                <li><div class="icon mono-16 commentblack"></div><a href="<?php the_permalink() ?>#comments"><?php comments_number('0 comments', '1 comment', '% comments'); ?></a></li>
            <?php endif;?>
            <?php if(isset($cats) && !empty($cats)) :?>
                <li>
                    <div class="icon mono-16 folder"></div>
                    <?php foreach($cats as $cat) :?>
                        <a href="<?php echo get_term_link($cat);?>" style="margin-right: 10px;"><?=$cat->name?></a>
                    <?php endforeach;?>
                </li>
            <?php endif;?>
            <?php if(isset($tags) && !empty($tags)) :?>
                <li>
                    <div class="icon mono-16 tag"></div>
                    <?php foreach($tags as $tag) :?>
                        <a href="#" style="margin-right: 10px;"><?=strtolower($tag->name)?></a>
                    <?php endforeach;?>
                </li>
            <?php endif;?>
        </ul>
    <?php   
    }
    
    /* Custom Post Types */

    /**
     * Testimonials Custom Post Type
     * Registers a custom post type for the Clear Books help guides and a topics taxonomy
     *
     * @see add_action('init', 'testimonials_cpt');
     *
     * @return void
     * @author David Heward
     **/
    function testimonials_cpt() {      
        $labels = array(
            'name' => _x('Testimonials', 'post type general name'),
            'singular_name' => _x('Testimonials', 'post type singular name'),
            'add_new' => _x('Add New', 'project item'),
            'add_new_item' => __('Add Testimonial'),
            'edit_item' => __('Edit Testimonial'),
            'new_item' => __('New Testimonial'),
            'view_item' => __('View Testimonial'),
            'search_items' => __('Search Testimonials'),
            'not_found' =>  __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'has_archive' => 'guide', // Activate the archive
            'rewrite' => array('slug'=>'testimonials', 'hierarchical'=>false, 'with_front'=>false),
            '_builtin' => false, // It's a custom post type, not built in!
            'capability_type' => 'post',
            'query_var' => "guide", // This goes to the WP_Query schema
            'menu_position' => null,
            'supports' => array('title','editor','thumbnail', 'author', 'excerpt')
        );
        
        register_post_type('testimonials' , $args);

    }

    /**
     * Case Study Custom Data fields
     * Creates custom fields for the case_study post type in the center panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function testimonials_cpt_custom() {
        global $post;
 		$custom = get_post_custom($post->ID);
		$testi_author = $custom["testi_author"][0];
		$testi_company = $custom["testi_company"][0];
		$testi_url = $custom["testi_url"][0];
		$testi_segment = $custom["testi_author_segment"][0];
		$testi_gravatar_email = $custom["testi_gravatar_email"][0];
		
		wp_nonce_field('cb_validate_nonce', 'cb_nonce'); ?>
        
		<ul>
            <li class="alignleft" style="margin-right: 20px;">
                <label>Author:</label><br />
                <input type="text" name="testi_author" value="<?=$testi_author?>">
            </li>
            
            <li class="alignleft" style="margin-right: 20px;">
                <label>Company:</label><br />
                <input type="text" name="testi_company" value="<?=$testi_company?>">
            </li>

            <li class="alignleft" style="margin-right: 20px;">
                <label>Cite URL:</label><br />
                <input type="text" name="testi_url" value="<?=$testi_url?>">
            </li>   
            <li class="alignleft" style="margin-right: 20px;">
                <label>Segment:</label><br />
                <input type="text" name="testi_author_segment" value="<?=$testi_segment?>">
            </li>
            <li class="alignleft" style="margin-right: 20px;">
                <label>Gravatar email:</label><br />
                <input type="text" name="testi_gravatar_email" value="<?=$testi_gravatar_email?>">
            </li>
 
        </ul>
        <div class="clear"></div>
		
		<?php
    }


    /**
     * Help Guides Custom Post Type
     * Registers a custom post type for the Clear Books help guides and a topics taxonomy
     *
     * @see add_action('init', 'guide_cpt');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function guide_cpt() {      
        $labels = array(
            'name' => _x('Help Guides', 'post type general name'),
            'singular_name' => _x('Help Guide', 'post type singular name'),
            'add_new' => _x('Add New', 'project item'),
            'add_new_item' => __('Add New Guide'),
            'edit_item' => __('Edit Guide'),
            'new_item' => __('New Guide'),
            'view_item' => __('View Guide'),
            'search_items' => __('Search Help Guides'),
            'not_found' =>  __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'has_archive' => 'guide', // Activate the archive
            'rewrite' => array('slug'=>'support/guides/%topic%', 'hierarchical'=>false, 'with_front'=>false),
            '_builtin' => false, // It's a custom post type, not built in!
            'capability_type' => 'post',
            'query_var' => "guide", // This goes to the WP_Query schema
            'menu_position' => null,
            'supports' => array('title','editor','thumbnail', 'author', 'excerpt')
        );
        
        register_post_type('guide' , $args);

        // Register Topic Taxonomy
        register_taxonomy('support', array('guide'), array(
            'public' => true,
            'hierarchical' => true,
            'label' => 'Topics',
            'rewrite' => array('slug'=>'support/guides', 'hierarchical'=>true, 'with_front'=>false),
            'singular_label' => 'Topic',
            'query_var' => true
            )
        );
    }
    
    /**
     * Help Guide Custom Data fields
     * Creates custom fields for the guide post type in the center panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function guide_cpt_custom() {
        global $post;

        $custom = get_post_custom($post->ID);
        $video = $custom["video"][0];
        $requirements = $custom["requirements"][0];
        $steps = $custom["steps"][0];
        
        // nonce hidden field used later on when saving to validate the request  
        wp_nonce_field('cb_validate_nonce', 'cb_nonce');
    ?>
        <ul>
            <li class="alignleft" style="margin-right: 20px;">
                <label>Clear Books Requirements:</label><br />
                <textarea cols="70" rows="8" name="requirements"><?=$requirements?></textarea>
            </li>
            
            <li class="alignleft" style="clear: left; margin-right: 20px;">
                <label>Summary of Steps:</label><br />
                <textarea cols="70" rows="8" name="steps"><?=$steps?></textarea>
            </li>
    
            <li>
                <label>Video URL:</label><br />
                <?php
                    switch($custom['video_service'][0]) {
                        case 'youtube':
                            $service_url = 'http://www.youtube.com/watch?v=';
                        break;
                        
                        case 'vimeo':
                            $service_url = 'http://www.vimeo.com/';
                        break;
                    }
                ?>
                <input type="text" name="video" value="<?php echo $service_url.$video; ?>" style="width: 360px;" />
            </li>
        </ul>
        <div class="clear"></div>
    <?php
    }
    
    /**
     * Help Guide Custom Meta fields
     * Creates custom fields for the guide post type in the side panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function guide_cpt_meta(){
        global $post;
        $custom = get_post_custom($post->ID);
        $priority = $custom["list_priority"][0];
        $level = $custom["level"][0];
        $time = $custom["time"][0];
        
        $difficulty_levels = array(
            0 => 'Beginner',
            1 => 'Intermediate',
            2 => 'Advanced'
        );
    ?>
        <p>
            <label>Difficulty:</label><br />
            <select name="level">
                    <option value=""></option>
                <?php foreach($difficulty_levels as $difficulty) :?>
                    <option value="<?=$difficulty?>" <?php if($level == $difficulty) {echo 'selected';} ?>><?=$difficulty?></option>
                <?php endforeach;?>
            </select>
        </p>
        <p>
            <label>Estimated Completion Time:</label><br />
            <input type="text" name="time" value="<?php echo $time; ?>" /> minutes
        </p>
        <p>
            <label>Topic List Position:</label><br />
            <input type="number" name="list_priority" value="<?php echo $priority; ?>" />
        </p>
    <?php
    }
    
    /**
     * Case Study Custom Post Type
     * Registers a custom post type for the Clear Books case studies
     *
     * @see add_action('init', 'case_study_cpt');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function case_study_cpt() {     
        $labels = array(
            'name' => _x('Case Studies', 'post type general name'),
            'singular_name' => _x('Case Study', 'post type singular name'),
            'add_new' => _x('Add New', 'case study'),
            'add_new_item' => __('Add New Case Study'),
            'edit_item' => __('Edit Case Study'),
            'new_item' => __('New Case Study'),
            'view_item' => __('View Case Study'),
            'search_items' => __('Search Case Studies'),
            'not_found' =>  __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'has_archive' => false, // Activate the archive
            'rewrite' => array('slug'=>'case-studies', 'hierarchical'=>false, 'with_front'=>false),
            '_builtin' => false, // It's a custom post type, not built in!
            'capability_type' => 'post',
            'query_var' => "case_study", // This goes to the WP_Query schema
            'menu_position' => null,
            'supports' => array('title','editor','thumbnail', 'author', 'excerpt')
        );
        
        register_post_type('case_study' , $args);
        
        // Register Type Taxonomy
        register_taxonomy('type', array('case_study'), array(
            'public' => true,
            'hierarchical' => true,
            'label' => 'Types',
            'rewrite' => false,
            'singular_label' => 'Type',
            'query_var' => true
            )
        );
    }
    
    /**
     * Case Study Custom Data fields
     * Creates custom fields for the case_study post type in the center panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function case_study_cpt_custom() {
        global $post;

        $custom = get_post_custom($post->ID);
        $video = $custom["video"][0];
        $fav_feature = $custom["fav_feature"][0];
        $customer_type = $custom["customer_type"][0];
        $testimonial = $custom["testimonial"][0];
        $include_testimonial_home = $custom["include_testimonial_home"][0];
        $include_video_home = $custom["include_video_home"][0];
        
        // nonce hidden field used later on when saving to validate the request  
        wp_nonce_field('cb_validate_nonce', 'cb_nonce');
    ?>
        <ul style="margin-top: 15px;">
            <li class="alignleft" style="margin-right: 20px;">
                <label for="testimonial"><strong>Testimonial:</strong></label><br />
                <textarea cols="70" rows="10" name="testimonial" id="testimonial"><?=$testimonial?></textarea>
            </li>
    
            <li>
                <label for="video"><strong>Video URL:</strong></label><br />
                <?php
                    switch($custom['video_service'][0]) {
                        case 'youtube':
                            $service_url = 'http://www.youtube.com/watch?v=';
                        break;
                        
                        case 'vimeo':
                            $service_url = 'http://www.vimeo.com/';
                        break;
                    }
                ?>
                <input type="text" name="video" id="video" value="<?php echo $service_url.$video; ?>" style="width: 360px;" />
            </li>
            
            <li>
                <label for="fav_feature"><strong>Favourite Clear Books Feature:</strong></label><br />
                <input type="text" name="fav_feature" id="fav_feature" value="<?php echo $fav_feature; ?>" style="width: 360px;" />
            </li>
            
            <li>
                <label for="customer_type"><strong>Customer Type:</strong></label><br />
                <input type="text" name="customer_type" id="customer_type" value="<?php echo $customer_type; ?>" style="width: 360px;" />
            </li>
            
            <li>
                <p><strong>Options</strong></p>
                <input type="checkbox" name="include_testimonial_home" id="include_testimonial_home" value="1" <?php if($include_testimonial_home == 1) { echo 'checked'; } ?> />
                <label for="include_testimonial_home">Include the testimonial on the home page.</label>
            </li>
            
            <li>
                <input type="checkbox" name="include_video_home" id="include_video_home" value="1" <?php if($include_video_home == 1) { echo 'checked'; } ?> />
                <label for="include_video_home">Include the video on the home page.</label>
            </li>
        </ul>
        <div class="clear"></div>
    <?php
    }
    
    /**
     * Case Study Custom Meta fields
     * Creates custom fields for the case_study post type in the side panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function case_study_cpt_meta(){
        global $post;
        $custom = get_post_custom($post->ID);
        
        $company_name = $custom["company_name"][0];
        $contact_name = $custom["contact_name"][0];
        $address = $custom["address"][0];
        $website = $custom["website"][0];
        $phone = $custom["phone"][0];
        $email = $custom["email"][0];
        
        if(empty($company_name)) {
            $style = 'style="display: none;"';
        }
        
        // Creates a connection to the Clear Books database
        $cbdb = new wpdb(CBDB_USER, CBDB_PSWD, CBDB_NAME, CBDB_HOST);
        
        $companies = $cbdb->get_results($cbdb->prepare("SELECT full_name FROM groups WHERE date_paid IS NOT NULL ORDER BY full_name ASC"));
    ?>
        <p>
            <label for="company_name"><strong>Company:</strong></label><br />
            <select name="company_name" id="company_name" style="width: 255px;">
                <option value="">-- Select Company --</option>
                <?php foreach($companies as $key=>$company) :?>
                    <?php if(!empty($company->full_name)) :?>
                        <option value="<?=$company->full_name?>" <?php if($company_name == $company->full_name) { echo 'selected';} ?>><?=$company->full_name?></option>
                    <?php endif;?>
                <?php endforeach;?>
            </select>
        </p>
        
        <div id="company_info" <?=$style?>>
            <p>
                <label for="contact_name"><strong>Contact Name:</strong></label><br />
                <input type="text" name="contact_name" id="contact_name" style="width: 255px;" value="<?=$contact_name?>" />
            </p>
            <p>
                <label for="address"><strong>Address:</strong></label><br />
                <textarea name="address" id="address" style="width: 255px;" rows="5"><?=$address?></textarea>
            </p>
            <p>
                <label for="website"><strong>Website:</strong></label><br />
                <input type="text" name="website" id="website" style="width: 255px;" value="<?=$website?>" />
            </p>
            <p>
                <label for="phone"><strong>Tel:</strong></label><br />
                <input type="text" name="phone" id="phone" style="width: 255px;" value="<?=$phone?>" />
            </p>
            <p>
                <label for="email"><strong>E-mail:</strong></label><br />
                <input type="text" name="email" id="email" style="width: 255px;" value="<?=$email?>" />
            </p>
        </div>
    <?php
    }
    
    /**
     * Clear Books API Documentation Custom Post Type
     * Registers a custom post type for the Clear Books API Documentation
     *
     * @see add_action('init', 'api_doc_cpt');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function api_doc_cpt() {        
        $labels = array(
            'name' => _x('API Docs', 'post type general name'),
            'singular_name' => _x('Clear Books API', 'post type singular name'),
            'add_new' => _x('Add New', 'api doc'),
            'add_new_item' => __('Add New API Doc'),
            'edit_item' => __('Edit API Doc'),
            'new_item' => __('New API Doc'),
            'view_item' => __('View API Doc'),
            'search_items' => __('Search Clear Books API'),
            'not_found' =>  __('Nothing found'),
            'not_found_in_trash' => __('Nothing found in Trash'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'has_archive' => false, // Activate the archive
            'rewrite' => array('slug'=>'support/api/docs/%service%', 'hierarchical'=>false, 'with_front'=>false),
            '_builtin' => false, // It's a custom post type, not built in!
            'capability_type' => 'post',
            'query_var' => "api_doc", // This goes to the WP_Query schema
            'menu_position' => null,
            'supports' => array('title','editor','thumbnail', 'author', 'excerpt')
        );
        
        register_post_type('api_doc' , $args);
        
        // Register Type Taxonomy
        register_taxonomy('service', array('api_doc'), array(
            'public' => true,
            'hierarchical' => true,
            'label' => 'Services',
            'rewrite' => false,
            'singular_label' => 'Service',
            'query_var' => true
            )
        );
    }
    
    /**
     * Clear Books API Documentation Custom Data fields
     * Creates custom fields for the api_doc post type in the center panel.
     *
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function api_doc_cpt_service($post, $callback_args) {
        global $post;

        $custom = get_post_custom($post->ID);
        $api = unserialize($custom["api"][0]);

        $param_types = array(
            'string' => 'String',
            'integer' => 'Integer',
            'boolean' => 'Boolean',
            'float' => 'Float',
            'array' => 'Array'
        );
        
        // nonce hidden field used later on when saving to validate the request  
        wp_nonce_field('cb_validate_nonce', 'cb_nonce');
    ?>
        <ul style="margin-top: 15px;">
            <li>
                <p>
                    <label for="api[<?=$callback_args['args']['service']?>][method][name]"><strong>Method: </strong>
                    <input type="text" name="api[<?=$callback_args['args']['service']?>][method][name]" value="<?=$api[$callback_args['args']['service']]['method']['name']?>" />
                    
                    <label for="api[<?=$callback_args['args']['service']?>][version]"><strong>API Version: </strong>
                    <input type="text" name="api[<?=$callback_args['args']['service']?>][version]" value="<?=$api[$callback_args['args']['service']]['version']?>" />
                </p>

                <p>
                    <input type="checkbox" name="api[<?=$callback_args['args']['service']?>][method][deprecated][value]" value="1" <?php if($api[$callback_args['args']['service']]['method']['deprecated']['value'] == 1) { echo 'checked';} ?> />
                    <label for="api[<?=$callback_args['args']['service']?>][method][deprecated][value]"><strong>Deprecated</strong></label>
                    
                    <?php
                        if(empty($api[$callback_args['args']['service']]['method']['deprecated']['value'])) {
                            $style = 'display: none;';
                        }
                    ?>
                        
                    <span class="deprecated_version" style="<?=$style?>">
                        <label for="api[<?=$callback_args['args']['service']?>][method][deprecated][version]"><strong> since API version: </strong></label>
                        <input type="text" name="api[<?=$callback_args['args']['service']?>][method][deprecated][version]" value="<?=$api[$callback_args['args']['service']]['method']['deprecated']['version']?>" />
                    </span>
                </p>
                <br/>
            </li>
            
            <li>
                <h4>API Request</h4>
                <?php $alt = 0; // Alternate row striping?>
                <table class="wp-list-table widefat params" id="<?=$callback_args['args']['service']?>_api_request_params">
                    <thead>
                        <tr>
                            <th class="manage-column column-cb check-column" scope="col">
                                <input type="checkbox" name="check-col" class="check_all" />
                            </th>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th style="width: 80px; text-align: center;">Required?</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <a href="#" class="remove_param"><strong>- Remove Checked</strong></a>
                            </td>
                            <td colspan="3" style="text-align: right;">
                                <a href="#" class="add_param"><strong>+ Add Param</strong></a>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if(!empty($api[$callback_args['args']['service']]['request']['params'])) :?>
                            <?php foreach($api[$callback_args['args']['service']]['request']['params'] as $id=>$param) :?>
                                <tr class="param <?=($alt++ %2 == 1) ? 'alt' : ''?>">
                                    <th class="check-column">
                                        <input type="checkbox" name="check-row" />
                                    </th>
                                    <td>
                                        <input type="text" name="api[<?=$callback_args['args']['service']?>][request][params][<?=$id?>][field]" value="<?=$param['field']?>" style="width: 100%;" />
                                    </td>
                                    <td>
                                        <select name="api[<?=$callback_args['args']['service']?>][request][params][<?=$id?>][type]" style="width: 100%;">
                                            <?php foreach($param_types as $key=>$value) :?>
                                                <option value="<?=$key?>" <?php if($key == $param['type']) { echo 'selected'; } ?>><?=$value?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="api[<?=$callback_args['args']['service']?>][request][params][<?=$id?>][description]" rows="5" style="width: 100%;"><?=$param['description']?></textarea>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="api[<?=$callback_args['args']['service']?>][request][params][<?=$id?>][required]" value="1" <?php if($param['required'] == 1) { echo 'checked';}?> />
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php else :?>
                            <tr class="param alt">
                                <th class="check-column">
                                    <input type="checkbox" />
                                </th>
                                <td>
                                    <input type="text" name="api[<?=$callback_args['args']['service']?>][request][params][0][field]" value="" style="width: 100%;" />
                                </td>
                                <td>
                                    <select name="api[<?=$callback_args['args']['service']?>][request][params][0][type]" style="width: 100%;">
                                        <?php foreach($param_types as $key=>$value) :?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="api[<?=$callback_args['args']['service']?>][request][params][0][description]" rows="5" style="width: 100%;"></textarea>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="api[<?=$callback_args['args']['service']?>][request][params][0][required]" value="1" />
                                </td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </li>
            
            <li>
                <h4><label for"api[<?=$callback_args['args']['service']?>][example][request]">Example Request</labeL></h4>
                <textarea name="api[<?=$callback_args['args']['service']?>][example][request]" rows="8" style="width: 100%;"><?=$api[$callback_args['args']['service']]['example']['request']?></textarea>
            </li>
            
            <li>
                <h4>API Response</h4>   
                <?php $alt = 0; // Alternate row striping?>
                <table class="wp-list-table widefat params" id="<?=$callback_args['args']['service']?>_api_response_params">
                    <thead>
                        <tr>
                            <th class="manage-column column-cb check-column" scope="col">
                                <input type="checkbox" name="check-col" class="check_all" />
                            </th>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th style="width: 80px; text-align: center;">Required?</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <a href="#" class="remove_param"><strong>- Remove Checked</strong></a>
                            </td>
                            <td colspan="3" style="text-align: right;">
                                <a href="#" class="add_param"><strong>+ Add Param</strong></a>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if(!empty($api[$callback_args['args']['service']]['response']['params'])) :?>
                            <?php foreach($api[$callback_args['args']['service']]['response']['params'] as $id=>$param) :?>
                                <tr class="param <?=($alt++ %2 == 1) ? 'alt' : ''?>">
                                    <th class="check-column">
                                        <input type="checkbox" name="check-row" />
                                    </th>
                                    <td>
                                        <input type="text" name="api[<?=$callback_args['args']['service']?>][response][params][<?=$id?>][field]" value="<?=$param['field']?>" style="width: 100%;" />
                                    </td>
                                    <td>
                                        <select name="api[<?=$callback_args['args']['service']?>][response][params][<?=$id?>][type]" style="width: 100%;">
                                            <?php foreach($param_types as $key=>$value) :?>
                                                <option value="<?=$key?>" <?php if($key == $param['type']) { echo 'selected'; } ?>><?=$value?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="api[<?=$callback_args['args']['service']?>][response][params][<?=$id?>][description]" rows="5" style="width: 100%;"><?=$param['description']?></textarea>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="api[<?=$callback_args['args']['service']?>][response][params][<?=$id?>][required]" value="1" <?php if($param['required'] == 1) { echo 'checked';}?> />
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php else :?>
                            <tr class="param alt">
                                <th class="check-column">
                                    <input type="checkbox" />
                                </th>
                                <td>
                                    <input type="text" name="api[<?=$callback_args['args']['service']?>][response][params][0][field]" value="" style="width: 100%;" />
                                </td>
                                <td>
                                    <select name="api[<?=$callback_args['args']['service']?>][response][params][0][type]" style="width: 100%;">
                                        <?php foreach($param_types as $key=>$value) :?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="api[<?=$callback_args['args']['service']?>][response][params][0][description]" rows="5" style="width: 100%;"></textarea>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="api[<?=$callback_args['args']['service']?>][response][params][0][required]" value="1" />
                                </td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </li>
            
            <li>
                <h4><label for="api[<?=$callback_args['args']['service']?>][example][response]">Example Response</label></h4>
                <textarea name="api[<?=$callback_args['args']['service']?>][example][response]" rows="8" style="width: 100%;"><?=$api[$callback_args['args']['service']]['example']['response']?></textarea>
            </li>
        </ul>
    <?php
    }

    /**
     * Change Log Custom Post Type
     * Registers a custom post type for the Clear Books change log
     *
     * @see add_action('init', 'change_cpt');
     *
     * @return void
     * @author Andrew Mackrodt
     **/
    function change_cpt()
    {
        $labels = array( 'name'               => _x( 'Change Log', 'post type general name' ),
                         'singular_name'      => _x( 'Change', 'post type singular name' ),
                         'add_new'            => _x( 'Add New', 'change' ),
                         'add_new_item'       => __( 'Add New Change' ),
                         'edit_item'          => __( 'Edit Change' ),
                         'new_item'           => __( 'New Change' ),
                         'view_item'          => __( 'View Change' ),
                         'search_items'       => __( 'Search Change Log' ),
                         'not_found'          => __( 'Nothing found' ),
                         'not_found_in_trash' => __( 'Nothing found in Trash' ),
                         'parent_item_colon'  => ''
        );

        $args = array( 'labels'             => $labels,
                       'public'             => true,
                       'publicly_queryable' => true,
                       'hierarchical'       => true,
                       'show_ui'            => true,
                       'has_archive'        => 'change',
                       'rewrite'            => array( 'slug' => 'about/change-log' ),
                       '_builtin'           => false,
                       'capability_type'    => 'post',
                       'query_var'          => 'change',
                       'menu_position'      => null,
                       'supports'           => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ) );

        // register post
        register_post_type( 'change', $args );

        $args = array( 'public'         => true,
                       'hierarchical'   => true,
                       'label'          => 'Categories',
                       'rewrite'        => array( 'slug' =>'about/change-log/category' ),
                       'singular_label' => 'Category',
                       'query_var'      => true );

        // register taxonomy
        register_taxonomy( 'about', array( 'change' ), $args );
    }

    /**
     * Page Attributes Meta Box (Custom Override)
     * Overwrite the page_attributes_meta_box() function in the Wordpress core. This function keeps the original fields used by pages, but adds an additional field.
     * 
     * @see add_action("admin_init", "admin_init");
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function page_attributes_meta_box_custom() {
        global $post; global $wp_registered_sidebars;
        $post_type_object = get_post_type_object($post->post_type);
    ?>  
    
    <?php if ( $post_type_object->hierarchical ) : ?>
        <?php $pages = wp_dropdown_pages(array('post_type' => $post->post_type, 'exclude_tree' => $post->ID, 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0)); ?>
        
        <?php if (!empty($pages)) :?>
            <p><strong><?php _e('Parent') ?></strong></p>
            <label class="screen-reader-text" for="parent_id"><?php _e('Parent') ?></label>
            <?php echo $pages; ?>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if ( 'page' == $post->post_type && 0 != count( get_page_templates() ) ) : ?>
        <?php $template = !empty($post->page_template) ? $post->page_template : false;?>
        
        <p><strong><?php _e('Template') ?></strong></p>
        <label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label>
        <select name="page_template" id="page_template">
            <option value='default'><?php _e('Default Template'); ?></option>
            <?php page_template_dropdown($template); ?>
        </select>
    <?php endif; ?>

    <?php if('page' == $post->post_type) :?>
        <?php
            $custom = get_post_custom($post->ID);
            $sidebar = $custom["sidebar"][0];
            $sidebar_position = $custom["sidebar_position"][0];
            
            switch($post->page_template) {
                case 'template-sidebar-page.php':
                    if($sidebar_position == "left") {
                        $template_preview = get_bloginfo('template_url').'/images/templates/sidebar_left_layout.jpg';
                    }
                    else if($sidebar_position == "right") {
                        $template_preview = get_bloginfo('template_url').'/images/templates/sidebar_right_layout.jpg';
                    }
                    else {
                        $template_preview = null;
                    }
                break;
                
                case 'template-single-page.php':
                    $template_preview = get_bloginfo('template_url').'/images/templates/single_layout.jpg';
                break;
                
                case 'template-two-column-page.php':
                    $template_preview = get_bloginfo('template_url').'/images/templates/two_column_layout.jpg';
                break;
                
                case 'template-slider-page.php':
                    $template_preview = get_bloginfo('template_url').'/images/templates/slider_layout.jpg';
                break;
                
                default;
                    $template_preview = null;
                break;
            }
            
            if($post->page_template != 'template-sidebar-page.php') {
                $style = 'style="display: none;"';
            }
            
            if(empty($template_preview)) {
                $hide_preview = 'style="display: none;"';
            }
        ?>
            <div id="use_sidebar" <?=$style?>>
                <p><strong>Sidebar</strong></p>
                <select name="sidebar" id="sidebar" style="width: 145px;">
                    <option value="">-- Select Sidebar --</option>
                    <?php foreach($wp_registered_sidebars as $key => $value) :?>
                        <option value="<?=$key?>" <?php if($sidebar == $key) {echo 'selected';} ?>><?=$value['name']?></option>
                    <?php endforeach;?>
                </select>
                <select name="sidebar_position" id="sidebar_position" style="width: 100px;">
                    <option value="left" <?php if($sidebar_position == "left") {echo 'selected';} ?>>Align Left</option>
                    <option value="right" <?php if($sidebar_position == "right") {echo 'selected';} ?>>Align Right</option>
                </select>
            </div>
            
            <div id="template_preview" <?=$hide_preview?> data-template="<?=get_bloginfo('template_url')?>/images/templates/">
                <p><strong>Template Preview</strong></p>
                <img src="<?=$template_preview?>" width="255" height="165" alt="Template Preview" title="Template Preview" style="border: 1px solid #dadade; margin-bottom: 10px;" />
                <p id="template_info">
                    <?php if($post->page_template == 'template-two-column-page.php'):?>
                        Use the "More" tag to split the page content into two columns. Content before the more tag will appear in the left column, content after in the right column.
                    <?php endif;?>
                </p>
            </div>
    <?php endif; ?>
    
    <p><strong><?php _e('Order') ?></strong></p>
    <p><label class="screen-reader-text" for="menu_order"><?php _e('Order') ?></label>
    <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
    
    <?php wp_nonce_field('cb_validate_nonce', 'cb_nonce');?>
    <p><?php if ( 'page' == $post->post_type ) _e( 'Need help? Use the Help tab in the upper right of your screen.' ); ?></p>
    
    <?php
    }
    
    /* Theme settings */
    
    if (function_exists('add_theme_support')) {
        add_theme_support('post-thumbnails');
        add_theme_support('excerpt');
    }
    
    if (function_exists('add_image_size')) {
        set_post_thumbnail_size(220, 165, false); // default Post Thumbnail dimensions
        add_image_size('featured-page-single', 960, 9999, false);
        add_image_size('featured-page-sidebar', 675, 9999, false);
        add_image_size('featured-page-landing', 960, 436, true);
        add_image_size('grid_thumbnail', 286, 165, true);
        add_image_size('sidebar_thumbnail', 230, 9999, false);
    }
    
    // Filter Hooks
    add_filter('excerpt_length', 'excerpt_length');
    add_filter('the_excerpt', 'excerpt_read_more_link');
    add_filter('wp_nav_menu_items', 'extend_nav_menu_items', 10, 2);
    add_filter('wp_nav_menu_args', 'extend_nav_menu_args', 10, 1);
    add_filter('the_content', 'remove_empty_read_more_span');
    add_filter('query_vars', 'custom_query_vars');
    
    if(function_exists('wp_syntax_before_filter') && function_exists('wp_syntax_after_filter')) {
        add_filter('syntax_highlight', 'wp_syntax_before_filter', 0);
        add_filter('syntax_highlight', 'wp_syntax_after_filter', 99);
    }
    
    // Action Hooks
    add_action("admin_init", "admin_init");
    add_action('init', 'custom_rewrite_rules');
    add_action('init', 'guide_cpt');
    add_action('init', 'case_study_cpt');
 	add_action('init', 'testimonials_cpt'); /* Added by Bytewire 2012 */
    add_action('init', 'api_doc_cpt');
    add_action('init', 'change_cpt');
    add_action('admin_head', 'cpt_icons');
    add_action('save_post', 'save_custom_post_meta');
    add_action('show_user_profile', 'cb_user_profile_custom_fields');
    add_action('edit_user_profile', 'cb_user_profile_custom_fields');
    add_action('personal_options_update', 'cb_save_user_profile_custom_fields');
    add_action('edit_user_profile_update', 'cb_save_user_profile_custom_fields');
    add_action('get_video_player', 'check_video_support');
    add_action('wp_footer', 'format_social_network_comments', 40); // 40 to ensure we hook after SFC Comments Javascripts are included.
    add_action('wp_print_styles', 'theme_styles');
    add_action('wp_print_scripts', 'theme_scripts');
    add_action('guides_init','guides_init');
    add_action('cb_before_guide_sidebar', 'guides_sidebar');

    /* Navigation Menus */
    
    // Main navigation
    register_nav_menus(
        array(
            'main_nav' => 'Main navigation.',
            'secondary_nav' => 'Additional navigation which sits to the right of the main navigation.',
            'footer_nav' => 'Footer navigation.'
        )
    );
    
    /* Sidebars */
    
    // Home page sidebars
    register_sidebars(1, array(
        'name' => 'Sidebar - Home (Marketing)',
        'id' => 'sidebar-home-marketing',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    register_sidebars(1, array(
        'name' => 'Sidebar - Home (Customer)',
        'id' => 'sidebar-home-customer',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    // Generic Sidebar
    register_sidebars(1, array(
        'name' => 'Sidebar - Page',
        'id' => 'sidebar-page',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    // Help Guide Sidebar
    
    register_sidebars(1, array(
        'name' => 'Sidebar - Help Guides',
        'id' => 'sidebar-guide',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    // Blog Sidebar
    register_sidebars(1, array(
        'name' => 'Sidebar - Blog',
        'id' => 'sidebar-blog',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    // Footer sidebars
    register_sidebars(1, array(
        'name' => 'Sidebar - Footer (Marketing)',
        'id' => 'footer-marketing',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    register_sidebars(1, array(
        'name' => 'Sidebar - Footer (Customer)',
        'id' => 'footer-customer',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
    
    /* Widgets */
    //pagenavrequire_once(TEMPLATEPATH.'/widgets.php');
    
    /* Shortcodes */
    
    /**
     * Clear Books Button
     * Renders a standard hyperlink as a styled button. Short code takes the following format:
     * [cb_button color="" size="" href="url" text="value" class="" style="" target=""]
     *
     * @param 'color' string (green/blue/red/yellow/white) the colour of the button. Default green.
     * @param 'size' string (big/small) the size of the button. Default small.
     * @param 'href' string the url to link to.
     * @param 'text' string the text to display on the button.
     * @param 'class' string any additional classes to apply to the button.
     * @param 'style' string any additional inline styles to apply
     * @param 'target' string the target 
     *
     * @return $button string the html output of the button
     * @author Matt Fairbrass
     **/
    function cb_button($atts) {
        extract(shortcode_atts(array(
            // Defaults if not set
            'color' => 'green',
            'size' => 'small',
            'href' => '#',
            'text' => '',
            'class' => '',
            'style' => '',
            'target' => ''
        ), $atts));
        
        $button = '<a href="'.$href.'" class="button '.$size.' '.$color.' '.$class.'" style="'.$style.'" target="'.$target.'"><span>'.$text.'</span></a>';
        
        return $button;
    }
    add_shortcode('cb_button', 'cb_button');
    
    /**
     * Clear Books Video
     * Shortcode wrapper for get_video_player function for displaying videos in posts. Use this instead of the futube shortcode as
     * the get_video_player function hooks check_video_support to display a nice message if flash or html5 video is not available to the user.
     *
     * @param $video_id string the youtube or vimeo video id.
     * @param $width int the width of the video player.
     * @param $height int the height of the video player.
     * @param $service string the service hosting the video - either youtube or vimeo.
     * @param $title string only applicable for youtube videos - Creates a title for the video "splash page".
     *
     * @return $html string the html output for the video player
     * @author Matt Fairbrass
     **/
    function cb_video($atts) {
        extract(shortcode_atts(array(
            // Defaults if not set
            'video' => '',
            'width' => '',
            'height' => '#',
            'service' => '',
            'title' => 'Clear Books - Online Accounting to Free your Time.'
        ), $atts));
        
        $html = '<div class="video">';
            $html .= get_video_player($video, $width, $height, $service, $title = "");
        $html .= '</div>';
        
        return $html;
    }
    
    add_shortcode('cb_video', 'cb_video');
    
    /**
     * Tracking Redirect
     * Track certain pages and redirect to homepage
     *
     * @param $atts array the shortcode attributes array 
     *
     * @return void
     * @author Fubra
     **/
    function tracking_redirect($atts) {
        extract(shortcode_atts(array(
            'track' => false
        ), $atts));

        setcookie("track", $track, time()+(60*60*24*30), "/",  ".clearbooks.co.uk".$_SERVER['VIRTUAL_DOMAIN']);   // expire in 1 month

        Header( "HTTP/1.1 301 Moved Permanently" );
        Header( "Location: http://www.clearbooks.co.uk");
        exit;
    }
    add_shortcode('tracking_redirect', 'tracking_redirect');
    
    /**
     * System URL
     * Return the system url
     *
     * @return constant SYSTEM_URL
     * @author Matt Fairbrass
     **/
    function system_url($atts) {
        return SYSTEM_URL;
    }
    add_shortcode('system_url', 'system_url');
    
    /**
     * Get Satisfaction Search
     * Output the Get Satisfaction Search Box
     *
     * @param $atts array the shortcode attributes array
     *
     * @return $g_box string the html output.
     * @author Matt Fairbrass
     **/
    function gs_search($atts) {
        extract(shortcode_atts(array(
            // Defaults if not set
            'gs_id' => 'clearbooks',
            'title' => 'Active customer service questions in Clear Books'
        ), $atts));
        
        $gs_box = '<div id="gsfn_list_widget">';
            $gs_box .= '<a href="http://getsatisfaction.com/'.$gs_id.'" class="widget_title">'.$title.'</a>';
            $gs_box .= '<div id="gsfn_content">Loading...</div>';
            $gb_box .= '<div class="powered_by">';
                $gb_box .= '<a href="http://getsatisfaction.com/">';
                    $gb_box .= '<img alt="Favicon" src="http://getsatisfaction.com/favicon.gif" style="vertical-align: middle;" /></a>';
                    $gb_box .= '<a href="http://getsatisfaction.com/">Get Satisfaction support network</a>';
                $gb_box .= '</div>';
            $gb_box .= '</div>';
    
        $gb_box .= '<script src="http://getsatisfaction.com/clearbooks/widgets/javascripts/cd7cafdce0/widgets.js" type="text/javascript"></script>';
        $gb_box .= '<script src="http://getsatisfaction.com/clearbooks/topics.widget?callback=gsfnTopicsCallback&length=80&limit=5&sort=recently_active&style=question" type="text/javascript"></script>';
    
        return $gs_box;
    }
    add_shortcode('gs_search', 'gs_search');
    
    /**
     * Get Satisfaction
     * Output the Get Satisfaction Search Form
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function get_satisfaction($atts) {
       extract(shortcode_atts(array(
           'type' => false
       ), $atts));
        
       switch ($type) {
           case 'search':
               return "<div id='gsfn_search_widget'><a href='http://getsatisfaction.com/clearbooks' class='widget_title'>People-Powered Customer Service for Clear Books</a>
            <div class='gsfn_content'>
            <form accept-charset='utf-8' action='http://getsatisfaction.com/clearbooks' id='gsfn_search_form' method='get' onsubmit='gsfn_search(this); return false;'>
            <div>
            <input name='product' type='hidden' value='clearbooks_clear_books' />
            <input name='style' type='hidden' value='question' />
            <input name='limit' type='hidden' value='5' />
            <input name='utm_medium' type='hidden' value='widget_search' />
            <input name='utm_source' type='hidden' value='widget_clearbooks' />
            <input name='callback' type='hidden' value='gsfnResultsCallback' />
            <input name='format' type='hidden' value='widget' />
            <label class='gsfn_label' for='gsfn_search_query'>Ask a question, share an idea, or report a problem.</label>
            <input id='gsfn_search_query' maxlength='120' name='query' type='text' value='' />
            <input id='continue' type='submit' value='Continue' />
            </div>
            </form>
            <div id='gsfn_search_results' style='height: auto;'></div>
            </div>
            <div class='powered_by'>
            <a href='http://getsatisfaction.com/'><img alt='Favicon' src='http://getsatisfaction.com/favicon.gif' style='vertical-align: middle;' /></a>
            <a href='http://getsatisfaction.com/'>Get Satisfaction support network</a>
            </div>
            </div>
            <script src='http://getsatisfaction.com/clearbooks/widgets/javascripts/cd7cafdce0/widgets.js' type='text/javascript'></script>";
           break;
           case 'questions':
            return "<div id='gsfn_list_widget'>
            <a href='http://getsatisfaction.com/clearbooks' class='widget_title'>Active customer service questions in Clear Books</a>
            <div id='gsfn_content'>Loading...</div>
            <div class='powered_by'>
            <a href='http://getsatisfaction.com/'><img alt='Favicon' src='http://getsatisfaction.com/favicon.gif' style='vertical-align: middle;' /></a>
            <a href='http://getsatisfaction.com/'>Get Satisfaction support network</a>
            </div>
            </div>
            <script src='http://getsatisfaction.com/clearbooks/widgets/javascripts/cd7cafdce0/widgets.js' type='text/javascript'></script>
            <script src='http://getsatisfaction.com/clearbooks/topics.widget?callback=gsfnTopicsCallback&length=80&limit=5&sort=recently_active&style=question' type='text/javascript'></script>
            ";
        break;
           default:
               return '<iframe width="100%" height="380" scrolling="no" frameborder="0" src="http://getsatisfaction.com/clearbooks/feedback/topics/new?display=inline&amp;style=idea"></iframe>';
       }
    }
    add_shortcode('getsatisfaction', 'get_satisfaction');
    
    /**
     * Post Thumbnail Shortcode
     * Registers a shortcode to display post thumbnails in wordpress posts.
     * 
     * @param $atts array An array of attributes to apply to the post thumbnail.
     * @return string the html output of the post thumbnail.
     *
     * @author Matt Fairbrass
     */
    function post_thumbnail_shortcode($atts) {
        global $post;
        
        function size_value() {
            if (isset($width) && isset($height)) {
                return array($width,$height);
            } 
            else {
                return $size;
            }
        }
        
        $default = array(
            'size' => size_value(),
            'width' => null,
            'height' => null,
            'link' => '',
            'alt' => $post->post_excerpt,
            'title' => $post->post_title,
            'class' => null
        );
        
        extract(shortcode_atts($default, $atts));       
        
        if ($link != '') { 
            $output .= "<a title=\"$title\" href=\"".trim($link)."\">"; 
        }
    
        if(function_exists('has_post_thumbnail') && has_post_thumbnail()) {
            $output .= get_the_post_thumbnail($post->ID, $size, array('title' => $title, 'alt' => $alt, 'class' => $class));
        }

        if ($link != '') { 
            $output .= "</a>"; 
        }
        
        return $output;
    }
    
    add_shortcode('post-thumbnail','post_thumbnail_shortcode');
    
    /**
     * Press Releases
     * Shortcode that outputs press releases posted in the blog.
     *
     * @return $output string the html output
     * @author Fubra
     **/
    function press_releases() {
        $posts = get_posts('category_name=press&showposts=10');
        $output = '<dl>';
        foreach ($posts AS $post) {
            $output .= '<dt>'.date('jS F Y', strtotime($post->post_date)).'</dt>'.
                       '<dd><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></dd>';
        }
        $output .= '</dl>';
        return $output;
    }
    add_shortcode('press_releases', 'press_releases');
    
    /* Custom User Profile Fields */
    
    /**
     * Clear Books User Profile Custom Fields
     * Add additional profile fields to the user profile page
     *
     * @param $user object the user object
     *
     * @see add_action('show_user_profile', 'cb_user_profile_custom_fields');
     * @see add_action('edit_user_profile', 'cb_user_profile_custom_fields');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function cb_user_profile_custom_fields($user) { 
    ?>
        <h3>Additional Contact Information</h3>
        <table class="form-table">
            <tr>
                <th><label for="facebook">Facebook</label></th>
                <td>
                    <span class="description">Please enter your Facebook page URL.</span><br />
                    <input type="text" name="facebook" id="facebook" value="<?=get_the_author_meta('facebook', $user->ID)?>" class="regular-text" />
                    <fieldset>
                        <label for="public_facebook">
                            <input type="checkbox" name="public_facebook" id="public_facebook" value="1" <?php if(get_the_author_meta('public_facebook', $user->ID) == 1) { echo 'checked';}?> /> Show on public profile?
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="twitter">Twitter</label></th>
                <td>
                    <span class="description">Please enter your Twitter username.</span><br />
                    <input type="text" name="twitter" id="twitter" value="<?=get_the_author_meta('twitter',$user->ID)?>" class="regular-text" />
                    <fieldset>
                        <label for="public_twitter">
                            <input type="checkbox" name="public_twitter" id="public_twitter" value="1" <?php if(get_the_author_meta('public_twitter', $user->ID) == 1) { echo 'checked';}?> /> Show on public profile?
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="googleplus">Google+</label></th>
                <td>
                    <span class="description">Please enter your Google+ profile URL.</span><br />
                    <input type="text" name="googleplus" id="googleplus" value="<?=get_the_author_meta('googleplus',$user->ID)?>" class="regular-text" />
                    <fieldset>
                        <label for="public_googleplus">
                            <input type="checkbox" name="public_googleplus" id="public_googleplus" value="1" <?php if(get_the_author_meta('public_googleplus', $user->ID) == 1) { echo 'checked';}?> /> Show on public profile?
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><label for="linkedin">LinkedIn</label></th>
                <td>
                    <span class="description">Please enter your LinkedIn profile URL.</span><br />
                    <input type="text" name="linkedin" id="linkedin" value="<?=get_the_author_meta('linkedin', $user->ID)?>" class="regular-text" />
                    <fieldset>
                        <label for="public_linkedin">
                            <input type="checkbox" name="public_linkedin" id="public_linkedin" value="1" <?php if(get_the_author_meta('public_linkedin', $user->ID) == 1) { echo 'checked';}?> /> Show on public profile?
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <h3>Skills &amp; Certifications</h3>
        <table class="form-table">
            <tr>
                <th><label for="skills">Skills</label></th>
                <td>
                    <span class="description">Please list your skills separated by commas.</span><br />
                    <textarea name="skills" id="skills" rows="6" class="regular-text"><?=get_the_author_meta('skills', $user->ID)?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="certifications">Certifications</label></th>
                <td>
                    <span class="description">Please list your certifications on separate lines.</span><br />
                    <textarea name="certifications" id="certifications" rows="6" class="regular-text"><?=get_the_author_meta('certifications', $user->ID)?></textarea>
                </td>
            </tr>
        </table>
        
        <?php if(current_user_can('administrator')) :?>
            <?php
                $cb_depts = array('Management', 'Support', 'Development', 'Sales &amp; Marketing');
            ?>
            <h3>Clear Books</h3>
            <table class="form-table">
                <tr>
                    <th><label for="clearbooks_start_date">Employment</label></th>
                    <td>
                        <fieldset>
                            <label for="clearbooks_start_date">
                                Start Date <input type="text" name="clearbooks_start_date" id="clearbooks_start_date" value="<?=get_the_author_meta('clearbooks_start_date', $user->ID)?>" readonly="true" />
                            </label>

                            <?php 
                                if(get_the_author_meta('clearbooks_staff', $user->ID) == 1) {
                                    $style = 'display:none;';
                                }
                            ?>

                            <label for="clearbooks_end_date" style="<?=$style?>">
                                End Date <input type="text" name="clearbooks_end_date" id="clearbooks_end_date" value="<?=get_the_author_meta('clearbooks_end_date', $user->ID)?>" readonly="true" <?php if(get_the_author_meta('clearbooks_staff', $user->ID) == 1) {echo 'disabled';}?> />
                            </label><br /><br />
                            <label for="clearbooks_staff">
                                <input type="checkbox" name="clearbooks_staff" id="clearbooks_staff" value="1" <?php if(get_the_author_meta('clearbooks_staff', $user->ID) == 1) {echo 'checked';}?> /> <?=ucwords(strtolower($user->user_login))?> currently works for Clear Books.
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="clearbooks_position">Position</label></th>
                    <td>
                        <input type="text" name="clearbooks_position" id="clearbooks_position" value="<?=get_the_author_meta('clearbooks_position', $user->ID)?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="clearbooks_department">Department</label></th>
                    <td>
                        <select name="clearbooks_department" id="clearbooks_department">
                            <option value="">-- Select Department --</option>
                            <?php foreach($cb_depts as $dept):?>
                                <option value="<?=$dept?>" <?php if($dept == get_the_author_meta('clearbooks_department', $user->ID)) { echo 'selected';}?>><?=$dept?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="clearbooks_projects">Projects</label></th>
                    <td>
                        <textarea name="clearbooks_projects" id="clearbooks_projects" rows="6" class="regular-text"><?=get_the_author_meta('clearbooks_projects', $user->ID)?></textarea><br />
                        <span class="description">Information about the projects you are working/have worked on for Clear Books.</span>
                    </td>
                </tr>
            </table>
        <?php endif;?>
    <?php 
    }
    
    
    /**
     * Clear Books User Profile Custom Fields
     * Save the custom user profile fields
     *
     * @param $user_id object the user object
     *
     * @see add_action('personal_options_update', 'cb_save_user_profile_custom_fields');
     * @see add_action('edit_user_profile_update', 'cb_save_user_profile_custom_fields');
     *
     * @return void
     * @author Matt Fairbrass
     **/
    function cb_save_user_profile_custom_fields($user_id) {
        $custom_user_fields = array('facebook', 'twitter', 'linkedin', 'googleplus', 'public_facebook', 'public_twitter', 'public_linkedin', 'public_googleplus',  'skills', 'certifications', 'clearbooks_start_date', 'clearbooks_end_date', 'clearbooks_staff', 'clearbooks_position', 'clearbooks_department', 'clearbooks_projects');
        
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        
        foreach($custom_user_fields as $custom_user_field) {
            if(isset($_POST[$custom_user_field])) {             
                
            }
            else {
                $_POST[$custom_user_field] = null;
            }
            
            update_usermeta($user_id, $custom_user_field, esc_attr($_POST[$custom_user_field]));
        }       
    }

// Custom HTML5 Comment Markup
function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li>
     <article <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
       <header class="comment-author vcard">
          <?php echo get_avatar($comment,$size='66',$default='<path_to_url>' ); ?>
          <?php edit_comment_link(__('(Edit)'),'  ','') ?>
		  <?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
          
       </header>
       <?php if ($comment->comment_approved == '0') : ?>
          <em><?php _e('Your comment is awaiting moderation.') ?></em>
          <br />
       <?php endif; ?>

       <?php comment_text() ?>

       <nav>
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
       </nav>
		<footer>  
			<time><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></time>
		</footer>
     </article>
    <!-- </li> is added by wordpress automatically -->
<?php
}

automatic_feed_links();

// Widgetized Sidebar HTML5 Markup
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'before_widget' => '<section>',
		'after_widget' => '</section>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
}

// Custom Functions for CSS/Javascript Versioning
$GLOBALS["TEMPLATE_URL"] = get_bloginfo('template_url')."/";
$GLOBALS["TEMPLATE_RELATIVE_URL"] = wp_make_link_relative($GLOBALS["TEMPLATE_URL"]);

// Add ?v=[last modified time] to style sheets
function versioned_stylesheet($relative_url, $add_attributes=""){
  echo '<link rel="stylesheet" href="'.versioned_resource($relative_url).'" '.$add_attributes.'>'."\n";
}

// Add ?v=[last modified time] to javascripts
function versioned_javascript($relative_url, $add_attributes=""){
  echo '<script src="'.versioned_resource($relative_url).'" '.$add_attributes.'></script>'."\n";
}

// Add ?v=[last modified time] to a file url
function versioned_resource($relative_url){
  $file = $_SERVER["DOCUMENT_ROOT"].$relative_url;
  $file_version = "";

  if(file_exists($file)) {
    $file_version = "?v=".filemtime($file);
  }

  return $relative_url.$file_version;
}