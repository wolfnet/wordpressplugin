<style>
.wolfnet_cssBox {
	position: absolute;
	width: 500px;
	height: 600px;
}
</style>

<div class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet - Edit CSS</h2>

    <form method="post" action="options.php">

    	<?php echo $formHeader; ?>

    	<fieldset>

    		<legend><h3>Edit CSS</h3></legend>

            <table class="form-table">

            	<tr>
            		<th scope="col"><label for="wolfnet_adminCss">Admin CSS</label></th>
            		<th scope="col"><label for="wolfnet_publicCss">Public CSS</label></th>
            	</tr>

            	<tr>
            		<td>
            			<div id="wolfnet_adminCss" class="wolfnet_cssBox">
            				<?php echo $adminCss; ?>
            			</div>
            		</td>
            		<td>
            			<div id="wolfnet_publicCss" class="wolfnet_cssBox">
            				<?php echo $publicCss; ?>
            			</div>
            		</td>
            	</tr>

            	<tr valign="top" colspan="2">
                    <td class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </td>
                </tr>

            </table>

    	</fieldset>

    </form>

</div>

<script src="<?php echo $this->url; ?>/js/ace/ace-1.1.01.js" type="text/javascript" charset="utf-8"></script>
<script>
	ace.config.set("basePath", "<?php echo $this->url; ?>js/ace");

	var adminCss = ace.edit("wolfnet_adminCss");
	adminCss.setTheme("ace/theme/monokai");
	adminCss.getSession().setMode("ace/mode/css");

	var publicCss = ace.edit("wolfnet_publicCss");
	publicCss.setTheme("ace/theme/monokai");
	publicCss.getSession().setMode("ace/mode/css");
</script>