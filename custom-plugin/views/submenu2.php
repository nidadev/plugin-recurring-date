<?php
/*
Plugin Name: Custom Recurring Delivery Date Plugin
Plugin URI: https://akismet.com/
Description: Developed by nida.
Version: 5.3.2
*/

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

    /*add_submenu_page(
        "recurring-delivery",
        "Add Field to Product",
        "Add Field to Product",
        "manage_options",
        "add-new",
        "add_new_function"
    );*/
}

add_action("admin_menu", "my_custom_add_menu");

function custom_admin_view()
{
    echo "<h1>Welcome to Recurring Delivery Dates</h1>";
}

function add_new_function()
{
    echo "<h1>This is my first submenu</h1>";

}
