<?php namespace Wc1c;
defined('ABSPATH') || exit;

$admin = Admin::instance();

$nav = '<nav class="nav-tab-wrapper woo-nav-tab-wrapper pt-0">';

foreach($admin->getSections() as $tab_key => $tab_name)
{
	if(!isset($tab_name['visible']) && $tab_name['title'] !== true)
	{
		continue;
	}

    if($tab_key === $admin->getCurrentSection())
    {
        $nav .= '<a href="' . admin_url('admin.php?page=wc1c&section=' . $tab_key) . '" class="nav-tab nav-tab-active">' . $tab_name['title'] . '</a>';
    }
    else
    {
        $nav .= '<a href="' . admin_url('admin.php?page=wc1c&section=' . $tab_key) . '" class="nav-tab">' . $tab_name['title'] . '</a>';
    }
}

echo $nav;

$settings = wc1c()->settings('connection');

if($settings->get('login', false))
{
	$admin->connectBox(__($settings->get('login', 'Undefined'), 'wc1c'), true);
}
else
{
	$admin->connectBox(__( 'Connection to the WC1C', 'wc1c'));
}

echo '</nav>';