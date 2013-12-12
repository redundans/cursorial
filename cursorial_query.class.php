<?php

/**
 * Class that handles all the Cursorial wp-queries
 */
class Cursorial_Query {
	/**
	 * Contains all results
	 */
	public $results = array();

	/**
	 * Search query keyword terms
	 */
	public $search_keywords = array();

	/**
	 * Maximum number of posts search result
	 */
	public $search_numberposts = 10;

	/**
	 * Creates an argument array for wp_query.
	 * @param string $block_name The name of the block
	 * @return object
	 */
	public static function get_block_query_args( $block_name ) {
		return array(
			'post_type' => Cursorial::POST_TYPE,
			'tax_query' => array(
				array(
					'taxonomy' => Cursorial::TAXONOMY,
					'field' => 'slug',
					'terms' => array( $block_name )
				)
			),
			'orderby' => 'date'
		);
	}

	/**
	 * Populates the result-array with given post.
	 * It skips already populated posts
	 *
	 * @param object $post Post from a wp_query
	 * @return void
	 */
	private function populate_results( $post, $blog_id = 0 ) {
		global $wpdb;

		foreach( $this->results as $result ) {
			if ( $result->ID === $post->ID ) {
				return;
			}
		}

		if ( ! $blog_id ) {
			$blog_id = $wpdb->blogid;
		}

		setup_postdata( $post );

		if ( preg_match( '/^([0-9]+),([0-9]+)$/', $post->guid, $guid ) ) {
			$post->ID = $guid[ 2 ];
			$post->blogid = $guid[ 1 ];
		} else {
			$post->blogid = get_post_meta( $post_id, 'cursorial-blog-id', true );
		}

		$post_id = property_exists( $post, 'cursorial_ID' ) ? $post->cursorial_ID : $post->ID;
		$post->post_title = apply_filters( 'the_title', $post->post_title );
		$post->post_author = get_the_author();
		$post->post_date = apply_filters( 'the_date', $post->post_date );
		$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
		$post->post_content = apply_filters( 'the_content', $post->post_content );
		$post->image = apply_filters( 'cursorial_image_id', get_post_thumbnail_id( $post_id ) );
		$post->cursorial_depth = apply_filters( 'cursorial_depth', ( int ) get_post_meta( $post_id, 'cursorial-post-depth', true ) );

		if ( ! $post->blogid ) {
			$post->blogid = $blog_id;
		}

		if ( $post->blogid !== $wpdb->blogid ) {
			switch_to_blog( $post->blogid );

			if ( ! property_exists( $post, 'cursorial_ID' ) ) {
				$post->image = get_post_thumbnail_id( $post_id );
			}

			$post->cursorial_image = wp_get_attachment_image_src( $post->image );
			restore_current_blog();
		} else {
			$post->cursorial_image = wp_get_attachment_image_src( $post->image );
		}

		$post->blogname = get_blog_option( $post->blogid, 'blogname' );
		$ref_id = get_post_meta( $post_id, 'cursorial-post-id', true );

		if ( $ref_id && $post->post_type == Cursorial::POST_TYPE ) {
			if ( $post->blogid !== $wpdb->blogid ) {
				switch_to_blog( $post->blogid );
				$original = get_post( $ref_id );
				restore_current_blog();
			} else {
				$original = get_post( $ref_id );
			}

			if ( $original ) {
				$post->post_type = $original->post_type;
				$post->post_permalink = get_permalink( $original );
				$post->ghost_permalink = get_post_meta( $ref_id, 'ghost_link', TRUE );
			}
		} else {
			$post->post_permalink = get_permalink( $post_id );
			$post->ghost_permalink = get_post_meta( $post_id, 'ghost_link', TRUE );
		}

		$hidden_fields = get_post_meta( $post_id, 'cursorial-post-hidden-fields', true );

		if ( is_array( $hidden_fields ) ) {
			foreach( $hidden_fields as $field_name ) {
				$hidden_field_name = $field_name . '_hidden';
				$post->$hidden_field_name = true;
			}
		}

		$this->results[] = $post;
	}

	/**
	 * Modifies the wp_query-where-string with search keywords
	 *
	 * @param string $where The SQL-where-string to modify
	 * @return string SQL-where-string
	 */
	public function post_title_filter( $where = '' ) {
		foreach ( $this->search_keywords as $term ) {
			$where .= sprintf( ' AND post_title LIKE "%%%s%%"', preg_replace( '/[\'\"]/', '', $term ) );
		}
		return $where;
	}

