<?php
/*
 Plugin Name: Tabs for WooCommerce
Plugin URI:  https://tooltips.org/
Description: Tabs for WooCommerce
Version: 1.0.5
Author: tooltipsorg
Author URI: https://tooltips.org/
Text Domain: tabs-for-woocommerce
License: GPLv3
*/
if (!defined('ABSPATH'))
{
	exit;
}

define('TFW_TABS_ADMIN_PATH', plugin_dir_path(__FILE__).'admin'.'/');
require_once(TFW_TABS_ADMIN_PATH."admin.php");

$tomas_product_tab_disable_all_feature = get_option('tomas_product_tab_disable_all_feature');

function tfw_add_woo_tabs_post_type() 
{
	global $wp_rewrite;
	$catlabels = array(
			'name'                          => 'Categories',
			'singular_name'                 => 'WooCommerce Tabs Categories',
			'all_items'                     => 'All WooCommerce Tabs',
			'parent_item'                   => 'Parent WooCommerce Tabs',
			'edit_item'                     => 'Edit WooCommerce Tabs',
			'update_item'                   => 'Update WooCommerce Tabs',
			'add_new_item'                  => 'Add New WooCommerce Tabs',
			'new_item_name'                 => 'New WooCommerce Tabs',
	);


	$args = array(
			'label'                         => 'Categories',
			'labels'                        => $catlabels,
			'public'                        => true,
			'hierarchical'                  => true,
			'show_ui'                       => true,
			'show_in_nav_menus'             => true,
			'args'                          => array( 'orderby' => 'term_order' ),
			'rewrite'                       => array( 'slug' => 'woocommer_tabs_categories', 'with_front' => false ),
			'query_var'                     => true
	);

	register_taxonomy( 'WooCommerceTabs_categories', 'WooCommerceTabs', $args );

	$labels = array(
			'name' => __('WooCommerce Tabs', 'tabs-for-woocommerce'),
			'singular_name' => __('WooCommerce Tab', 'tabs-for-woocommerce'),
			'add_new' => __('Add New', 'tabs-for-woocommerce'),
			'add_new_item' => __('Add New Tab for WooCommece', 'tabs-for-woocommerce'),
			'edit_item' => __('Edit Tab for WooCommerce', 'tabs-for-woocommerce'),
			'new_item' => __('New Tab for WooCommerce', 'tabs-for-woocommerce'),
			'all_items' => __('All Tabs', 'tabs-for-woocommerce'),
			'view_item' => __('View Tabs for WooCommerce', 'tabs-for-woocommerce'),
			'search_items' => __('Search Tab for WooCommerce', 'tabs-for-woocommerce'),
			'not_found' =>  __('No Tab for WooCommerce found', 'tabs-for-woocommerce'),
			'not_found_in_trash' => __('No Tab for WooCommerce found in Trash', 'tabs-for-woocommerce'),
			'menu_name' => __('Product Tabs', 'tabs-for-woocommerce')
	);

	$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'_builtin' =>  false,
			'query_var' => "WooCommerceTabs",
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor','author','custom-fields','thumbnail','excerpt')
	);
	register_post_type('WooCommerceTabs', $args);
	$wp_rewrite->flush_rules();
}
add_action( 'init', 'tfw_add_woo_tabs_post_type' );

if ('YES' == $tomas_product_tab_disable_all_feature)
{
	return;
}



add_filter( 'woocommerce_product_tabs', 'twf_add_product_tabs_for_woo' );

