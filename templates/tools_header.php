<?php defined('ABSPATH') || exit; ?>

<?php
if(wc1c_get_var($_GET['tool_id'], '') !== '' && is_wc1c_admin_tools_request())
{

}
else
{
    ?>

    <p><?php _e('Information about tools installed in the system.', 'wc1c') ?></p>

<?php
}
?>