	/**
	 * Populates the result-array with a search-query.
	 * The search is split into four different queries, each with it's own priority order.
	 * * Priority #1: title, match words in title
	 * * Priority #2: category taxonomy, match category names
	 * * Priority #3: tags taxonomy, match tag names
	 * * Priority #4: author, match author name
	 *
	 * @param string $terms Search keywords
	 * @return void
	 */
	public function search( $terms, $blog_id = 0 ) {
		global $cursorial, $wpdb;

		if ( count( $this->results ) >= $this->search_numberposts ) {
			return;
		}

		if ( ! $blog_id ) {
			$blog_id = $cursorial->get_default_blog_id();
		}

		$this->search_keywords = explode( ' ', trim( $terms ) );

		if ( preg_match( '/^http(:?s)?:\/\//i', $this->search_keywords[ 0 ] ) ) {
			$cursorial_search_id = url_to_postid( $this->search_keywords[ 0 ] );
		}

		if ( isset( $cursorial_search_id ) && $cursorial_search_id ) {
			$post = get_post( $cursorial_search_id );

			if ( $post->post_type != Cursorial::POST_TYPE ) {
				$this->populate_results( $post );
			}
		} else {
			$date = strtotime( $terms );

			foreach ( array(
				'title' => 'post_title_filter',
				'category' => array(
					'tax_query' => array(
						array(
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => $this->search_keywords
						)
					)
				),
				'tags' => array(
					'tax_query' => array(
						array(
							'taxonomy' => 'post_tag',
							'field' => 'slug',
							'terms' => $this->search_keywords
						)
					)
				),
				'author' => array(
					'author_name' => implode( ',', $this->search_keywords )
				),
				array(
					'year' => date( 'Y', $date ),
					'monthnum' => date( 'n', $date ),
					'day' => date( 'j', $date ),
					'hour' => date( 'H', $date ),
					'minute' => date( 'i', $date )
				),
				array(
					'year' => date( 'Y', $date ),
					'monthnum' => date( 'n', $date ),
					'day' => date( 'j', $date ),
					'hour' => date( 'H', $date )
				),
				array(
					'year' => date( 'Y', $date ),
					'monthnum' => date( 'n', $date ),
					'day' => date( 'j', $date )
				),
				array(
					'year' => date( 'Y', $date ),
					'monthnum' => date( 'n', $date )
				),
				array(
					'year' => date( 'Y', $date )
				),
			) as $field => $args ) {
				switch_to_blog( $blog_id );

				if ( is_string( $args ) ) {
					add_filter( 'posts_where', array( &$this, $args ) );
					$query = new WP_Query( 'post_type=any' );
					$posts = $query->get_posts();
					remove_filter( 'posts_where', array( &$this, $args ) );
				} else {
					$args[ 'post_type' ] = 'any';
					$query = new WP_Query( $args );
					$posts = $query->get_posts();
				}

				restore_current_blog();

				foreach ( $posts as $post ) {
					if ( count( $this->results ) >= $this->search_numberposts ) {
						break;
					}

					if ( $post->post_type != Cursorial::POST_TYPE ) {
						$this->populate_results( $post, $blog_id );
					}
				}
			}
		}
	}

	public function blogs( $query = '' ) {
		global $wpdb;
		$current_blog_id = $wpdb->blogid;

		if( is_numeric( $query ) ) {
			$query = $wpdb->get_results( sprintf(
				'SELECT `blog_id`, `domain` FROM `%s` WHERE `blog_id` = "%d" AND `public` = "1" AND `archived` = "0"',
				addslashes( $wpdb->blogs ),
				addslashes( $query )
			), ARRAY_A );
		} elseif( ! empty( $query ) ) {
			$query = $wpdb->get_results( sprintf(
				'SELECT `blog_id`, `domain` FROM `%s` WHERE `domain` LIKE "%%%s%%" AND `public` = "1" AND `archived` = "0"',
				addslashes( $wpdb->blogs ),
				addslashes( $query )
			), ARRAY_A );
		}

		if( is_array( $query ) && ! empty( $query ) ) {
			foreach( $query as $blog ) {
				$wpdb->set_blog_id( $blog[ 'blog_id' ] );

				$subquery = $wpdb->get_results( sprintf(
					'SELECT `option_name`, `option_value` FROM `%s` WHERE `option_name` IN ( "siteurl", "blogname", "blogdescription" )',
					addslashes( $wpdb->options )
				), ARRAY_A );

				foreach( $subquery as $opt ) {
					$blog[ $opt[ 'option_name' ] ] = esc_attr( $opt[ 'option_value' ] );
				}

				$this->results[] = $blog;
			}

			$wpdb->set_blog_id( $current_blog_id );
		}
	}

	/**
	 * Makes a query for all posts in specified block and populates the results-array.
	 *
	 * @param string $block_name Block name
	 * @return void
	 */
	public function block( $block_name ) {
		$query = new WP_Query( self::get_block_query_args( $block_name ) );

		foreach ( $query->get_posts() as $post ) {
			$this->populate_results( $post );
		}
	}
}