function twf_add_product_tabs_for_woo( $tabs ) 
{
	global $wpdb, $product, $post_id;
	
	$post_type = 'WooCommerceTabs';
	$sql = $wpdb->prepare( "SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_type=%s AND post_status='publish'",$post_type);
	$results = $wpdb->get_results( $sql );
	
	if ((!(empty($results))) && (is_array($results)) && (count($results) >0))
	{
		$i = 50;
		foreach ( $results as $single ) 
		{
			$tab_title = $single->post_title;
			$tab_content = $single->post_content;
			$tab_post_id = $single->ID;
			
			$tab_id = '_twf_woo_add_product_tab_' . $single->ID;
			$twf_var_woo_product_tab_id = get_post_meta( $product->id, $tab_id, true);
			if ($twf_var_woo_product_tab_id == 'yes')
			{
				$tabs[ $tab_post_id ] = array(
						'title'    => $tab_title,
						'priority' => $i,
						'content'  => $tab_content,
						'tab_post_id' => $tab_post_id,
						'callback' => 'twf_show_product_tabs_for_woo',
				);				
			}
			$i+= 1;
		}
	}
	$tabs = apply_filters( 'twf_show_product_tabs_for_woo_tabs', $tabs );
	return $tabs;
}

function twf_show_product_tabs_for_woo( $key, $tab ) 
{
	$tabs_post 	= get_post($tab['tab_post_id']);
	$tabs_title = $tabs_post->post_title;
	$tabs_content 		= $tabs_post->post_content;
	$tabs_content 		= apply_filters('the_content', $tabs_content);
	$tabs_content 		= str_replace(']]>', ']]&gt;', $tabs_content);
	
	$tabs_title = apply_filters( 'twf_show_product_tabs_for_woo_title', '<h2>' . $tabs_title . '</h2>', $tab );
	echo $tabs_title;
	$tabs_content = apply_filters( 'twf_show_product_tabs_for_woo_content', $tabs_content, $tab );
	echo $tabs_content;
	return;
}

function twf_woo_product_add_product_tab() {
	?>
    <li class="twf_woo_product_add_product_tab_icon"><a href="#twf_woo_product_add_product_tab_data"><span><?php esc_html_e( 'Product Tabs', 'tabs-for-woocommerce' ); ?></span></a></li>
<?php
}
add_action( 'woocommerce_product_write_panel_tabs', 'twf_woo_product_add_product_tab' );	


function twf_woo_product_tab_checkbox()
{
	global $post,$wpdb;
	?>
    <div id="twf_woo_product_add_product_tab_data" class="panel woocommerce_options_panel">
        <div class="options_group">
            <p class="form-field twf_woo_add_product_tab_p_class">
                <h4 style="padding-left: 10px;">
                	<?php 
                	echo __ ( 'Add Tabs to this Product:', 'tabs-for-woocommerce' );
                	?>
                </h4>
				<?php
				$post_type = 'WooCommerceTabs';
				$sql = $wpdb->prepare( "SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_type=%s AND post_status='publish'",$post_type);
				$results = $wpdb->get_results( $sql );
				
				if ((!(empty($results))) && (is_array($results)) && (count($results) >0))
				{
					foreach ( $results as $single )
					{
						$tab_id = '_twf_woo_add_product_tab_' . $single->ID;
						$tab_title = $single->post_title;
						woocommerce_wp_checkbox(
									array(
										'id' => $tab_id,
										'label' => $tab_title
									)
								);
					}
				}
				?>
            </p>
        </div>
    </div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'twf_woo_product_tab_checkbox' );

function twf_woo_product_tab_save_meta() 
{
	global $post_id,$wpdb;

	$post_type = 'WooCommerceTabs';
	$sql = $wpdb->prepare( "SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_type=%s AND post_status='publish'",$post_type);
	$results = $wpdb->get_results( $sql );
		
	if ((!(empty($results))) && (is_array($results)) && (count($results) >0))
	{
		foreach ( $results as $single )
		{
			$tab_id = '_twf_woo_add_product_tab_' . $single->ID;

			if (( !(isset( $_POST[$tab_id] ) )) || (empty($_POST[$tab_id] )))
			{
				$twf_var_woo_product_tab_id =  sanitize_text_field($_POST[$tab_id]);
				delete_post_meta($post_id, $tab_id);
			}
			else
			{
				$twf_var_woo_product_tab_id =  sanitize_text_field($_POST[$tab_id]);
				update_post_meta( $post_id, $tab_id, $twf_var_woo_product_tab_id );
			}

		}
	}
	return;
}
add_action( 'woocommerce_process_product_meta', 'twf_woo_product_tab_save_meta' );



