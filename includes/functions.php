<?php
/**
 * Convert kb, mb, gb to bytes
 *
 * @param $size
 *
 * @return float|int
 */
function wc1c_convert_size($size)
{
	if(empty($size))
	{
		return 0;
	}

	$type = $size[strlen($size) - 1];

	if(!is_numeric($type))
	{
		$size = (int) $size;

		switch($type)
		{
			case 'K':
				$size *= 1024;
				break;
			case 'M':
				$size *= 1024 * 1024;
				break;
			case 'G':
				$size *= 1024 * 1024 * 1024;
				break;
			default:
				return $size;
		}
	}

	return (int)$size;
}

/**
 * Get all available configurations statuses
 *
 * @return array
 */
function wc1c_configurations_get_statuses()
{
	$statuses =
		[
			'draft',
			'inactive',
			'active',
			'processing',
			'error',
			'deleted',
		];

	return apply_filters( 'wc1c_configurations_get_statuses', $statuses);
}

/**
 * Get normal configuration status
 *
 * @param string $status
 *
 * @return string
 */
function wc1c_configurations_get_statuses_label($status)
{
	$default_label = __('Undefined', 'wc1c');

	$statuses_labels = apply_filters
	(
		'wc1c_configurations_get_statuses_labels',
		[
			'draft' => __('Draft', 'wc1c'),
			'active' => __('Active', 'wc1c'),
			'inactive' => __('Inactive', 'wc1c'),
			'error' => __('Error', 'wc1c'),
			'processing' => __('Processing', 'wc1c'),
			'deleted' => __('Deleted', 'wc1c'),
		]
	);

	if(empty($status) || !array_key_exists($status, $statuses_labels))
	{
		$status_label = $default_label;
	}
	else
	{
		$status_label = $statuses_labels[$status];
	}

	return apply_filters( 'wc1c_configurations_get_statuses_label_return', $status_label, $status, $statuses_labels);
}

/**
 * Get folder name for configuration statuses
 *
 * @param string $status
 *
 * @return string
 */
function wc1c_configurations_get_statuses_folder($status)
{
	$default_folder = __('Undefined', 'wc1c');

	$statuses_folders = apply_filters
	(
		'wc1c_configurations_get_statuses_folders',
		[
			'draft' => __('Drafts', 'wc1c'),
			'active' => __('Activated', 'wc1c'),
			'inactive' => __('Deactivated', 'wc1c'),
			'error' => __('With errors', 'wc1c'),
			'processing' => __('In processing', 'wc1c'),
			'deleted' => __('Trash', 'wc1c'),
		]
	);

	$status_folder = $default_folder;

	if(!empty($status) || array_key_exists($status, $statuses_folders))
	{
		$status_folder = $statuses_folders[$status];
	}

	return apply_filters( 'wc1c_configurations_get_statuses_folder_return', $status_folder, $status, $statuses_folders);
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime
 *
 * @param string $time_string Time string
 * @param int|null $from_timestamp Timestamp to convert from
 *
 * @return int
 */
function wc1c_string_to_timestamp($time_string, $from_timestamp = null)
{
	$original_timezone = date_default_timezone_get();

	date_default_timezone_set('UTC');

	if(null === $from_timestamp)
	{
		$next_timestamp = strtotime($time_string);
	}
	else
	{
		$next_timestamp = strtotime($time_string, $from_timestamp);
	}

	date_default_timezone_set($original_timezone);

	return $next_timestamp;
}

/**
 * Helper to retrieve the timezone string for a site until
 *
 * @return string PHP timezone string for the site
 */
function wc1c_timezone_string()
{
	// If site timezone string exists, return it
	$timezone = get_option('timezone_string');

	if($timezone)
	{
		return $timezone;
	}

	// Get UTC offset, if it isn't set then return UTC
	$utc_offset = (int) get_option('gmt_offset', 0);
	if(0 === $utc_offset)
	{
		return 'UTC';
	}

	// Adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// Attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr('', $utc_offset);
	if($timezone)
	{
		return $timezone;
	}

	// Last try, guess timezone string manually
	foreach(timezone_abbreviations_list() as $abbr)
	{
		foreach($abbr as $city)
		{
			// WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone
			if((bool) date('I') === (bool) $city['dst'] && $city['timezone_id'] && (int) $city['offset'] === $utc_offset)
			{
				return $city['timezone_id'];
			}
		}
	}

	return 'UTC';
}

/**
 * Get timezone offset in seconds
 *
 * @return float
 */
function wc1c_timezone_offset()
{
	$timezone = get_option('timezone_string');

	if($timezone)
	{
		return (new DateTimeZone($timezone))->getOffset(new DateTime('now'));
	}

	return (float) get_option('gmt_offset', 0) * HOUR_IN_SECONDS;
}

/**
 * Is WC1C admin tools request?
 *
 * @param string $tool_id
 *
 * @return bool
 */
function is_wc1c_admin_tools_request($tool_id = '')
{
	if(true !== is_wc1c_admin_section_request('tools'))
	{
		return false;
	}

	if('' === $tool_id)
	{
		return true;
	}

	$get_tool_id = wc1c()->getVar($_GET['tool_id'], '');

	if($get_tool_id !== $tool_id)
	{
		return false;
	}

	return true;
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
	if(wc1c()->getVar($_GET['section'], '') !== $section)
	{
		return false;
	}

	if(wc1c()->request()->isWc1cAdmin())
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
function wc1c_admin_tools_get_url($tool_id = '')
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
function wc1c_admin_configurations_get_url($action = 'all', $configuration_id = '')
{
	$path = 'admin.php?page=wc1c&section=configurations';

	if('all' !== $action)
	{
		$path .= '&do_action=' . $action;
	}

	if('' === $configuration_id)
	{
		return admin_url($path);
	}

	$path .= '&configuration_id=' . $configuration_id;

	return admin_url($path);
}