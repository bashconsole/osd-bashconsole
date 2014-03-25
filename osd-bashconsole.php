<?php

/*
Plugin Name: osd-bashconsole
Plugin URI: 
Description: Submenu widget and [osdsubpagesbashconsole] submenu shortcode
Version: 1.0
Author:  bashconsole
Author URI:  http://bashconsole.com
License: GPLv2 or later
*/




define(IMAGE_WIDTH, 30);
define(IMAGE_HEIGHT, 30);
define(TITLE_LENGTH, 20);


class OSD_Bashconsole_Walker extends Walker_Page {
        function start_el(&$output, $page, $depth, $args, $current_page) {
        if ( $depth )
            $indent = str_repeat("\t", $depth);
        else
            $indent = '';
 
        extract($args, EXTR_SKIP);
        if($page_item != '' || $page_item_ != '')
		$css_class = array($page_item, $page_item_ . $page->ID);
        if ( !empty($current_page) ) {
            $_current_page = get_page( $current_page );
            _get_post_ancestors($_current_page);
            if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
                $css_class[] = $current_page_ancestor;
            if ( $page->ID == $current_page )
                $css_class[] = $current_page_item;
            elseif ( $_current_page && $page->ID == $_current_page->post_parent )
                $css_class[] = $current_page_parent;
        } elseif ( $page->ID == get_option('page_for_posts') ) {
            $css_class[] = $current_page_parent;
        }
 
	if(sizeof($css_class)){
		$css_class = implode( ' ', apply_filters( $page_css_class, $css_class, $page, $depth, $args, $current_page ) );
		$output .= $indent . '<li class="' . $css_class . '">';
	} else
		$output .= $indent . '<li>';
        
        $output .= '<a href="' . get_permalink($page->ID) . '">' . $link_before . osd_bashconsole_short_title(apply_filters( 'the_title', $page->post_title, $page->ID ), TITLE_LENGTH) . $link_after . get_the_post_thumbnail($page->ID, array(IMAGE_WIDTH, IMAGE_HEIGHT)) .'</a>';
 
        if ( !empty($show_date) ) {
            if ( 'modified' == $show_date )
                $time = $page->post_modified;
            else
                $time = $page->post_date;
 
            $output .= " " . mysql2date($date_format, $time);
        }
    }
}


function osd_bashconsole_short_title($title, $length = 100) {
		if( strlen($title) > $length ){ // limiting content
			$pos = strpos($title, ' ', $length); // find first space position
			if ($pos !== false)
				$first_space_pos = $pos;
			else
				$first_space_pos = $limit_content;				
			$title = mb_substr($title, 0, $first_space_pos, 'UTF-8') . '...';
		}
		
		return  force_balance_tags($title);
}


function osd_bashconsole_list_subpages($args = '') {
	$defaults = array(
		'depth' => 1, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => '', 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => '', 'walker' => '',
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
	$r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);

	if ( !empty($pages) ) {

		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page )
			$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

	}

	$output = apply_filters('wp_list_pages', $output, $r);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}


function osd_bashconsole_subpages_add_stylesheet() {
		wp_enqueue_style( 'osd-bashconsole-style', plugins_url( '/css/styles.css', __FILE__ ), false, '1.0', 'all' );
}

function osd_bashconsole_subpages_add_script() {
		wp_enqueue_script( 'osd-bashconsole-script', plugins_url( '/js/sort.js', __FILE__ ), false, '1.0', false );
		wp_localize_script('osd-bashconsole-script', 'this_script_vars', array('imagepath' => plugins_url( '/images/', __FILE__ )));
}


wp_register_script( $handle, $src, $deps, $ver, $in_footer );
wp_enqueue_style( $handle, $src, $deps, $ver, $media );

