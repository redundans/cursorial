<?php global $cursorial, $cursorial_admin; ?>
<script language="javascript" type="text/javascript">
	jQuery( function( $ ) {
		// Setup cursorial search field
		$( 'input#cursorial-search-field' ).cursorialSearch( {
			templates: {
				post: '#cursorial-search-result .template' // Where to find a template to use when render posts
																									 // To render post-fields you'll also have to define
																									 // elements inside this element with two classes:
																									 // template-data and template-data-{field-name}
			},
			timeout: 1000, // For how long wait until search-query is posted to server
			target: '#cursorial-search-result', // Where to place rendered posts
			blocks: '.cursorial-block .cursorial-posts' // Where to find blocks to connect posts to
		} );

		$( 'input#cursorial-blog-search' ).cursorialBlogSearch( {
			templates: {
				blog: '#cursorial-blog-search-result .template'
			},
			timeout: 1000,
			target: '#cursorial-blog-search-result',
			search: '#cursorial-search-field'
		} );

		// Setup cursorial blocks
		$( '.cursorial-block' ).cursorialBlock( {
			templates: {
				post: '#cursorial-search-result .template' // Where to find a template to use when render posts
			},
			buttons: {
				save: 'input.cursorial-block-save', // The save button
				post_edit: 'input.cursorial-post-edit', // Post edit buttons
				post_save: 'input.cursorial-post-save', // Post save buttons
				post_cancel: 'input.cursorial-post-cancel', // Post cancel buttons
				post_remove: 'a.cursorial-post-remove' // Post remove buttons
			},
			target: '.cursorial-posts', // Where to place posts
			blocks: '.cursorial-block .cursorial-posts', // Where to find other blocks to connect posts to
			statusIndicator: '#cursorial-block-status-list' // Where to place statuses for blocks
		} );

		$( 'input.cursorial-block-saveall' ).click( function() {
			$( '.cursorial-block' ).cursorialBlock( 'save' );
		} );


	} );
</script>
<?php if ( version_compare( $GLOBALS['wp_version'], '3.8-alpha', '>' ) ) $class = 'cursorial-3-8'; ?>

