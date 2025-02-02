<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    ConvertedIn WooCommerce
 * @subpackage ConvertedIn_WooCommerce/admin/partials
 */

function ConvertedIn_WooCommerce_register_menu_pages()
{
    add_menu_page(
        null,
        'ConvertedIn',
        'manage_options',
        'converted-in',
        null,
        'data:image/svg+xml;base64,' . base64_encode("<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!-- Generator: Adobe Illustrator 25.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
<svg version=\"1.1\" id=\"Layer_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\"
	 viewBox=\"0 0 256 256\" style=\"enable-background:new 0 0 256 256;\" xml:space=\"preserve\">
<style type=\"text/css\">
	.st0{fill:#404040;}
</style>
<g id=\"hlpQJp.tif\">
	<g>
		<path fill=\"black\" class=\"st0\" d=\"M138.24,230.68c-0.43-0.2-0.86-0.4-1.29-0.6c-2.24-1.05-3.45-3.47-2.92-5.85c0.53-2.39,2.65-4.09,5.09-4.1
			c2.39,0,4.52,1.67,5.06,3.98c0.58,2.44-0.61,4.89-2.89,5.97c-0.39,0.18-0.76,0.4-1.14,0.6
			C139.52,230.68,138.88,230.68,138.24,230.68z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M40,127.12c0.63-1.9,1.68-3.42,3.69-4.06c2.68-0.85,5.51,0.57,6.46,3.23c0.93,2.59-0.34,5.46-2.88,6.54
			c-2.57,1.09-5.57-0.08-6.74-2.62c-0.18-0.39-0.35-0.78-0.53-1.17C40,128.4,40,127.76,40,127.12z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M108.45,130.02c0.53,11.11,6.22,21.3,17.94,27.42c11.65,6.09,23.34,5.32,34.27-2.01
			c4.75-3.19,9.81-2.29,13.04,1.22c3.22,3.49,6.7,6.74,10.05,10.12c2.78,2.8,2.52,6.69-0.64,9.04c-3.33,2.47-7.01,3.14-10.84,1.44
			c-6.13-2.71-12.16,0.05-14.32,6.69c-0.6,1.83-0.79,3.82-0.95,5.76c-0.59,6.89-3.28,12.47-9.87,15.38
			c-9.83,4.34-20.1-0.53-23.25-10.87c-1.01-3.31-1.35-6.72-1.48-10.16c-0.2-5.12-3.75-9.37-8.63-10.28
			c-3.89-0.73-7.17,0.56-9.79,3.48c-1.83,2.03-4.09,3.18-6.81,3.29c-3.9,0.15-7.52-2.2-9.01-5.77c-1.53-3.67-0.65-7.71,2.3-10.55
			c5.4-5.2,4.08-14.21-2.57-17.52c-1.83-0.91-3.78-1.17-5.8-1.2c-6.8-0.1-12.06-3.02-15.18-9.12c-4.23-8.26-2.15-21.93,10.72-25.39
			c1.82-0.49,3.79-0.48,5.7-0.58c7.93-0.4,12.87-8.49,9.4-15.54c-0.57-1.16-1.4-2.25-2.31-3.16c-3.89-3.86-3.99-9.88-0.15-13.62
			c3.88-3.78,9.82-3.58,13.58,0.45c3.34,3.58,7.49,4.71,11.59,3.17c4.12-1.54,6.83-5.43,7-10.11c0.14-3.89,0.59-7.73,1.99-11.39
			c2.87-7.49,8.98-11.29,17.22-10.73c7.38,0.5,13.14,5.49,14.8,13c0.45,2.05,0.37,4.22,0.8,6.28c0.39,1.89,0.89,3.84,1.78,5.54
			c2.59,4.94,7.71,6.68,12.85,4.5c3.4-1.44,6.7-1.38,9.89,0.43c1.05,0.6,2.07,1.41,2.81,2.36c1.71,2.19,1.48,5.23-0.49,7.24
			c-3.75,3.82-7.51,7.63-11.35,11.35c-3.19,3.08-7.62,3.16-11.53,0.57c-11.92-7.88-24.22-8.4-36.59-1.28
			C114.51,105.25,108.44,116.35,108.45,130.02z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M173.06,186.79c5.23,0.01,9.43,4.23,9.42,9.45c-0.01,5.22-4.26,9.45-9.46,9.43c-5.22-0.02-9.41-4.24-9.4-9.47
			C163.62,190.96,167.83,186.77,173.06,186.79z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M106.94,69.13c-5.24,0-9.44-4.17-9.44-9.41c-0.01-5.23,4.19-9.45,9.41-9.47c5.2-0.01,9.45,4.22,9.46,9.43
			C116.38,64.9,112.16,69.12,106.94,69.13z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M97.49,196.23c0-5.24,4.18-9.44,9.42-9.44c5.23,0,9.45,4.19,9.45,9.42c0.01,5.21-4.23,9.46-9.43,9.46
			C101.71,205.67,97.5,201.45,97.49,196.23z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M163.61,59.64c0.02-5.22,4.28-9.43,9.48-9.39c5.22,0.04,9.42,4.31,9.38,9.51c-0.04,5.24-4.25,9.38-9.5,9.37
			C167.77,69.12,163.59,64.89,163.61,59.64z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M71.87,86.31c5.24,0.01,9.41,4.2,9.4,9.45c-0.01,5.24-4.22,9.43-9.45,9.41c-5.23-0.02-9.41-4.23-9.4-9.47
			C62.43,90.45,66.61,86.3,71.87,86.31z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M71.79,169.62c-5.23-0.02-9.4-4.23-9.37-9.47c0.03-5.23,4.26-9.42,9.48-9.39c5.24,0.04,9.4,4.25,9.37,9.49
			C81.24,165.49,77.05,169.63,71.79,169.62z\"/>
		<path fill=\"black\" class=\"st0\" d=\"M144.3,30.63c-0.02,2.87-2.33,5.17-5.18,5.16c-2.91-0.01-5.23-2.37-5.19-5.29c0.03-2.81,2.44-5.23,5.2-5.23
			C141.91,25.27,144.32,27.75,144.3,30.63z\"/>
	</g>
</g>
</svg>
")
        ,
        null
    );
    if (get_option('ConvertedIn_WooCommerce_loggedIn_in_platform', false) == false) {
        add_submenu_page(
            'converted-in',
            'Installation',
            'Installation',
            'manage_options',
            'converted-in',
            'ConvertedIn_WooCommerce_installationPage_markup',
            null
        );
        add_submenu_page(
            'converted-in',
            'Settings',
            'Settings',
            'manage_options',
            'converted-settings',
            'ConvertedIn_WooCommerce_settingsPage_markup',
            2
        );
    } else {
        add_submenu_page(
            'converted-in',
            'Settings',
            'Settings',
            'manage_options',
            'converted-in',
            'ConvertedIn_WooCommerce_settingsPage_markup',
            2
        );
        add_submenu_page(
            'converted-in',
            'ConvertedIn Pixel',
            'ConvertedIn Pixel',
            'manage_options',
            'converted-in-pixel',
            'ConvertedIn_WooCommerce_PixelPage_markup',
            2
        );
    }

}

function ConvertedIn_WooCommerce_settingsPage_markup()
{
    include plugin_dir_path(__FILE__) . 'settingsPage.php';
    if (isset($_POST['ConvertedIn_WooCommerce_api_token'])) {
        update_option('ConvertedIn_WooCommerce_api_token', sanitize_text_field($_POST['ConvertedIn_WooCommerce_api_token']));
    }
    if (isset($_POST['ConvertedIn_WooCommerce_Pixel_Code'])) {
        update_option('ConvertedIn_WooCommerce_Pixel_Code', sanitize_text_field($_POST['ConvertedIn_WooCommerce_Pixel_Code']));
    }
//    include plugin_dir_path(__FILE__) . 'settingsPage.php';
}

function ConvertedIn_WooCommerce_PixelPage_markup()
{
    include plugin_dir_path(__FILE__) . 'pixelPage.php';

    if (isset($_POST['ConvertedIn_WooCommerce_Pixel_Code'])) {
        update_option('ConvertedIn_WooCommerce_Pixel_Code', sanitize_text_field($_POST['ConvertedIn_WooCommerce_Pixel_Code']));
    }
}

function ConvertedIn_WooCommerce_installationPage_markup()
{
    include plugin_dir_path(__FILE__) . 'installationPage.php';
    if (isset($_POST['ConvertedIn_WooCommerce_api_token'])) {
        update_option('ConvertedIn_WooCommerce_api_token', sanitize_text_field($_POST['ConvertedIn_WooCommerce_api_token']));
    }
    if (isset($_POST['ConvertedIn_WooCommerce_Pixel_Code'])) {
        update_option('ConvertedIn_WooCommerce_Pixel_Code', sanitize_text_field($_POST['ConvertedIn_WooCommerce_Pixel_Code']));
    }
//    include plugin_dir_path(__FILE__) . 'settingsPage.php';
}


add_action('admin_menu', 'ConvertedIn_WooCommerce_register_menu_pages');

