<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$x_mflow_public_key = 'pk_dd9da640f0e6174fd3819fc642557ed7';
$x_mflow_secret_key = 'sk_04ddbf3e6702ace9a24064ed0bd42f1b';

function wcip_add_settings_page() {
    	add_menu_page(
		'Import Product', // page <title>Title</title>
		'Import Product', // link text
		'manage_options', // user capabilities
		'import-product', // page slug
		'wcip_render_plugin_settings_page', // this function prints the page content
		'dashicons-menu-alt3', // icon (from Dashicons for example)
		4 // menu position
	);


		// I created variables to make the things clearer
		$page_slug = 'import-product';
		$option_group = 'import_product_settings';

		// 1. create section
		add_settings_section(
			'ip_section_id', // section ID
			'', // title (optional)
			'', // callback function to display the section (optional)
			$page_slug
		);

		// 2. register fields
		register_setting( $option_group, 'num_of_product', 'absint' );

		add_settings_field(
			'num_of_product',
			'Number of Products',
			'Import_number',  //callback function
			$page_slug,
			'ip_section_id',
			array(
				'label_for' => 'num_of_product',
				'class' => 'num-of-product', // for <tr> element
				'name' => 'num_of_product' // pass any custom parameters
			)
		);
}
add_action( 'admin_menu', 'wcip_add_settings_page' );
// custom callback function to print field HTML
function Import_number( $args ){
	printf(
		'<input type="number" id="%s" name="%s" value="%d" />',
		$args[ 'name' ],
		$args[ 'name' ],
		get_option( $args[ 'name' ], 10 ) // 10 is the default number of slides
	);
}

//Show success message 
add_action( 'admin_notices', 'import_product_notice' );
function import_product_notice() {

	if(
		isset( $_GET[ 'page' ] ) 
		&& 'import-product' == $_GET[ 'page' ]
		&& isset( $_GET[ 'settings-updated' ] ) 
		&& true == $_GET[ 'settings-updated' ]
	) {
		?>
			<div class="notice notice-success is-dismissible">
				<p>
					<strong>Import Product settings saved.</strong>
				</p>
			</div>
		<?php
	}

}

function wcip_render_plugin_settings_page() {
    ?>
    <div class="wrap">

		<h1><?php echo get_admin_page_title() ?></h1>
		<i>Wait and Do Not Refresh the page while importing the Product It Take Few minutes to import Please Select less Number execute faster</i>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'import_product_settings' ); // settings group name
				do_settings_sections( 'import-product' ); // just a page slug
				submit_button(); // "Save Changes" button
			?>
		</form>
	</div>
    
	<?php


	if(isset($_GET[ 'page' ] ) && $_GET['settings-updated'] && 'import-product' == $_GET[ 'page' ] && get_option('num_of_product') > 0){



		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://stage.mflow.co.il/api/v1/products/listAll',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'x-mflow-public-key: pk_dd9da640f0e6174fd3819fc642557ed7',
		    'x-mflow-secret-key: sk_04ddbf3e6702ace9a24064ed0bd42f1b',
		    'Cookie: XSRF-TOKEN=eyJpdiI6InNqVGYwNVluZXFVempVNDg1TnhHbHc9PSIsInZhbHVlIjoiZEJpZDcvdXFPaFBsN1pTVloyalR2OThnYmxkN2VoTy9QcjFWeXovSnoxbEZ6U1p0UDdMb3pkNklPZEJ3UVE2YUpuS2wxUGpqSFJtdmJud1A4UEdtTVdQU2crQWo0aXFFekg1Mm5sTTMvcUFhb083VTU2STFyajJDQkQ2YkZSYkgiLCJtYWMiOiI1OGYxMWY2MWIzYzljOTI5M2E0ZTcwY2U1MDU2OWUwZjM2NmE4NWI5OWU5MzM1NmNkMThmMTg2NDFjMTc0MDY3IiwidGFnIjoiIn0%3D; mflow_erp_session=eyJpdiI6ImlEWnFsNTh4UFBERUZZa3FqbGw3a2c9PSIsInZhbHVlIjoiU1pxR2VpeVY3MDNyRXB6bW5Ja2lRaVJwdlVuditmTmVhZTZxT2FCbjdhUXBXQ0d2c2RlT25ORjJuaWdSMGxnVTRQcm9Vbmt0V3g0VG9UNXdtV2s4VHU0NCtwbm1wZENGU1EzTS9Ya3VBcWdkRyt2OHpCdUtrdVlxTWJ6dXk3eWUiLCJtYWMiOiIzYjBkMmU0YTIxMDRmZTZiZGJjMzVhNWJiNDNjMzBjMjdmMDcyYTBkZmRkZWRlZDk4OWVhZGZmODc5ODNiNzJiIiwidGFnIjoiIn0%3D'
		  ),
		));

		$response = curl_exec($curl);
		$response = json_decode($response, true);

		    // Products found
    if (!empty($response)) {

    	$total_product = get_option('num_of_product');
    	// var_dump($response['products'][0]['id']);
		for($i=0; $i< $total_product;$i++){

			// check if SKU already exists
            $productID = wc_get_product_id_by_sku($response['products'][$i]['sku']);
            if($productID){
            	$total_product = $total_product + 1;
            }

            // Product Do not exists
            if (empty($productID)) {
               	echo ' Product Name : '.$response['products'][$i]['name']."<br>";
				$name = $response['products'][$i]['name'];
				$sku = $response['products'][$i]['sku'];
				$product = new WC_Product_Variable();
				$product->set_name($name);
				$product->set_regular_price($response['products'][$i]['price']);
				$product->set_description($response['products'][$i]['description']);
				$product->set_sku($sku);
				$product->set_stock_status( 'instock' );
				$product->set_manage_stock( true );
				$product->set_stock_quantity($response['products'][$i]['stock_quantity']);
				$product->save();
				$file = 'https://wordpress.org/about/images/logos/wordpress-logo-stacked-rgb.png';
				$image_id = media_sideload_image($file, $product->id, "Cloth Sample", 'id');
				set_post_thumbnail( $product->id, $image_id );
            }
			
		}
    }

	}
	
		
				

}
	
?>

