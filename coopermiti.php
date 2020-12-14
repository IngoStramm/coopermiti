<?php

/**
 * Plugin Name: Coopermiti
 * Plugin URI: https://agencialaf.com
 * Description: Descrição do Coopermiti.
 * Version: 0.0.2
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

require_once 'tgm/tgm.php';
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

function coop_exibe_metabox()
{
    $curr_role = coop_get_user_role();
    if (in_array($curr_role, ['subeditor', 'educa_museu_editor'])) {
        return false;
    } else {
        return true;
    }
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
            remove_menu_page('envato-elements');
            remove_menu_page('nav-menus.php');
            remove_menu_page('new-users');
        });
        remove_action('cmb2_admin_init', 'wb_register_theme_options_metabox', 10, 1);
    } elseif (coop_get_user_role() == 'subeditor') {
        add_action('admin_menu', function () {
            global $menu, $submenu;

            remove_menu_page('edit.php');
            remove_menu_page('upload.php');
            remove_menu_page('edit-comments.php');
            remove_menu_page('edit.php?post_type=informacao');
            remove_menu_page('envato-elements');
            remove_menu_page('nav-menus.php');
            remove_menu_page('new-users');
            remove_menu_page('wb_options');

            remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
        });
    }
});

add_action('cmb2_admin_init', 'coopermiti_register_page_metabox');
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function coopermiti_register_page_metabox()
{
    /**
     * Sample metabox to demonstrate each field type included
     */
    $cmb = new_cmb2_box(array(
        'id'            => 'coopermiti_demo_metabox',
        'title'         => esc_html__('Acessibilidade da página', 'cmb2'),
        'object_types'  => array('page'), // Post type
        'context'       => 'side',
        'show_on_cb'    => 'coop_exibe_metabox'
    ));

    $cmb->add_field(array(
        'name' => esc_html__('Gestor de Conteúdo', 'cmb2'),
        'desc' => esc_html__('Marque esta opção para tornar esta página acessível para o Gestor de Conteúdo.', 'cmb2'),
        'id'   => 'subeditor',
        'type' => 'checkbox',
    ));

    $cmb->add_field(array(
        'name' => esc_html__('Gestor do Museu e Educação', 'cmb2'),
        'desc' => esc_html__('Marque esta opção para tornar esta página acessível para o Gestor do Museu e Educação.', 'cmb2'),
        'id'   => 'educa_museu_editor',
        'type' => 'checkbox',

    ));
}

add_action('pre_get_posts', 'coop_show_pages');

function coop_show_pages($query)
{
    $curr_role = coop_get_user_role();
    if (is_admin() && !empty($_GET['post_type']) && $_GET['post_type'] == 'page' && $query->query['post_type'] == 'page') {
        $custom_roles = ['subeditor', 'educa_museu_editor'];
        foreach ($custom_roles as $custom_role) {
            if ($curr_role == $custom_role) {
                $query->set(
                    'meta_query',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => $custom_role,
                            'value' => 'on',
                            'compare' => '='
                        ),
                        array(
                            'key' => $custom_role,
                            'compare' => 'EXISTS'
                        ),
                    )
                );
            }
        }
    }
}

add_action('admin_bar_menu', 'coop_remove_bar_menu_page_edit', 999);

function coop_remove_bar_menu_page_edit($wp_admin_bar)
{
    $curr_role = coop_get_user_role();
    $custom_roles = ['subeditor', 'educa_museu_editor'];
    if (in_array($curr_role, $custom_roles)) {
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('edit');
    }
}

add_action('admin_head', 'coop_menus_edit_style');

function coop_menus_edit_style()
{
    $screen = get_current_screen();
    if ($screen->id == 'edit-page') { ?>
        <style>
            .page-title-action {
                display: none !important;
            }
        </style>
<?php }
}

require 'plugin-update-checker-4.10/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/IngoStramm/coopermiti/master/info.json',
    __FILE__,
    'coopermiti'
);
