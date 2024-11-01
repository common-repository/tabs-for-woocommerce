<?php

if (!defined('ABSPATH'))
{
	exit;
}


function twf_product_tabs_menu()
{
	add_submenu_page('edit.php?post_type=woocommercetabs',__('Global Settings','tabs-for-woocommerce'), __('Global Settings','tabs-for-woocommerce'),'manage_options', 'tabglobalsettings','twf_product_tabs_menu_setting');
}

add_action('admin_menu', 'twf_product_tabs_menu',12);


function twf_product_tabs_menu_setting()
{
	global $wpdb, $wp_roles;
	
	if (isset ( $_POST ['tomas_product_tab_disable_all_feature_sbumit'] ))
	{
	
		if (isset ( $_POST ['tomas_product_tab_disable_all_feature'] ))
		{
	
			$tomas_product_tab_disable_all_feature = $_POST ['tomas_product_tab_disable_all_feature'];
			update_option('tomas_product_tab_disable_all_feature',$tomas_product_tab_disable_all_feature);
		}
	}

	
	$tomas_product_tab_disable_all_feature = get_option('tomas_product_tab_disable_all_feature');
	
	?>

<div style='margin: 10px 5px;'>
	<div style='padding-top: 5px; font-size: 22px;'>Product Tabs Global Settings:</div>
</div>
<div style='clear: both'></div>
<div class="wrap">
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="post-body">
				<div id="dashboard-widgets-main-content">
					<div class="postbox-container" style="width: 90%;">
						<div class="postbox">
							<h3 class='hndle' style='padding: 20px; !important'>
								<span>
									<?php
	echo __ ( 'Temporarily Turn off All Featrures:', 'tabs-for-woocommerce' );
	?>
									</span>
							</h3>

							<div class="inside" style='padding-left: 10px;'>
								<form id="bpmoform" name="bpmoform" action="" method="POST">
									<table id="bpmotable" width="100%">

										<tr style="margin-top: 30px;">
											<td width="30%" style="padding: 20px;" valign="top">
										<?php
	echo __ ( 'Temporarily Turn off All Featrures?', 'tabs-for-woocommerce' );
	?>
										</td>
											<td width="70%" style="padding: 20px;">
										<select name = "tomas_product_tab_disable_all_feature" id = "tomas_product_tab_disable_all_feature">
										<?php 
											$tomas_product_tab_disable_all_feature = get_option('tomas_product_tab_disable_all_feature');

											if ($tomas_product_tab_disable_all_feature == 'NO')
											{
												?>
												<option selected = "selected" value="NO">NO, enable all features please</option>
												<?php 
											}
											else 
											{											
										?>
												<option value="NO">NO, enable all features please</option>
										<?php 
											}
											if ($tomas_product_tab_disable_all_feature == 'YES')
											{
												?>
												<option selected = "selected" value="YES">Yes, disable all features now</option>
												<?php 
											}
											else 
											{
												
										?>
												<option value="YES">Yes, disable all features now</option>
										<?php 
											}											
										?>
										</select>
											</td>
										</tr>
									</table>
									<br />
	<input type="submit" id="tomas_product_tab_disable_all_feature_sbumit" name="tomas_product_tab_disable_all_feature_sbumit" value=" Submit " style="margin: 1px 20px;">
								</form>

								<br />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both"></div>
<br />
<?php
}

