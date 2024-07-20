<?php
/*
Plugin Name: Custom Recurring Delivery Date Plugin
Plugin URI: https://yahoo.com
Description: Developed by nida.
Version: 1.3.2
*/

define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("PLUGIN_URL_PATH", plugins_url(__FILE__));
define("PLUGIN_VERSION", '1.0.0');
define("WP_DIR_URL", plugin_dir_url(__FILE__));

function my_custom_add_menu()
{
    add_menu_page(
        "customplugin", //page title
        "Recurring Delivery Dates", //menu title
        "manage_options",
        "recurring-delivery",  //page slug
        "add_new_function",
        "dashicons-dashboard",
        "11"
    );
    add_submenu_page(
        "recurring-delivery",
        "Add Field to Product",
        "Add Field to Product",
        "manage_options",
        "add-new",
        "add_new_function"
    );
}

add_action("admin_menu", "my_custom_add_menu");

function custom_admin_view()
{
    echo "<h1>Welcome to Recurring Delivery Dates</h1>";
}

add_action('woocommerce_product_options_general_product_data', 'my_add_custom_fields_to_general_tab');

function my_add_custom_fields_to_general_tab()
{
    //check whether the role is admin or shop manager
    global $user_ID;
    $userdetails = get_userdata($user_ID);
    $role = $userdetails->roles;
    $valid_roles = array("administrator", "shop manager");

    if (in_array($role[0], $valid_roles)) {
       
    woocommerce_form_field(
        '_my_custom_field_id',
        array(
            'type'  => 'select',
            'id'          => '_my_custom_field_id', // Required, should be unique
            'label'       => esc_html__('Select Recurring Delivery Date', 'saucal-custom-code'), // Label for the field
            'placeholder' => esc_html__('Enter a value', 'saucal-custom-code'), // Placeholder text
            'desc_tip'    => true, // Enable description tooltip
            'description' => esc_html__('Select Recurring Delivery Date.', 'saucal-custom-code'), // Description for the tooltip
            'options' => array(
                '' => 'Select an option',
                'option_1' => '1st day of every month',
                'option_2' => '3rd day of every week',
            )
        )
    );
}
}

function add_new_function()
{
    include_once PLUGIN_DIR_PATH . "/views/add-new.php";
}

function add_new_datepicker()
{
    ob_start();
    include_once PLUGIN_DIR_PATH . "/views/datepicker.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}

function add_new_datepicker2()
{
    ob_start();
    include_once PLUGIN_DIR_PATH . "/views/datepicker2.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}

function custom_ui_js_files()
{
    wp_enqueue_style("jquery-wp-css", WP_DIR_URL . 'assets/css/jquery-ui.min.css');
    wp_enqueue_script("jquery");
    wp_enqueue_script("jquery-ui-accordion");
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_script("custom-script", WP_DIR_URL . 'assets/js/script2.js', array('jquery'), '1.0.0', true);
}
add_action("wp_enqueue_scripts", "custom_ui_js_files");

function custom_plugin_assets()
{
    //wp_enqueue_style("cpl_style", PLUGIN_URL_PATH . 'custom-plugin/assets/css/style.css', '', PLUGIN_VERSION);
    wp_enqueue_script("cpl_script", PLUGIN_URL_PATH . 'custom-plugin/assets/js/script2.js', '', PLUGIN_VERSION, false);
}

add_action("init", "custom_plugin_assets");
add_action('woocommerce_admin_process_product_object', 'wpn_save_field', 10, 1);
function wpn_save_field($product)
{
    if (isset($_POST['_my_custom_field_id'])) {
        $product->update_meta_data('_my_custom_field_id', sanitize_text_field($_POST['_my_custom_field_id']));
    }

    $product->save();
}
function my_display_custom_date_picker()
{
    global $product;

    if (is_a($product, 'WC_Product')) {
        $text = $product->get_meta('_my_custom_field_id');
        if ($text == 'option_1') {
            echo '<div class="custom-date-picker">';
            add_new_datepicker();
            echo '</div>';
        }

        if ($text == 'option_2') {
            echo '<div class="custom-date-picker">';
            add_new_datepicker2();
            echo '</div>';
        }
    
}
}
add_action('woocommerce_before_add_to_cart_form', 'wpn_display_on_single_product_page', 1);
function wpn_display_on_single_product_page()
{
    global $product;
    //check whether the role is admin or shop manager
    global $user_ID;
    $userdetails = get_userdata($user_ID);
    $role = $userdetails->roles;
    $valid_roles = array("administrator", "shop manager");

    if (in_array($role[0], $valid_roles)) {
    if (is_a($product, 'WC_Product')) {
        $text = $product->get_meta('_my_custom_field_id');
        if ($text == 'option_1') {
            $option = '1st day of every month';
            add_action('woocommerce_before_add_to_cart_button', 'my_display_custom_date_picker', 9);
        }

        if ($text == 'option_2') {
            $option = '3rd day of every week';
            add_action('woocommerce_before_add_to_cart_button', 'my_display_custom_date_picker', 9);
        } else {
            $option = '';
        }
        echo '<div class="woocommerce-message"> Your Selected Option is:' . $option . '</div>';
    }
}
}

// Add Delivery Date to cart item data for all products
add_filter('woocommerce_add_cart_item_data', 'my_add_custom_date_to_cart', 10, 2);
function my_add_custom_date_to_cart($cart_item_data, $product_id)
{
    if (isset($_POST['custom_date'])) {
        $cart_item_data['custom_date'] = sanitize_text_field($_POST['custom_date']);
    }
    return $cart_item_data;
}

// Display Delivery Date in cart and checkout
add_filter('woocommerce_get_item_data', 'my_display_custom_date_in_cart', 10, 2);
function my_display_custom_date_in_cart($cart_data, $cart_item)
{
    if (isset($cart_item['custom_date'])) {
        $cart_data[] = array(
            'name' => 'Delivery Date',
            'value' => sanitize_text_field($cart_item['custom_date']),
        );
    }
    return $cart_data;
}

// Save the Delivery Date field value to the order items
add_action('woocommerce_checkout_create_order_line_item', 'my_save_custom_date_to_order_items', 10, 4);
function my_save_custom_date_to_order_items($item, $cart_item_key, $values, $order)
{
    if (isset($values['custom_date'])) {
        $item->add_meta_data('Delivery Date', $values['custom_date'], true);
    }
}

// Display Delivery Date in admin order items table
add_filter('woocommerce_order_item_name', 'my_display_custom_date_in_admin_order_items_table', 10, 2);
function my_display_custom_date_in_admin_order_items_table($item_name, $item)
{
    // Check if the item has Delivery Date associated with it
    if ($custom_date = $item->get_meta('Delivery Date')) {
        // Append the Delivery Date to the item name
        $item_name .= '<br><small>' . esc_html__('Delivery Date:', 'your-textdomain') . ' ' . esc_html($custom_date) . '</small>';
    }
    return $item_name;
}
