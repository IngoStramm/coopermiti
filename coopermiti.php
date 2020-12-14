<?php

/**
 * Plugin Name: Coopermiti
 * Plugin URI: https://agencialaf.com
 * Description: Descrição do Coopermiti.
 * Version: 0.0.1
 * Author: Ingo Stramm
 * Text Domain: coopermiti
 * License: GPLv2
 */

defined('ABSPATH') or die('No script kiddies please!');

define('COOPERMITI_DIR', plugin_dir_path(__FILE__));
define('COOPERMITI_URL', plugin_dir_url(__FILE__));

function coop_debug($debug)
{
    echo '<pre>';
    var_dump($debug);
    echo '</pre>';
}

// require_once 'tgm/tgm.php';
// require_once 'classes/classes.php';
// require_once 'scripts.php';

function coop_get_user_role()
{
    if (is_user_logged_in()) :
        $user = wp_get_current_user();
        $roles = (array) $user->roles;
        return $roles[0];
    else :
        return false;
    endif;
}

add_action('init', function () {
    if (coop_get_user_role() == 'educa_museu_editor') {
        add_action('admin_menu', function () {
            global $menu, $submenu;

            remove_menu_page('edit.php');
            remove_menu_page('upload.php');
            remove_menu_page('edit.php?post_type=page');
            remove_menu_page('edit-comments.php');
            remove_menu_page('edit.php?post_type=informacao');
        });
    } elseif (coop_get_user_role() == 'subeditor') {
        add_action('admin_menu', function () {
            global $menu, $submenu;

            remove_menu_page('edit.php');
            remove_menu_page('upload.php');
            remove_menu_page('edit-comments.php');
            remove_menu_page('edit.php?post_type=informacao');
        });
    }
});


require 'plugin-update-checker-4.10/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/IngoStramm/coopermiti/master/info.json',
    __FILE__,
    'coopermiti'
);
