
<div id="wnt-sb" class="wrap">

	<div id="icon-options-wolfnet" class="icon32"><br /></div>

	<h1 class="wp-heading-inline">Search Manager - WolfNet<sup>&reg;</sup></h1>
	<a href="<?php echo $wnt_url_new; ?>" class="page-title-action"><?php _e('Add New'); ?></a>

	<?php var_dump($wnt_searches); ?>

		<div class="tablenav top">
			<div class="tablenav-pages one-page">
				<span class="displaying-num">2 items</span>
			</div>
			<br class="clear" />
		</div>

		<h2 class="screen-reader-text">Saved Searches list</h2>

		<table class="wp-list-table widefat fixed striped posts">

			<thead>
				<tr>
					<td class="manage-column column-cb check-column"></td>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="http://192.168.50.100/wp-admin/edit.php?post_type=wolfnet_search&amp;orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="keyid" class="manage-column column-rel sortable desc">
						<a href="http://192.168.50.100/wp-admin/edit.php?post_type=wolfnet_search&amp;orderby=keyid&amp;order=asc"><span>Date</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="date" class="manage-column column-date sortable asc">
						<a href="http://192.168.50.100/wp-admin/edit.php?post_type=wolfnet_search&amp;orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a>
					</th>
				</tr>
			</thead>

			<tbody id="the-list">

				<?php foreach($wnt_searches as $wnt_search): ?>

					<tr id="<?php echo sprintf('post-%d', $wnt_search->ID); ?>"
					 class="<?php echo sprintf('post-%d', $wnt_search->ID); ?> iedit author-self level-0 type-wolfnet_search status-publish hentry">
						<th scope="row" class="check-column"></th>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
							<strong>
								<a class="row-title"
								 href="<?php echo sprintf('?wnt_action=edit&post=%d', $wnt_search->ID); ?>"
								 aria-label="<?php _e(sprintf('"%s" (Edit)', $wnt_search->post_title)); ?>"><?php _e($wnt_search->post_title); ?></a>
							</strong>
							<div class="hidden" id="<?php echo sprintf('inline_%d', $wnt_search->ID); ?>">
								<div class="post_title"><?php _e($wnt_search->post_title); ?></div>
								<div class="post_name"><?php echo $wnt_search->post_name; ?></div>
								<div class="post_author"><?php echo $wnt_search->post_author; ?></div>
								<div class="comment_status"><?php echo $wnt_search->comment_status; ?></div>
								<div class="ping_status"><?php echo $wnt_search->ping_status; ?></div>
								<div class="_status"><?php echo $wnt_search->post_status; ?></div>
								<div class="post_date"><?php echo $wnt_search->post_date; ?></div>
								<div class="post_modified"><?php echo $wnt_search->post_modified; ?></div>
								<div class="post_password"><?php echo $wnt_search->post_password; ?></div>
								<div class="page_template"><?php echo $wnt_search->page_template; ?></div>
							</div>
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo sprintf('?post=%d&amp;action=edit', $wnt_search->ID); ?>"
									 aria-label="<?php _e(sprintf('Edit "%s"', $wnt_search->ID)); ?>"><?php _e('Edit'); ?></a> |
								</span>
								<span class="trash">
									<a href="<?php echo sprintf('?post=%d&amp;action=trash', $wnt_search->ID); ?>" class="submitdelete"
									 aria-label="<?php _e(sprintf('Move "%s" to the Trash', $wnt_search->ID)); ?>"><?php _e('Trash'); ?></a>
								</span>
							</div>
							<button type="button" class="toggle-row">
								<span class="screen-reader-text"><?php _e('Show more details'); ?></span>
							</button>
						</td>
						<td class="date column-date" data-colname="Date">
							<?php echo date('n/j/Y g:i a', strtotime($wnt_search->post_modified)); ?>
						</td>
					</tr>

				<?php endforeach; ?>


				<tr id="post-25" class="iedit author-self level-0 post-25 type-wolfnet_search status-publish hentry">
					<th scope="row" class="check-column"></th>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
						<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
						<strong><a class="row-title" href="http://192.168.50.100/wp-admin/post.php?post=25&amp;action=edit" aria-label="“office-only” (Edit)">office-only</a></strong>
						<div class="hidden" id="inline_25">
							<div class="post_title">office-only</div>
							<div class="post_name">office-only</div>
							<div class="post_author">1</div>
							<div class="comment_status">closed</div>
							<div class="ping_status">closed</div>
							<div class="_status">publish</div>
							<div class="jj">24</div>
							<div class="mm">04</div>
							<div class="aa">2018</div>
							<div class="hh">20</div>
							<div class="mn">07</div>
							<div class="ss">56</div>
							<div class="post_password"></div>
							<div class="page_template">default</div>
							<div class="sticky"></div>
						</div>
						<div class="row-actions">
							<span class="edit">
								<a href="http://192.168.50.100/wp-admin/post.php?post=25&amp;action=edit" aria-label="Edit “office-only”">Edit</a> |
							</span>
							<span class="inline hide-if-no-js">
								<button type="button" class="button-link editinline" aria-label="Quick edit “office-only” inline" aria-expanded="false">Quick&nbsp;Edit</button> |
							</span>
							<span class="trash">
								<a href="http://192.168.50.100/wp-admin/post.php?post=25&amp;action=trash&amp;_wpnonce=754a695073" class="submitdelete" aria-label="Move “office-only” to the Trash">Trash</a>
							</span>
						</div>
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
					</td>
					<td class="date column-date" data-colname="Date">
						Published<br />
						<abbr title="2018/04/24 8:07:56 pm">1 min ago</abbr>
					</td>
				</tr>
				<tr id="post-23" class="iedit author-self level-0 post-23 type-wolfnet_search status-publish hentry">
					<th scope="row" class="check-column"></th>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
						<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
						<strong><a class="row-title" href="http://192.168.50.100/wp-admin/post.php?post=23&amp;action=edit" aria-label="“plain-ol-search” (Edit)">plain-ol-search</a></strong>

						<div class="hidden" id="inline_23">
							<div class="post_title">plain-ol-search</div>
							<div class="post_name">plain-ol-search</div>
							<div class="post_author">1</div>
							<div class="comment_status">closed</div>
							<div class="ping_status">closed</div>
							<div class="_status">publish</div>
							<div class="jj">24</div>
							<div class="mm">04</div>
							<div class="aa">2018</div>
							<div class="hh">20</div>
							<div class="mn">06</div>
							<div class="ss">29</div>
							<div class="post_password"></div>
							<div class="page_template">default</div>
							<div class="sticky"></div>
						</div>
						<div class="row-actions">
							<span class="edit">
								<a href="http://192.168.50.100/wp-admin/post.php?post=23&amp;action=edit" aria-label="Edit “plain-ol-search”">Edit</a> |
							</span>
							<span class="inline hide-if-no-js">
								<button type="button" class="button-link editinline" aria-label="Quick edit “plain-ol-search” inline" aria-expanded="false">Quick&nbsp;Edit</button> |
							</span>
							<span class="trash">
								<a href="http://192.168.50.100/wp-admin/post.php?post=23&amp;action=trash&amp;_wpnonce=728a4eecdd" class="submitdelete" aria-label="Move “plain-ol-search” to the Trash">Trash</a>
							</span>
						</div>
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
					</td>
					<td class="date column-date" data-colname="Date">
						Published<br />
						<abbr title="2018/04/24 8:06:29 pm">2 mins ago</abbr>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column"></td>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="http://192.168.50.100/wp-admin/edit.php?post_type=wolfnet_search&amp;orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" class="manage-column column-date sortable asc">
						<a href="http://192.168.50.100/wp-admin/edit.php?post_type=wolfnet_search&amp;orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a>
					</th>
				</tr>
			</tfoot>
		</table>

		<div class="tablenav bottom">
			<div class="alignleft actions"></div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">2 items</span>
			</div>
			<br class="clear" />
		</div>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

		});

	}

</script>
