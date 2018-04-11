<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
add_action('cf7d_admin_after_heading_field', 'cf7d_admin_after_heading_edit_field_func');
function cf7d_admin_after_heading_edit_field_func_2()
{
    ?>
    <th style="width: 32px;" class="manage-column"><?php _e('Edit'); ?></th>
    <?php
}
add_action('cf7d_admin_after_body_field', 'cf7d_admin_after_body_edit_field_func_2', 10, 2);