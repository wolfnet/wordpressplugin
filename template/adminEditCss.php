<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet - Edit CSS</h2>

    <form method="post" action="options.php">

    	<?php echo $formHeader; ?>

    	<fieldset>

    		<legend><h3>Edit CSS</h3></legend>

            <table class="form-table">

            	<tr>
            		<th scope="col"><label for="wolfnetCss_adminCss">Admin CSS</label></th>
            		<th scope="col"><label for="wolfnetCss_publicCss">Public CSS</label></th>
            	</tr>

            	<tr>
            		<td>
            			<?php /* <div id="wolfnet_adminCss" class="wolfnet_cssBox">
            				<?php echo $adminCss; ?>
            			</div> */ ?>
                        <p class="wolfnet_note">This CSS will affect your Wordpress admin (the pages you are looking at now).</p>
            			<textarea id="wolfnetCss_adminCss" name="wolfnetCss_adminCss" class="wolfnet_cssBox"><?php echo $adminCss; ?></textarea>
            		</td>
            		<td>
            			<?php /* <div id="wolfnet_publicCss" class="wolfnet_cssBox">
            				<?php echo $publicCss; ?>
            			</div> */ ?>
                        <p class="wolfnet_note">This CSS will affect the public-facing pages of your web site.</p>
            			<textarea id="wolfnetCss_publicCss" name="wolfnetCss_publicCss" class="wolfnet_cssBox"><?php echo $publicCss; ?></textarea>
            		</td>
            	</tr>

            	<tr>
            		<td colspan="2" class="submit">
            			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            		</td>
            	</tr>

            </table>

    	</fieldset>

    </form>

</div>

<?php /* <script src="<?php echo $this->url; ?>/js/ace/ace-1.1.01.js" type="text/javascript" charset="utf-8"></script>
<script>
	ace.config.set("basePath", "<?php echo $this->url; ?>js/ace");

	var adminCss = ace.edit("wolfnet_adminCss");
	adminCss.setTheme("ace/theme/monokai");
	adminCss.getSession().setMode("ace/mode/css");

	var publicCss = ace.edit("wolfnet_publicCss");
	publicCss.setTheme("ace/theme/monokai");
	publicCss.getSession().setMode("ace/mode/css");
</script> */ ?>