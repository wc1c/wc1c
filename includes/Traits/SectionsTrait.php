<?php namespace Wc1c\Traits;

defined('ABSPATH') || exit;

/**
 * SectionsTrait
 *
 * @package Wc1c\Traits
 */
trait SectionsTrait
{
	/**
	 * @var array Sections
	 */
	private $sections = [];

	/**
	 * @var string Current section
	 */
	private $current_section = '';

	/**
	 * Get current section
	 *
	 * @return string
	 */
	public function getCurrentSection()
	{
		return $this->current_section;
	}

	/**
	 * Set current section
	 *
	 * @param string $current_section
	 */
	public function setCurrentSection($current_section)
	{
		$final = apply_filters(WC1C_ADMIN_PREFIX . 'init_sections_current', $current_section);

		$this->current_section = $final;
	}

	/**
	 * Initializing current section
	 *
	 * @return string
	 */
	public function initCurrentSection()
	{
		$current_section = !empty($_GET['section']) ? sanitize_title($_GET['section']) : '';

		if($current_section !== '')
		{
			$this->setCurrentSection($current_section);
		}

		return $this->getCurrentSection();
	}

	/**
	 * Initialization
	 *
	 * @param array $sections
	 */
	public function initSections($sections = [])
	{
		$default_sections = [];

		if(!empty($sections) && is_array($sections))
		{
			$default_sections = array_merge($default_sections, $sections);
		}

		$final = apply_filters(WC1C_ADMIN_PREFIX . 'init_sections', $default_sections);

		$this->setSections($final);
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function getSections()
	{
		return apply_filters(WC1C_ADMIN_PREFIX . 'get_sections', $this->sections);
	}

	/**
	 * Set sections
	 *
	 * @param array $sections
	 */
	public function setSections($sections)
	{
		// hook
		$sections = apply_filters(WC1C_ADMIN_PREFIX . 'set_sections', $sections);

		$this->sections = $sections;
	}
}