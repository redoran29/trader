<?php
global $wpdb;
$newtable = $wpdb->get_results( "SELECT * FROM wp_trader WHERE name = 'Bitcoin'" );
//pre_print($newtable);
$i = 0;
$sum = '';
foreach ($newtable AS $k=>$cry){
    echo $k;
    $li = 'Price: ' . $cry->price_usd . ' - Date and time: ' . $cry->max_supply;
    $price = $cry->price_usd;
        $sum .= $price^$k;
    pre_print($li);
$i++;}
echo $sum;