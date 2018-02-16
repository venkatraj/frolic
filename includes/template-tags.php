<?php
/**
 * Custom template tags for this theme.
 *   
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Frolic  
 */


if ( ! function_exists( 'frolic_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function frolic_post_nav() {    
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation clearfix" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'frolic' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous"><span class="meta-previuous-post">%link</span></div>', _x( ' &larr; Previous Post', 'Previous post link', 'frolic' ) );
				next_post_link(     '<div class="nav-next"><span class="meta-next-post">%link</span></div>',     _x( 'Next Post&nbsp; &rarr;', 'Next post link',     'frolic' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;


if ( ! function_exists( 'frolic_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function frolic_entry_footer() {
	// Hide category and tag text for pages.
	
	if ( 'post' == get_post_type() ) {    
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ' ', 'frolic' ) ); 
		if ( $categories_list ) {
			printf( '<span class="cat-links"><i class="fa fa-folder-open"></i> ' . __( '%1$s ', 'frolic' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ' ', 'frolic' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><i class="fa fa-tags"></i> ' . __( '%1$s ', 'frolic' ) . '</span>', $tags_list );
		}
	}
}
endif;


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
if ( ! function_exists( 'frolic_categorized_blog' ) ) :
	function frolic_categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'frolic_categories' ) ) ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories( array(
				'fields'     => 'ids',
				'hide_empty' => 1,

				// We only need to know if there is more than one category.
				'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'frolic_categories', $all_the_cool_cats );
		}

		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so frolic_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so frolic_categorized_blog should return false.
			return false;
		}
	}
endif;

/**
 * Flush out the transients used in frolic_categorized_blog.
 */
if ( ! function_exists( 'frolic_category_transient_flusher' ) ) :
	function frolic_category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient( 'frolic_categories' );
	}
endif;
add_action( 'edit_category', 'frolic_category_transient_flusher' );
add_action( 'save_post',     'frolic_category_transient_flusher' );

if( ! function_exists('frolic_top_meta') ) {
	function frolic_top_meta() {
		global $post;
		if ( 'post' == get_post_type() ) {  ?>
			<div class="entry-meta">
				<span class="date-structure">				
					<span class="dd"><i class="fa fa-calendar"></i><?php the_time(get_option('date_format')); ?></span>			
				</span>
				<?php frolic_author(); ?>
				<?php frolic_comments_meta(); ?> 
				<?php frolic_edit();
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'frolic' ) ); 
				if ( $categories_list ) {
					printf( '<span class="cat-links"><i class="fa fa-folder-open"></i> ' . __( '%1$s ', 'frolic' ) . '</span>', $categories_list );
				} ?>

			</div><!-- .entry-meta --><?php
		}
	}
}


// Recent Posts with featured Images to be displayed on home page
if( ! function_exists('frolic_recent_posts') ) {
	function frolic_recent_posts() {       
		$output = '';
		$posts_per_page  = get_theme_mod('recent_posts_count', 4 );
		// WP_Query arguments
		$args = array (
			'post_type'              => 'post',
			'post_status'            => 'publish',   
			'posts_per_page'         => intval($posts_per_page),
			'ignore_sticky_posts'    => true,
			'order'                  => 'DESC',
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			$output .= '<div class="post-wrapper">'; 
			$output .=  '<div class="container">'; 
			$output .=  '<h2 class="recent-post-title">'. apply_filters('frolic_recent_post_title',__('Blog Updates','frolic')).'</h2>';
			$output .= '<div class="latest-posts clearfix">';
			while ( $query->have_posts() ) {
				$query->the_post();
				$output .= '<div class="four columns latst-post-wrapper">';
				$output .= '<div class="latest-post clearfix">';
						if ( has_post_thumbnail() ) {
							$output .= '<div class="latest-post-thumb"><a href="'. esc_url(get_permalink()) . '">'; 
							$output .= get_the_post_thumbnail($query->post->ID ,'frolic-recent-posts-img');
							$output .= '</a></div><!-- .latest-post-thumb -->';
						}		
						$output .= '<div class="latest-post-content-wrapper">';
							$output .= '<h4><a href="'. esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
							$output .='<div class="entry-meta">';
								 $output .='<span class="data-structure"><span class="dd"><a class="url fn n" href="'. get_day_link(get_the_time('Y'), get_the_time('m'),get_the_time('d')).'">' . get_the_time('j M, Y').'</a></span></span>';
							     $output .= frolic_get_comments_meta();  
							$output .='</div><!-- entry-meta -->';
							$output .= '</div>';
					
					$output .= '</div>';	
				$output .= '</div><!-- .latest-post -->';
			}
			$output .= '</div><!-- latest post end -->';
			$output .= '</div>';
			$output .= '</div><!-- .post-wrapper -->';
		} 
		$query = null;
		// Restore original Post Data
		wp_reset_postdata();
		echo $output;
	}
}

