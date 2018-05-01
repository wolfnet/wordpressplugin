<?php

/**
 *
 * @title         adminSelectKey.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

?>

<div class="wrap wnt-wrap">

	<div id="icon-options-wolfnet" class="icon32"><br /></div>

	<h1>Select a Market</h1>

	<p><?php _e($instructions); ?></p>

	<?php for ($i=0; $i<=count($keys)-1; $i++): ?>
		<p>
			<a href="<?php echo esc_attr(sprintf('%s&keyid=%d', $next_url, $keys[$i]->id)); ?>"
			 class="button button-secondary">
				<span>
					<?php
						_e('Continue with');
						echo ' ' . ($keys[$i]->market ?: $keys[$i]->label);
					?>
				</span>
				<span class="dashicons dashicons-arrow-right"></span>
			</a>
		</p>
	<?php endfor; ?>

</div>
