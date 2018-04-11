<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
//send function
add_action('cf7d_after_admin_form', 'cf7d_send_message');
function cf7d_send_message()
{
?>
    <div id='true'></div>
<?php
}
?>
