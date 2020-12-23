<?php
/**
 * Is WC1C admin tools request?
 *
 * @param string $tool_id
 *
 * @return bool
 */
function is_wc1c_admin_tools_request($tool_id = '')
{
	if(is_wc1c_admin_section_request('tools'))
	{
		if('' === $tool_id)
		{
			return true;
		}

		$get_tool_id = wc1c_get_var($_GET['tool_id'], '');

		if($get_tool_id !== $tool_id)
		{
			return false;
		}

		return true;
	}

	return false;
}

/**
 * Is WC1C admin section request?
 *
 * @param string $section
 *
 * @return bool
 */
function is_wc1c_admin_section_request($section = '')
{
	if('' === $section)
	{
		return false;
	}

	if(is_wc1c_admin_request() && wc1c_get_var($_GET['section'], '') === $section)
	{
		return true;
	}

	return false;
}

/**
 * @param string $tool_id
 *
 * @return string
 */
function wc1c_admin_get_tools_url($tool_id = '')
{
	$path = 'admin.php?page=wc1c&section=tools';

	if('' === $tool_id)
	{
		return admin_url($path);
	}

	$path = 'admin.php?page=wc1c&section=tools&tool_id=' . $tool_id;
	
	return admin_url($path);
}

/**
 * @param string $action
 * @param string $configuration_id
 *
 * @return string
 */
function wc1c_admin_get_configuration_url($action = 'list', $configuration_id = '')
{
	$path = 'admin.php?page=wc1c&section=configurations';

	if('list' !== $action)
	{
		$path .= '&do_action=' . $action;
	}

	if('' === $configuration_id)
	{
		return admin_url($path);
	}

	$path .= '&config_id=' . $configuration_id;

	return admin_url($path);
}

/**
 * Outputs a "back" link so admin screens can easily jump back a page
 *
 * @param string $label title of the page to return to.
 * @param string $url URL of the page to return to.
 */
function wc1c_admin_back_link($label, $url)
{
	echo '<small class="wc-admin-breadcrumb"><a href="' . esc_url($url) . '" aria-label="' . esc_attr($label) . '">&#x2934;</a></small>';
}