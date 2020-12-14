<?php

add_action('wp_enqueue_scripts', 'coopermiti_frontend_scripts');

function coopermiti_frontend_scripts()
{

    $min = (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '10.0.0.3'))) ? '' : '.min';

    if (empty($min)) :
        wp_enqueue_script('coopermiti-livereload', 'http://localhost:35729/livereload.js?snipver=1', array(), null, true);
    endif;

    wp_register_script('coopermiti-script', COOPERMITI_URL . 'assets/js/coopermiti' . $min . '.js', array('jquery'), '1.0.0', true);

    wp_enqueue_script('coopermiti-script');

    wp_localize_script('coopermiti-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('coopermiti-style', COOPERMITI_URL . 'assets/css/coopermiti.css', array(), false, 'all');
}
