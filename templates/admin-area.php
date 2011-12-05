<div id="cursorial-admin" class="wrap">
	<div id="icon-themes" class="icon32"><br/></div>
	<h2><?php printf( __( 'Cursorial Area %s', 'cursorial' ), $area->label ); ?></h2>

	<div class="widget-liquid-left">
		<div id="widgets-left">
			<div class="widgets-holder-wrap">
				<div id="nav-menu-header" class="sidebar-name">
					<div class="publishing-actions">
						<input type="submit" value="<?php _e( 'Save Area', 'cursorial' ); ?>" class="button-primary cursorial-area-save" id="" name="save_area" />
					</div>
					<h3><?php echo $area->label; ?></h3>
					<div class="clear"></div>
				</div>
				<div class="widget-holder">
					<p class="description"><?php _e( "Here's a list of content. To add content into this list, search content in the box to the right.", 'cursorial' ); ?></p>
					<div id="widget-list" class="cursorial-area cursorial-area-<?php echo $area->name; ?>"></div>
					<div class="clear"></div>
					<div class="publishing-actions">
						<input type="submit" value="<?php _e( 'Save Area', 'cursorial' ); ?>" class="button-primary cursorial-area-save" id="" name="save_area" />
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="widget-liquid-right">
		<div id="widgets-right">
			<div id="cursorial-search" class="widgets-holder-wrap">
				<div class="sidebar-name">
					<h3>
						<span><?php _e( 'Find content', 'cursorial' ); ?></span>
					</h3>
				</div>
				<div class="widgets-sortables">
					<div id="cursorial-search-form" class="widget">
						<div class="widget-inside">
							<p class="description"><?php _e( 'Enter keywords below to find content to add into the cursorial area.', 'cursorial' ); ?></p>
							<label for="cursorial-search-field"><?php _e( 'Search keywords:', 'cursorial' ); ?></label>
							<input id="cursorial-search-field" class="widefat" type="text" value="" name="query" />
						</div>
					</div>
					<div id="cursorial-search-result">
						<div class="template widget">
							<div class="widget-top">
								<div class="widget-title">
									<h4 class="post-title"><span class="template-data-post_title"></span></h4>
								</div>
							</div>
							<div class="widget-inside">
								<p class="post-meta">
									<?php _e( 'Author:', 'cursorial' ); ?> <span class="template-data-post_author"></span><br/>
									<?php _e( 'Date:', 'cursorial' ); ?> <span class="template-data-post_date"></span>
								</p>
								<p class="post-excerpt template-data-post_excerpt"></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
