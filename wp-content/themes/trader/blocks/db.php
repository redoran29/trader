<?php
global $wpdb;
$table_name = $wpdb->get_blog_prefix() . 'trader';
$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$sql = "CREATE TABLE {$table_name} (
    id int(11) unsigned NOT NULL auto_increment,
    name varchar(255) NOT NULL default '',
    symbol varchar(255) NOT NULL default '',
    rank varchar(255) NOT NULL default '',
    price_usd varchar(255) NOT NULL default '',
    price_btc varchar(255) NOT NULL default '',
    24h_volume_usd varchar(255) NOT NULL default '',
    market_cap_usd varchar(255) NOT NULL default '',
    available_supply varchar(255) NOT NULL default '',
    total_supply varchar(255) NOT NULL default '',
    max_supply varchar(255) NOT NULL default '',
    percent_change_1h varchar(255) NOT NULL default '',
    percent_change_24h varchar(255) NOT NULL default '',
    percent_change_7d varchar(255) NOT NULL default '',
    last_updated varchar(255) NOT NULL default ''
    PRIMARY KEY  (id)
) {$charset_collate};";
dbDelta( $sql );