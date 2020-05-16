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

		$get_tool_id = wc1c_get_var($_GET['tool_id']);

		if($get_tool_id !== $tool_id)
		{
			return false;
		}

		try
		{
			$available_tools = WC1C()->get_tools();
		}
		catch(Exception $e)
		{
			return false;
		}

		if(array_key_exists($tool_id, $available_tools))
		{
			return true;
		}
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
function get_wc1c_admin_tools_url($tool_id = '')
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
function get_wc1c_admin_configuration_url($action = 'list', $configuration_id = '')
{
	$path = 'admin.php?page=wc1c&section=configurations';

	if('list' !== $action)
	{
		$path .= '&action=' . $action;
	}

	if('' === $configuration_id)
	{
		return admin_url($path);
	}

	$path .= '&config_id=' . $configuration_id;

	return admin_url($path);
}