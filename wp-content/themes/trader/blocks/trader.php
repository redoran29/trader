<?php
function true_load_posts(){
    echo $today = date("Y.m.d, G:i:s");
    global $wpdb;
    $query = $wpdb->get_results("SELECT * FROM wp_trader WHERE symbol = 'BTC'");
    echo '<pre>';
    print_r($query);
    echo '</pre>';
    die();
}
add_action('wp_ajax_action', 'true_load_posts');
add_action('wp_ajax_nopriv_action', 'true_load_posts');