<div id="cursorial-admin" class="wrap <?php echo $class; ?>">
	<div id="icon-themes" class="icon32"><br/></div>
	<h2><?php printf( __( 'Cursorial » %s', 'cursorial' ), $cursorial_admin->label ); ?></h2>

	<div class="widget-liquid-left">
		<div id="widgets-left">
			<div class="widgets-holder-wrap">

				<table class="cursorial-blocks-table">
					<tbody>

						<?php for( $r = 0; $r < $cursorial_admin->get_rows(); $r++ ) : ?>
							<tr>

								<?php for( $c = 0; $c < $cursorial_admin->get_cols(); $c++ ) :
									$block = $cursorial_admin->get_grid( $r, $c );
									if (
										$block !== null
										&& $block !== $cursorial_admin->get_grid( $r, $c - 1 )
										&& $block !== $cursorial_admin->get_grid( $r - 1, $c )
									) : ?>
										<td width="<?php echo ( 100 / $cursorial_admin->get_cols() ) * $block->settings[ 'width' ]; ?>%" colspan="<?php echo $block->settings[ 'width' ]; ?>" rowspan="<?php echo $block->settings[ 'height' ]; ?>">

											<?php if ( $block->block ) : ?>
												<div id="cursorial-block-<?php echo $block->block->name; ?>" class="cursorial-block cursorial-block-<?php echo $block->block->name; ?>">
													<div class="sidebar-name cursorial-block-name">
														<div class="publishing-actions">
															<input type="submit" value="<?php _e( 'Save block', 'cursorial' ); ?>" class="button-primary cursorial-block-save" name="save_block" />
														</div>
														<h3><?php echo $block->block->label; ?></h3>
														<div class="clear"></div>
													</div><!-- .cursorial-block-name -->
													<div class="widget-holder cursorial-block-content">
														<p class="description"><?php _e( "Here's a list of posts. To add posts to this list, search for content in the box to the right and drag them into this space. You can also drag posts between blocks.", 'cursorial' ); ?></p>
														<div class="cursorial-posts"></div>
														<div class="clear"></div>
														<div class="publishing-actions">
															<input type="submit" value="<?php _e( 'Save block', 'cursorial' ); ?>" class="button-primary cursorial-block-save" name="save_block" />
														</div>
														<div class="clear"></div>
													</div><!-- .cursorial-block-content -->
												</div><!-- .cursorial-block -->
											<?php else : ?>
												<div class="sidebar-name">
													<h3><?php echo isset( $block->settings[ 'dummy-title' ] ) ? $block->settings[ 'dummy-title' ] : __( 'Dummy block', 'cursorial' ); ?></h3>
												</div>
												<div class="widget-holder">
													<p class="description"><?php echo isset( $block->settings[ 'dummy-description' ] ) ? $block->settings[ 'dummy-description' ] : __( 'This is a dummy block occupying space.', 'cursorial' ); ?></p>
												</div>
											<?php endif; ?>

										</td>
									<?php endif; ?>

								<?php endfor; ?>

							</tr>
						<?php endfor; ?>

					</tbody>
				</table><!-- table.cursorial-block-table -->

			</div><!-- .widgets-holder-wrap -->
		</div><!-- #widgets-left -->
	</div><!-- .widget-liquid-left -->

	<div class="widget-liquid-right">
		<div id="widgets-right">
			<div id="cursorial-block-actions" class="widgets-holder-wrap">
				<div class="sidebar-name">
					<h3>
						<span><?php _e( 'Publish', 'cursorial' ); ?></span>
					</h3>
				</div><!-- .sidebar-name -->
				<div class="widgets-sortables widget-holder">
					<ul id="cursorial-block-status-list"></ul>
					<div class="publishing-actions">
						<input type="submit" value="<?php _e( 'Save all blocks', 'cursorial' ); ?>" class="button-primary cursorial-block-saveall" name="save_block" />
					</div>
					<div class="clear"></div>
				</div><!-- .widgets-sortables -->
			</div><!-- #cursorial-block-saving -->

			<div id="cursorial-search" class="widgets-holder-wrap">
				<div class="sidebar-name">
					<h3>
						<span><?php _e( 'Find content', 'cursorial' ); ?></span>
					</h3>
				</div><!-- .sidebar-name -->
				<div class="widgets-sortables">
					<div id="cursorial-search-form" class="widget">
						<div class="widget-inside">
							<p class="description"><?php _e( 'Enter keywords below to find content. Then drag the posts to a block and drop them there.', 'cursorial' ); ?></p>
							<label for="cursorial-blog-search-field"><?php _e( 'Blog search:', 'cursorial' ); ?></label>
							<input id="cursorial-blog-search" class="widefat" type="text" value="<?php echo preg_replace('/^http:\/\/(.+?)\/$/', '$1', get_blogaddress_by_id( $cursorial->get_default_blog_id() ) ); ?>" name="blogname" />
							<ul id="cursorial-blog-search-result">
								<li class="template">
									<input type="radio" name="blogid" class="template-data template-data-blog_id" />
									<span class="template-data template-data-blogname"></span>
								</li>
							</ul>
							<label for="cursorial-blog-search"><?php _e( 'Search keywords:', 'cursorial' ); ?></label>
							<input id="cursorial-search-field" class="widefat" type="text" value="" name="query" />
						</div><!-- .widget-inside -->
					</div><!-- #cursorial-search-form -->
					<div id="cursorial-search-result">
						<div class="template widget">
							<div class="widget-top">
								<div class="widget-title">
									<h4 class="post-title"><span class="template-data-post_title"></span></h4>
								</div>
							</div><!-- .widget-top -->
							<div class="widget-inside">
								<p class="post-image template-data template-data-image"></p>
								<p class="post-meta">
									<span class="template-data"><?php _e( 'Blog:', 'cursorial' ); ?> <span class="template-data-blogname"></span></span><br/>
									<span class="template-data"><?php _e( 'Author:', 'cursorial' ); ?> <span class="template-data-post_author"></span></span><br/>
									<span class="template-data"><?php _e( 'Date:', 'cursorial' ); ?> <span class="template-data-post_date"></span></span>
								</p>
								<div class="post-excerpt template-data template-data-post_excerpt"></div>
								<div class="post-excerpt template-data template-data-ghost_permalink"></div>
								<div class="widget-control-actions">
									<div class="alignleft">
										<a class="widget-control-remove cursorial-post-remove" href="#remove"><span><?php _e( 'Remove', 'cursorial' ); ?></span></a>
									</div>
									<div class="alignright">
										<input class="button-primary widget-control-save cursorial-post-edit" type="submit" value="<?php _e( 'Edit', 'cursorial' ); ?>" name="edit"/>
										<input class="button widget-control-cancel cursorial-post-cancel" type="button" value="<?php _e( 'Cancel', 'cursorial' ); ?>" name="cancel"/>
										<input class="button-primary widget-control-save cursorial-post-save" type="submit" value="<?php _e( 'Save', 'cursorial' ); ?>" name="edit"/>
									</div>
									<div class="clear"></div>
								</div><!-- .widget-control-actions -->
							</div><!-- .widget-inside -->
						</div><!-- .template -->
					</div><!-- #cursorial-search-result -->
				</div><!-- .widgets-sortables -->
			</div><!-- #cursorial-search -->

				<div id="cursorial-create" class="postbox ">
					<h3 class="hndle"><span><?php _e( 'Create content', 'cursorial' ); ?></span></h3>
					<div class="inside">
						<form id="create_cursorial">

						<label for="post_title"><?php _e( 'Title', 'cursorial' ); ?>
						<input type="text" id="post_title" name="post_title" class="widefat" autocomplete="off" value="">
						</label>

						<label for="upload_image">
							<div id="upload_image_thumbnail"></div>
							<input id="upload_image" type="hidden" size="36" name="ad_image" value="http://" />
							<p><a id="upload_image_button"><?php _e( 'Choose image', 'cursorial' ); ?></a></p>
						</label>

						<label for="post_content"><?php _e( 'Description', 'cursorial' ); ?>
						<textarea id="post_content" name="post_content" class="widefat" autocomplete="off" value=""></textarea>
						</label>

						<label for="post_guid"><?php _e( 'Link', 'cursorial' ); ?>
						<input type="text" id="post_guid" name="post_guid" class="widefat" autocomplete="off" value="">
						</label>

						<input type="submit" id="submit_post" name="submit_post" class="button button-primary button-large" value="<?php _e( 'Submit', 'cursorial' ); ?>">

						</form>
						<div class="clear"></div>
					</div>
				</div><!-- #cursorial-create -->

		</div><!-- #widgets-right -->
	</div><!-- .widgets-liquid-right -->

</div><!-- #cursorial-admin -->