/**
  * Generates Breadcrumb Navigation 
  */
 
 if( ! function_exists( 'frolic_breadcrumbs' )) {
 
	function frolic_breadcrumbs() {
		/* === OPTIONS === */
		$text['home']     = __( '<i class="fa fa-home"></i>','frolic' ); // text for the 'Home' link
		$text['category'] = __( 'Archive by Category "%s"','frolic' ); // text for a category page
		$text['search']   = __( 'Search Results for "%s" Query','frolic' ); // text for a search results page
		$text['tag']      = __( 'Posts Tagged "%s"','frolic' ); // text for a tag page
		$text['author']   = __( 'Articles Posted by %s','frolic' ); // text for an author page
		$text['404']      = __( 'Error 404','frolic' ); // text for the 404 page

		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$showOnHome  = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$breadcrumb_char = get_theme_mod( 'breadcrumb_char', '1' );
		if ( $breadcrumb_char ) {
		 switch ( $breadcrumb_char ) {
		 	case '2' :
		 		$delimiter = ' &#47; ';
		 		break;
		 	case '3':
		 		$delimiter = ' &gt; ';
		 		break;
		 	case '1':
		 	default:
		 		$delimiter = ' &raquo; ';
		 		break;
		 }
		}

		$before      = '<span class="current">'; // tag before the current crumb
		$after       = '</span>'; // tag after the current crumb
		/* === END OF OPTIONS === */

		global $post;
		$homeLink = esc_url(home_url()) . '/';
		$linkBefore = '<span typeof="v:Breadcrumb">';
		$linkAfter = '</span>';
		$linkAttr = ' rel="v:url" property="v:title"';
		$link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

		if (is_home() || is_front_page()) {

			if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . esc_url($homeLink) . '">' . $text['home'] . '</a></div>';

		} else {

			echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, esc_url($homeLink), $text['home']) . $delimiter;

			if ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
				}
				echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

			} elseif ( is_search() ) {
				echo $before . sprintf($text['search'], get_search_query()) . $after;

			} elseif ( is_day() ) {
				echo sprintf($link, get_year_link(get_the_time(__( 'Y', 'frolic') )), get_the_time(__( 'Y', 'frolic'))) . $delimiter;
				echo sprintf($link, get_month_link(get_the_time(__( 'Y', 'frolic')),get_the_time(__( 'm', 'frolic'))), get_the_time(__( 'F', 'frolic'))) . $delimiter;
				echo $before . get_the_time(__( 'd', 'frolic')) . $after;

			} elseif ( is_month() ) {
				echo sprintf($link, get_year_link(get_the_time(__( 'Y', 'frolic'))), get_the_time(__( 'Y', 'frolic'))) . $delimiter;
				echo $before . get_the_time(__( 'F', 'frolic')) . $after;

			} elseif ( is_year() ) {
				echo $before . get_the_time(__( 'Y', 'frolic')) . $after;

			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {  
					$post_type = get_post_type_object(get_post_type()); 
					printf($link, get_post_type_archive_link(get_post_type()), $post_type->labels->singular_name);
					if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
				} else {   
					$cat = get_the_category(); $cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
					if ($showCurrent == 1) echo $before . get_the_title() . $after;
				}

			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				echo $before . $post_type->labels->singular_name . $after;

			} elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) echo $before . get_the_title() . $after;

			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo $delimiter;
				}
				if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

			} elseif ( is_tag() ) {
				echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

			} elseif ( is_author() ) {
		 		global $author;
				$userdata = get_userdata($author);
				echo $before . sprintf($text['author'], $userdata->display_name) . $after;

			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;
			}

			if ( get_query_var('paged') ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
				 _e('Page', 'frolic' ) . ' ' . get_query_var('paged');
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
			}

			echo '</div>';

		}
	
	} // end frolic_breadcrumbs()

}

if ( ! function_exists( 'frolic_author' ) ) :
	function frolic_author() {
		echo frolic_get_author();
	}
endif;
 
