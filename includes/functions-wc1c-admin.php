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
	if(is_wc1c_admin_request() && wc1c_get_var($_GET['section']) === 'tools')
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

		$available_tools = WC1C()->get_tools();

		if(array_key_exists($tool_id, $available_tools))
		{
			return true;
		}
	}

	return false;
}