function osd_bashconsole_subpages_shortcode( $atts ) {
	global $post;
	
	$category = get_the_category($post->ID);
		
	if(!$category) {  // if pages not posts
		
	$args = array(
		'child_of'     => $post->ID,
		'sort_order' => 'ASC',
		'sort_column' => 'menu_order',
		'hierarchical' => 1,
		'exclude'      => 0,
		'include'      => '',
		'meta_key'     => '',
		'meta_value'   => '',
		'authors'      => '',
		'parent'       => -1,
		'exclude_tree' => '',
		'number'       => '',
		'offset'       => 0,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'echo' => 0,
		'walker' => new OSD_Bashconsole_Walker()		
	);
		
		
	return  '	<div class="sort-list-block">
	<div class="sort-panel">
		<a class="sort-icon"><img src="' . plugins_url( '/images/sort-icon-def.png', __FILE__ ) . '"></a>
	</div>
	<ul class="linkbar">' . "\n" . osd_bashconsole_list_subpages( $args ) . "\n</ul></div>\n";	
			
		}else{ // if posts category or post page
		
		      $args = array(
			    'numberposts'     => 0,
			    'offset'          => 0,
			    'category'        => $category[0]->cat_ID,
			    'orderby'         => '',
			    'order'           => '',
			    'include'         => '',
			    'exclude'         => '',
			    'meta_key'        => '',
			    'meta_value'      => '',
			    'post_type'       => 'post',
			    'post_mime_type'  => '',
			    'post_parent'     => '',
			    'post_status'     => 'publish'
		      );
	
		      $posts_array = get_posts( $args );
	
		      $output =  '<div class="sort-list-block">
	<div class="sort-panel">
		<a class="sort-icon"><img src="' . plugins_url( '/images/sort-icon-def.png', __FILE__ ) . '"></a>
	</div>
	<ul class="linkbar">';
	
		      foreach ($posts_array as $single_post) {
			      $output .= "<li><a href=\"" . get_page_link($single_post->ID) . "\" title=\"" . $single_post->post_title . "\">"
			      . osd_bashconsole_short_title($single_post->post_title, TITLE_LENGTH) . ' ' . get_the_post_thumbnail($single_post->ID, array(IMAGE_WIDTH, IMAGE_HEIGHT))
			      . "</a></li>\n";
		      }
         
		      $output .= "</ul></div>\n";
		      
		      return $output;
         	
	       }
	
}


add_action('wp_print_styles', 'osd_bashconsole_subpages_add_stylesheet');
add_action('wp_print_scripts', 'osd_bashconsole_subpages_add_script');
add_shortcode( 'osdsubpagesbashconsole', 'osd_bashconsole_subpages_shortcode' );



/* Bashconsole Test Widget */
class OSD_Bashconsole_Submenu_Widget extends WP_Widget {

	function OSD_Bashconsole_Submenu_Widget() {
		// widget actual processes
		parent::WP_Widget(false, $name = 'Bashconsole Sidebar Menu', array(
			'description' => 'Displays a Bashconsole Sidebar Menu'
		));
	}
	
	function widget($args, $instance) {
		global $post;
		extract($args);
				
		echo $before_widget;	
		
		$category = get_the_category($post->ID);
		
		if(!$category) {  // if pages not posts
			
		$args = array(
			'child_of'     => $post->ID,
			'sort_order' => 'ASC',
			'sort_column' => 'menu_order',
		        'hierarchical' => 1,
		        'exclude'      => 0,
			'include'      => '',
			'meta_key'     => '',
			'meta_value'   => '',
			'authors'      => '',
			'parent'       => -1,
			'exclude_tree' => '',
			'number'       => '',
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'echo' => 0,
			'walker' => new OSD_Bashconsole_Walker(),
			'page_item' => 'menu-item',
		        'page_item_' => 'menu-item-'
		);
		
		      echo  '<div class="sort-list-block-menu">
		      <div class="sort-panel-menu">
				<a class="sort-icon-menu"><img src="' . plugins_url( '/images/sort-icon-menu-def.png', __FILE__ ) . '"></a>
		      </div>
		      <ul class="sidebar-menu">' . "\n" . osd_bashconsole_list_subpages( $args ) . "\n" . '</ul></div>';				
		
		}else{ // if posts category or post page
		
		      $args = array(
			    'numberposts'     => 0,
			    'offset'          => 0,
			    'category'        => $category[0]->cat_ID,
			    'orderby'         => '',
			    'order'           => '',
			    'include'         => '',
			    'exclude'         => '',
			    'meta_key'        => '',
			    'meta_value'      => '',
			    'post_type'       => 'post',
			    'post_mime_type'  => '',
			    'post_parent'     => '',
			    'post_status'     => 'publish'
		      );
	
		      $posts_array = get_posts( $args );
	
		      echo  '<div class="sort-list-block-menu">
		      <div class="sort-panel-menu">
				<a class="sort-icon-menu"><img src="' . plugins_url( '/images/sort-icon-menu-def.png', __FILE__ ) . '"></a>
		      </div>
		      <ul class="sidebar-menu">' . "\n";
	
		      foreach ($posts_array as $single_post) {
			      $li = "<li class=\"menu-item  menu-item-" . $single_post->ID . "\"><a href=\"" . get_page_link($single_post->ID) . "\" title=\"" . $single_post->post_title . "\">"
			      . osd_bashconsole_short_title($single_post->post_title, TITLE_LENGTH) . ' ' . get_the_post_thumbnail($single_post->ID, array(IMAGE_WIDTH, IMAGE_HEIGHT))
			      . "</a></li>\n";
			      echo $li;
		      }
         
		      echo "</ul></div>\n";
         	
	       }
				
		echo $after_widget;
		
	}

	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

}

function register_osd_bashconsole_submenu_widget() {
	register_widget('OSD_Bashconsole_Submenu_Widget');
}

add_action('widgets_init', 'register_osd_bashconsole_submenu_widget');