if ( ! function_exists( 'frolic_get_author' ) ) :
	function frolic_get_author() {  
		$byline = sprintf(
			esc_html_x( ' %s', 'post author', 'frolic' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '"><i class="fa fa-user"></i> ' . esc_html( get_the_author() ) . '</a></span>'
		);		

		return $byline;  
	}
endif;  

if ( ! function_exists( 'frolic_edit' ) ) :
	function frolic_edit() {
		edit_post_link( __( 'Edit', 'frolic' ), '<span class="edit-link"><i class="fa fa-pencil"></i> ', '</span>' );
	}
endif;


// Related Posts Function by Tags (call using frolic_related_posts(); ) /NecessarY/ May be write a shortcode?
if ( ! function_exists( 'frolic_related_posts' ) ) :
	function frolic_related_posts() {
		echo '<ul id="frolic-related-posts">';
		global $post;
		$post_hierarchy = get_theme_mod('related_posts_hierarchy','1');
		$relatedposts_per_page  =  get_option('post_per_page') ;
		if($post_hierarchy == '1') {
			$related_post_type = wp_get_post_tags($post->ID);
			$tag_arr = '';
			if($related_post_type) {
				foreach($related_post_type as $tag) { $tag_arr .= $tag->slug . ','; }
		        $args = array(
		        	'tag' => esc_html($tag_arr),
		        	'numberposts' => intval( $relatedposts_per_page ), /* you can change this to show more */
		        	'post__not_in' => array($post->ID)
		     	);
		   }
		}else {
			$related_post_type = get_the_category($post->ID); 
			if ($related_post_type) {
				$category_ids = array();
				foreach($related_post_type as $category) {
				     $category_ids = $category->term_id; 
				}  
				$args = array(
					'category__in' => absint($category_ids),
					'post__not_in' => array($post->ID),
					'numberposts' => intval($relatedposts_per_page),
		        );
		    }
		}
		if( $related_post_type ) {
	        $related_posts = get_posts($args);
	        if($related_posts) {
	        	foreach ($related_posts as $post) : setup_postdata($post); ?>
		           	<li class="related_post">
		           		<a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('frolic-recent-work'); ?></a>
		           		<a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		           	</li>
		        <?php endforeach; }
		    else {
	            echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'frolic' ) . '</li>'; 
			 }
		}else{
			echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'frolic' ) . '</li>';
		}
		wp_reset_postdata();
		
		echo '</ul>';
	}
endif;

if ( ! function_exists( 'frolic_comments_meta' ) ) :
	function frolic_comments_meta() {
		echo frolic_get_comments_meta();	
	}  
endif;  

if ( ! function_exists( 'frolic_get_comments_meta' ) ) :
	function frolic_get_comments_meta() {			
		$num_comments = get_comments_number(); // get_comments_number returns only a numeric value
 
		if ( comments_open() ) {
		  if ( $num_comments == 0 ) {
		    $comments = __('No Comments','frolic');
		  } elseif ( $num_comments > 1 ) {
		    $comments = $num_comments . __(' Comments','frolic');
		  } else {
		    $comments = __('1 Comment','frolic');  
		  }
		  $write_comments = '<span class="comments-link"><i class="fa fa-comments"></i><a href="' . esc_url(get_comments_link()) .'">'. esc_html($comments).'</a></span>';
		} else{
			$write_comments = '<span class="comments-link"><i class="fa fa-comments"></i><a href="' . esc_url(get_comments_link()) .'">'. esc_html(__('Leave a comment', 'frolic') ).'</a></span>';
		}
		return $write_comments;	
	}

endif;



/*  Site Layout Option  */
if ( ! function_exists( 'frolic_layout_class' ) ) :
	function frolic_layout_class() {
	     $sidebar_position = get_theme_mod( 'sidebar_position', 'right' ); 
		     if( 'fullwidth' == $sidebar_position ) {
		     	echo 'sixteen';
		     }else{
		     	echo 'eleven';
		     }
		     if ( 'no-sidebar' == $sidebar_position ) {
		     	echo ' no-sidebar';
		     }
	}
endif;

/* More tag wrapper */
add_action( 'the_content_more_link', 'frolic_add_more_link_class', 10, 2 );
if ( ! function_exists( 'frolic_add_more_link_class' ) ) :
	function frolic_add_more_link_class($link, $text ) {
		return '<p class="portfolio-readmore"><a class="btn btn-mini more-link" href="'. esc_url(get_permalink()) .'"><i class="fa fa-chevron-circle-right"></i>'.__('ReadMore','frolic').'</a></p>';
	}
endif;

/* Header video */
add_action('frolic_before_header','frolic_before_header_video');
if(!function_exists('frolic_before_header_video')){
	function frolic_before_header_video() {
		if(function_exists('the_custom_header_markup') ) { ?>
		    <div class="custom-header-media">
				<?php the_custom_header_markup(); ?>
			</div>
	    <?php } 
	}
}

/* Admin notice */
/* Activation notice */
add_action( 'load-themes.php',  'frolic_one_activation_admin_notice'  );

if( !function_exists('frolic_one_activation_admin_notice') ) {
	function frolic_one_activation_admin_notice() {
        global $pagenow;
	    if ( is_admin() && ('themes.php' == $pagenow) && isset( $_GET['activated'] ) ) {
	        add_action( 'admin_notices', 'frolic_admin_notice' );
	    } 
	} 
}  

/**
 * Add admin notice when active theme
 *
 * @return bool|null
 */
function frolic_admin_notice() { ?>
    <div class="updated notice notice-alt notice-success is-dismissible">  
        <p><?php printf( __( 'Welcome! Thank you for choosing %1$s! To fully take advantage of the best our theme can offer please make sure you visit our <a href="%2$s">Welcome page</a>', 'frolic' ), 'Frolic', esc_url( admin_url( 'themes.php?page=frolic_upgrade' ) ) ); ?></p>
    	<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=frolic_upgrade' ) ); ?>" class="button" style="text-decoration: none;"><?php _e( 'Get started with Frolic', 'frolic' ); ?></a></p>
    </div><?php
}