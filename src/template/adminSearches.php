<?php

	$searches_count = count($searches);

?>


<div id="wnt-sb" class="wrap">

	<div id="icon-options-wolfnet" class="icon32"><br /></div>

	<h1 class="wp-heading-inline">Saved Searches</h1>
	<a href="<?php echo $search_urls['new']; ?>" class="page-title-action"><?php _e('Add New'); ?></a>

	<div class="tablenav top">
		<div class="tablenav-pages one-page">
			<span class="displaying-num">
				<?php _e(sprintf('%d item%s', $searches_count, ($searches_count != 1 ? 's' : ''))); ?>
			</span>
		</div>
		<br class="clear" />
	</div>

	<h2 class="screen-reader-text">Saved Searches list</h2>

	<table class="wp-list-table widefat fixed striped posts">

		<thead>
			<tr>
				<td class="manage-column column-cb check-column"></td>
				<th scope="col" id="title" class="manage-column column-title column-primary"><?php _e('Title'); ?></th>
				<th scope="col" id="author" class="manage-column column-author"><?php _e('Author'); ?></th>
				<th scope="col" id="wnt_market" class="manage-column column-rel"><?php _e('Market'); ?></th>
				<th scope="col" id="date" class="manage-column column-date"><?php _e('Date'); ?></th>
			</tr>
		</thead>

		<tbody id="the-list">

			<?php foreach($searches as $search): ?>

				<tr id="<?php echo sprintf('post-%d', $search['ID']); ?>"
				 class="<?php echo sprintf('post-%d', $search['ID']); ?> iedit author-self level-0 type-wolfnet_search status-publish hentry">
					<th scope="row" class="check-column"></th>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
						<strong>
							<a class="row-title"
							 href="<?php echo sprintf($search_urls['edit'], $search['ID']); ?>"
							 aria-label="<?php _e(sprintf('"%s" (Edit)', $search['post_title'])); ?>"><?php _e($search['post_title']); ?></a>
						</strong>
						<div class="hidden" id="<?php echo sprintf('inline_%d', $search['ID']); ?>">
							<div class="post_title"><?php _e($search['post_title']); ?></div>
							<div class="post_name"><?php echo $search['post_name']; ?></div>
							<div class="post_author"><?php echo $search['post_author']; ?></div>
							<div class="comment_status"><?php echo $search['comment_status']; ?></div>
							<div class="ping_status"><?php echo $search['ping_status']; ?></div>
							<div class="_status"><?php echo $search['post_status']; ?></div>
							<div class="post_date"><?php echo $search['post_date']; ?></div>
							<div class="post_modified"><?php echo $search['post_modified']; ?></div>
							<div class="post_password"><?php echo $search['post_password']; ?></div>
							<div class="page_template"><?php echo $search['page_template']; ?></div>
						</div>
						<div class="row-actions">
							<span class="edit">
								<a href="<?php echo sprintf($search_urls['edit'], $search['ID']); ?>"
								 aria-label="<?php _e(sprintf('Edit "%s"', $search['ID'])); ?>"><?php _e('Edit'); ?></a> |
							</span>
							<span class="trash">
								<a href="<?php echo sprintf($search_urls['trash'], $search['ID']); ?>" class="submitdelete"
								 aria-label="<?php _e(sprintf('Move "%s" to the Trash', $search['ID'])); ?>"><?php _e('Trash'); ?></a>
							</span>
						</div>
						<button type="button" class="toggle-row">
							<span class="screen-reader-text"><?php _e('Show more details'); ?></span>
						</button>
					</td>
					<td class="author column-author" data-colname="Author">
						<?php echo $search['user_login']; ?>
					</td>
					<td class="date column-rel" data-colname="Market">
						<?php echo $search['key_market']; ?>
					</td>
					<td class="date column-date" data-colname="Date">
						<?php echo date('n/j/Y g:i a', strtotime($search['post_modified'])); ?>
					</td>
				</tr>

			<?php endforeach; ?>

		</tbody>

		<tfoot>
			<tr>
				<td class="manage-column column-cb check-column"></td>
				<th scope="col" class="manage-column column-title column-primary"><?php _e('Title'); ?></th>
				<th scope="col" class="manage-column column-author"><?php _e('Author'); ?></th>
				<th scope="col" class="manage-column column-rel"><?php _e('Market'); ?></th>
				<th scope="col" class="manage-column column-date"><?php _e('Date'); ?></th>
			</tr>
		</tfoot>

	</table>

	<div class="tablenav bottom">
		<div class="alignleft actions"></div>
		<div class="tablenav-pages one-page">
			<span class="displaying-num">
				<?php _e(sprintf('%d item%s', $searches_count, ($searches_count != 1 ? 's' : ''))); ?>
			</span>
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
