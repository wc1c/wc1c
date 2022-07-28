<?php namespace Wc1c\Schemas\Productscml;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * Admin
 *
 * @package Wc1c\Schemas\Productscml
 */
class Admin
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var Core Schema core
	 */
	protected $core;

	/**
	 * @return Core
	 */
	public function core()
	{
		return $this->core;
	}

	/**
	 * @param Core $core
	 */
	public function setCore($core)
	{
		$this->core = $core;
	}

	/**
	 * @return void
	 */
	public function initConfigurationsFields()
	{
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsReceiver'], 10, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProducts'], 20, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsSync'], 30, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsCreate'], 40, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsUpdate'], 50, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsNames'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsDescriptions'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsImages'], 60, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsPrices'], 70, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsInventories'], 72, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsDimensions'], 74, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsWithCharacteristics'], 80, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsCategories'], 80, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsCategoriesClassifierGroups'], 85, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsAttributes'], 90, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsAttributesClassifierProperties'], 90, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsMediaLibrary'], 100, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsLogs'], 110, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsOther'], 120, 1);
	}

	/**
	 * Configurations fields: receiver
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsReceiver($fields)
	{
		$fields['title_receiver'] =
		[
			'title' => __('Receiving requests from 1C', 'wc1c'),
			'type' => 'title',
			'description' => __('Authorization of requests and regulation of algorithms for receiving requests for the Receiver from the 1C programs by CommerceML protocol.', 'wc1c'),
		];

		$lazy_sign = $this->core()->configuration()->getMeta('receiver_lazy_sign');

		if(empty($lazy_sign))
		{
			$lazy_sign = md5($this->core()->configuration()->getId() . time());
			$this->core()->configuration()->addMetaData('receiver_lazy_sign', $lazy_sign, true);
			$this->core()->configuration()->saveMetaData();
		}

		$url_raw = get_site_url(null, '/?wc1c-receiver=' . $this->core()->configuration()->getId() . '&lazysign=' . $lazy_sign . '&get_param');
		$url_raw = '<p class="input-text p-2 bg-light regular-input wc1c_urls">' . esc_html($url_raw) . '</p>';

		$fields['url_requests'] =
		[
			'title' => __('Website address', 'wc1c'),
			'type' => 'raw',
			'raw' => $url_raw,
			'description' => __('Specified in the exchange settings on the 1C side. The Recipient is located at this address, which will receive requests from 1C. When copying, you need to get rid of whitespace characters, if they are present.', 'wc1c'),
		];

		$fields['user_login'] =
		[
			'title' => __('Username', 'wc1c'),
			'type' => 'text',
			'description' => __('Specified in 1C when setting up an exchange with a site on the 1C side. At the same time, work with data on the site is performed on behalf of the configuration owner, and not on behalf of the specified username.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		$fields['user_password'] =
		[
			'title' => __('User password', 'wc1c'),
			'type' => 'password',
			'description' => __('Specified in 1C paired with a username when setting up an exchange with a site on the 1C side. It is advisable not to specify the password for the current WordPress user.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		$fields['receiver_check_auth_key_disabled'] =
		[
			'title' => __('Request signature verification', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox to disable request signature verification. By default, validation is performed.', 'wc1c'),
			'description' => __('The setting disables authentication of requests from 1C. May be required only for very old versions of 1C. Enable only if there are errors in the request signature verification in the logs. If disabled, signature verification will be performed using the lazy signature from the lazysign parameter.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: other
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsOther($fields)
	{
		$fields['title_other'] =
		[
			'title' => __('Other parameters', 'wc1c'),
			'type' => 'title',
			'description' => __('Change of data processing behavior for environment compatibility and so on.', 'wc1c'),
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum size of accepted requests', 'wc1c'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<br />%s <b>%s</b><br />%s',
				__('Enter the maximum size of accepted requests from 1C at a time in bytes. May be specified with a dimension suffix, such as 7M, where M = megabyte, K = kilobyte, G - gigabyte.', 'wc1c'),
				__('Current WC1C limit:', 'wc1c'),
				wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
				__('Can only decrease the value, because it must not exceed the limits from the WC1C settings.', 'wc1c')
			),
			'default' => wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
			'css' => 'min-width: 100px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: categories
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsCategories($fields)
	{
		$fields['categories'] =
		[
			'title' => __('Categories', 'wc1c'),
			'type' => 'title',
			'description' => __('Categorization of product positions on the WooCommerce side according to data from 1C.', 'wc1c'),
		];

		$merge_options =
		[
			'no' => __('Do not use', 'wc1c'),
			'yes' => __('Name matching', 'wc1c'),
			'yes_parent' => __('Name matching, with the match of the parent category', 'wc1c'),
		];

		$fields['categories_merge'] =
		[
			'title' => __('Using existing categories', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			('%s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><hr>%s',
			 __('In the event that the categories were created manually or from another configuration, you must enable the merge. Merging will avoid duplication of categories.', 'wc1c'),
			 __('Name matching', 'wc1c'),
			 __('The categories will be linked when the names match without any other data matching.', 'wc1c'),
			 __('Name matching, with the match of the parent category', 'wc1c'),
			 __('The categories will be linked only if they have the same name and parent category.', 'wc1c'),
			 __('The found categories will be updated according to 1C data according to the update settings. If not want to refresh the data, must enable refresh based on the configuration.', 'wc1c')
			),
			'default' => 'no',
			'options' => $merge_options
		];

		$fields['categories_create'] =
		[
			'title' => __('Creating categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Categories are only created if they are recognized as new. New categories are those that are not related according to 1C data and are not in an identical hierarchy.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_update'] =
		[
			'title' => __('Updating categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the category created earlier was linked to 1C data, then when you change any category data in 1C, the data will also change in WooCommerce.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_update_only_configuration'] =
		[
			'title' => __('Consider configuration when updating categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When updating category data, the update will only occur if the category was created through the current configuration.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_update_only_schema'] =
		[
			'title' => __('Consider schema when updating categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When updating category data, the update will only occur if the category was created through the current schema.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: categories from classifier groups
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsCategoriesClassifierGroups($fields)
	{
		$fields['categories_classifier_groups'] =
		[
			'title' => __('Categories: classifier groups', 'wc1c'),
			'type' => 'title',
			'description' => __('Create and update categories based on groups from the classifier.', 'wc1c'),
		];

		$fields['categories_classifier_groups_create'] =
		[
			'title' => __('Creating categories from classifier groups', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Categories are only created if they have not been created before. Also, if access to work with categories is allowed from the global settings.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_classifier_groups_create_assign_parent'] =
		[
			'title' => __('Assign parent categories on creating', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If there is a parent category in 1C, it will also be assigned in WooCommerce. The setting is triggered when a category is created.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['categories_classifier_groups_create_assign_description'] =
		[
			'title' => __('Assign categories description on creating', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When creating categories, descriptions will be filled in if category descriptions are present in 1C.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_classifier_groups_update'] =
		[
			'title' => __('Updating categories from classifier groups', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the category created earlier was linked to 1C data, then when you change any category data in 1C, the data will also change in WooCommerce.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_classifier_groups_update_parent'] =
		[
			'title' => __('Update parent categories on updating', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When enabled, parent categories will be updated when they are updated in 1C. The setting is triggered when a category is updated.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['categories_classifier_groups_update_name'] =
		[
			'title' => __('Updating categories name', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the category was previously linked to 1C data, then when changing the name in 1C, the name will also change in WooCommerce.', 'wc1c'),
			'default' => 'no'
		];

		$fields['categories_classifier_groups_update_description'] =
		[
			'title' => __('Updating categories description', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the category was previously linked to 1C data, then when you change the description in 1C, the description will also change in WooCommerce. 
			It should be borne in mind that descriptions in 1C are not always stored. Therefore, you should not enable this function if the descriptions were filled out on the site.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products with characteristics
	 *
	 * @param array $fields Прежний массив настроек
	 *
	 * @return array Новый массив настроек
	 */
	public function configurationsFieldsProductsWithCharacteristics($fields)
	{
		$fields['title_products_with_characteristics'] =
		[
			'title' => __('Products (goods): with characteristics', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s %s',
				__('The same product (product) can have various kinds of differences, such as color, size, etc.', 'wc1c'),
				__('In 1C programs, these differences can be presented in the form of characteristics.', 'wc1c'),
				__('This section of the settings regulates the behavior of the processing of such characteristics on the Woocommerce side.', 'wc1c')
			)
		];

		$fields['products_with_characteristics'] =
		[
			'title' => __('Using characteristics', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<br/>%s %s<br /><hr>%s',
				__('When turning on, products with characteristics will processing on the basis of settings for products.', 'wc1c'),
				__('At the same time, products are divided into simple and variable. Work with simple products will occur when the parent is not found.', 'wc1c'),
				__('The search for the parent product takes place according to a unique identifier of 1C. Search for simple products is carried out in all available settings for synchronization.', 'wc1c'),
				__('With the option disconnected, all the data of products with characteristics will be simply missed. Neither the creation, nor update and no other processing will be.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['products_with_characteristics_use_attributes'] =
		[
			'title' => __('Using global attributes for products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<br /><hr>%s',
				__('It will be allowed to create global attributes and then add values based on product characteristics.', 'wc1c'),
				__('If the setting is disabled, either existing attributes or attributes at the product level will be used.', 'wc1c')
			),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: attributes
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsAttributes($fields)
	{
		$fields['attributes'] =
		[
			'title' => __('Attributes', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s %s',
				__('General (global) attributes are used for all products.', 'wc1c'),
				__('Work with individual product attributes is configured at the product level.', 'wc1c'),
				__('These settings only affect the global attributes. As a rule, there is no deletion of global attributes and their values. Removal operations are performed manually or through a cleaner.', 'wc1c')
			)
		];

		$fields['attributes_create'] =
		[
			'title' => __('Creating attributes', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('It will be allowed to add common attributes for products based on characteristics, properties and other data according to the other setting sections.', 'wc1c'),
				__('Creation will only occur if the attribute has not been previously created. Verification is possible by: name, identifier from 1C, etc. The default is to match by name.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['attributes_update'] =
		[
			'title' => __('Updating attributes', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('It will be allowed to update common attributes for products based on characteristics, properties and other data according to the other setting sections.', 'wc1c'),
				__('Attribute updating refers to adding product attribute values based on product characteristics, classifier properties, and other data specified in the settings. If you disable this feature, work will only occur with existing attribute values without updating attribute data. In some cases, updating refers to sorting and renaming the attributes themselves.', 'wc1c')
			),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: attributes
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsAttributesClassifierProperties($fields)
	{
		$fields['attributes_classifier_properties'] =
		[
			'title' => __('Attributes: classifier properties', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s',
				__('Adding and updating global attributes for products from classifier properties.', 'wc1c'),
				__('The properties are contained both in the classifier of the offer package and the product catalog.', 'wc1c')
			),
		];

		$fields['attributes_create_by_classifier_properties'] =
		[
			'title' => __('Creating attributes from classifier properties', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The creation will occur when processing the properties of the classifier. Creation occurs only if there is no attribute with the specified name or associated identifier.', 'wc1c'),
				__('If disable the creation of attributes and create some attributes manually, it is possible to adding values to them.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['attributes_values_by_classifier_properties'] =
		[
			'title' => __('Adding values to attributes from classifier properties', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Adding product attribute values based on classifier property values.', 'wc1c'),
				__('The value is added only if it is absent: by name.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['attributes_values_by_product_properties'] =
		[
			'title' => __('Adding values to attributes from product properties', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('Classifier properties do not always contain values in the reference. When the setting is enabled, values will be added based on the values of the product properties.', 'wc1c'),
				__('The value is added only if it is absent: by name.', 'wc1c'),
				__('The value is added only if it is missing. If do not add a value, the attribute will be skipped.', 'wc1c')
			),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products sync
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsSync($fields)
	{
		$fields['product_sync'] =
		[
			'title' => __('Products (goods): synchronization', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			('%s <br /> %s',
			    __('Dispute resolution between existing products (goods) on the 1C side and in WooCommerce. For extended matching (example by SKU), must use the extension.', 'wc1c'),
				__('Products not found by sync keys will be treated as new. Accordingly, the rules for creating products will apply to them.', 'wc1c')
			),
		];

		$fields['product_sync_by_id'] =
		[
			'title' => __('By external ID from 1C', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable. Enabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr> %s',
				__('When creating new products based on data from 1C, a universal global identifier from 1C is filled in for them. Can also fill in global identifiers manually for manually created products.', 'wc1c'),
				__('Enabling the option allows you to use the filled External ID to mark products (goods) as existing, and thereby run algorithms to update them.', 'wc1c')
			),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProducts($fields)
	{
		$fields['title_products'] =
		[
			'title' => __('Products (goods)', 'wc1c'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for products. Operations on products are based on data from product catalogs and offer packages described in CommerceML.', 'wc1c'),
		];

		$fields['products_create'] =
		[
			'title' => __('Creation of products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable the creation of new products upon request from 1C. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<br /><hr>%s',
				__('The products is only created if it is not found in WooCommerce when searching by criteria for synchronization.', 'wc1c'),
				__('The option works only with automatic creation of products. When disabled, it is still possible to manually create products through ManualCML and similar extensions.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['products_update'] =
		[
			'title' => __('Update of products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable product updates on demand from 1C. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<br /><hr>%s',
				__('Products are updated only if they were found using the product synchronization keys.', 'wc1c'),
				__('The option works only with automatic updating of products. When disabled, it is still possible to manually update products through ManualCML and similar extensions.', 'wc1c')
			),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products prices
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsPrices($fields)
	{
		$fields['title_products_prices'] =
		[
			'title' => __('Products (goods): prices', 'wc1c'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating prices.', 'wc1c'),
		];

		$products_prices_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c'),
			'yes_primary' => __('From first found', 'wc1c'),
			'yes_name' => __('From specified name', 'wc1c'),
		];

		$fields['products_prices_regular_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: regular', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The found price after use will not be available for selection as a sale price.', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the regular prices data from CommerceML data will be skipped.', 'wc1c'),
				__('From first found', 'wc1c'),
				__('The first available price of all available prices for the product will be used as the regular price.', 'wc1c'),
				__('From specified name', 'wc1c'),
				__('The price with the specified name will be used as the regular price. If the price is not found by name, no value will be assigned.', 'wc1c')
			),
			'default' => 'no',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_regular_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: regular - name in 1C', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the name of the base price in 1C, which is used for filling to WooCommerce as the base price.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		$fields['products_prices_sale_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: sale', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The sale price must be less than the regular price. Otherwise, it simply wont apply.', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the sale prices data from CommerceML data will be skipped.', 'wc1c'),
				__('From first found', 'wc1c'),
				__('The first available price of all available prices for the product will be used as the sale price.', 'wc1c'),
				__('From specified name', 'wc1c'),
				__('The price with the specified name will be used as the sale price. If the price is not found by name, no value will be assigned.', 'wc1c')
			),
			'default' => 'no',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_sale_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: sale - name in 1C', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the name of the sale price in 1C, which is used for filling to WooCommerce as the sale price.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products names
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsNames($fields)
	{
		$fields['title_products_names'] =
		[
			'title' => __('Products (goods): names', 'wc1c'),
			'type' => 'title',
			'description' => __('Sources and algorithms for filling out product names.', 'wc1c'),
		];

		$products_names_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c'),
			'name' => __('From the standard name', 'wc1c'),
			'full_name' => __('From the full name', 'wc1c'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c'),
		];

		$fields['products_names_by_cml'] =
		[
			'title' => __('Names based on CommerceML data', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the name data from CommerceML data will be skipped. If a product is updating, then its current name will not be updated.', 'wc1c'),
				__('From the standard name', 'wc1c'),
				__('This name is contained in the standard name of 1C products. It is located in the conditional tag - name.', 'wc1c'),
				__('From the full name', 'wc1c'),
				__('In 1C it is presented in the form of the Full name of the nomenclature. Unloaded as a requisite with the appropriate name.', 'wc1c'),
				__('From requisite with the specified name', 'wc1c'),
				__('The name data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c')
			),
			'default' => 'name',
			'options' => $products_names_by_cml_options
		];

		$fields['products_names_from_requisites_name'] =
		[
			'title' => __('Names based on CommerceML data: name for requisite', 'wc1c'),
			'type' => 'text',
			'description' => __('The name of the requisite of the product (goods) which contains a name of the product.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products descriptions
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsDescriptions($fields)
	{
		$fields['title_products_descriptions'] =
		[
			'title' => __('Products (goods): descriptions', 'wc1c'),
			'type' => 'title',
			'description' => __('Sources and algorithms for filling out product descriptions, both short descriptions and full descriptions.', 'wc1c'),
		];

		$products_descriptions_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c'),
			'yes' => __('From the standard description', 'wc1c'),
			'yes_html' => __('From the HTML description', 'wc1c'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c'),
		];

		$fields['products_descriptions_short_by_cml'] =
		[
			'title' => __('Descriptions based on CommerceML data: short', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the short description data from CommerceML data will be skipped. If a product is updating, then its current short description will not be updated.', 'wc1c'),
				__('From the standard description', 'wc1c'),
				__('This description is contained in the standard description of 1C products. It is located in the conditional tag - description.', 'wc1c'),
				__('From the HTML description', 'wc1c'),
				__('Standard description, in HTML format only. Unloaded in a short description if there is a checkmark in 1C - Description in HTML format.', 'wc1c'),
				__('From requisite with the specified name', 'wc1c'),
				__('The short description data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c')
			),
			'default' => 'yes',
			'options' => $products_descriptions_by_cml_options
		];

		$fields['products_descriptions_short_from_requisites_name'] =
		[
			'title' => __('Descriptions based on CommerceML data: short - name for requisite', 'wc1c'),
			'type' => 'text',
			'description' => __('The name of the requisite of the product (goods) which contains a short description of the product.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		$fields['products_descriptions_by_cml'] =
		[
			'title' => __('Descriptions based on CommerceML data: full', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Filling in full description data from CommerceML data will be skipped. If a product is updating, then its current full description will not be updated.', 'wc1c'),
				__('From the standard description', 'wc1c'),
				__('This description is contained in the standard description of 1C products. It is located in the conditional tag - description.', 'wc1c'),
				__('From the HTML description', 'wc1c'),
				__('Standard description, in HTML format only. It is unloaded when there is a checkmark in 1C - Description in HTML format.', 'wc1c'),
				__('From requisite with the specified name', 'wc1c'),
				__('The full description data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c')
			),
			'default' => 'yes_html',
			'options' => $products_descriptions_by_cml_options
		];

		$fields['products_descriptions_from_requisites_name'] =
		[
			'title' => __('Descriptions based on CommerceML data: full - name for requisite', 'wc1c'),
			'type' => 'text',
			'description' => __('The name of the requisite of the product (goods) which contains a full description of the product.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products images
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsImages($fields)
	{
		$fields['title_products_images'] =
		[
			'title' => __('Products (goods): images', 'wc1c'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with images of products (goods).', 'wc1c'),
		];

		$fields['products_images_by_cml'] =
		[
			'title' => __('Images based on CommerceML files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('When enabled, work with images based on CommerceML files will be allowed.', 'wc1c'),
				__('Available images in CommerceML files for products will be populated for future use.', 'wc1c'),
				__('In this case, the image files themselves must first be added to the WordPress media library. If they are not included, their use will be skipped.', 'wc1c')
			),
			'default' => 'no'
		];

		$fields['products_images_by_cml_max'] =
		[
			'title' => __('Images based on CommerceML files: maximum quantity', 'wc1c'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The maximum number of images to be processed. The excess number will be ignored. To remove the limit, specify - 0. The limit is necessary for weak systems.', 'wc1c'),
				__('If you specify one image, it will be uploaded as the main one without adding the rest to the product gallery.', 'wc1c')
			),
			'default' => '10',
			'css' => 'min-width: 60px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: media library
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsMediaLibrary($fields)
	{
		$fields['title_media_library'] =
		[
			'title' => __('Media library', 'wc1c'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with WordPress media library.', 'wc1c'),
		];

		$fields['media_library'] =
		[
			'title' => __('Using the media library', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('All file handling capabilities available to the library will be enabled. If disabled, no actions will be performed on files in the library through the schema.', 'wc1c'),
			'default' => 'no'
		];

		$fields['media_library_images_by_receiver'] =
		[
			'title' => __('Images based on Receiver', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('All image files sent to Receiver files will be added to the WordPress media library.', 'wc1c'),
				__('These images can later be used to populate product images.', 'wc1c'),
				__('When adding an image, it will be assigned the identifier of the current configuration, as well as the identifier of the scheme and the path of being in 1C.', 'wc1c')
			),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products inventories
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsInventories($fields)
	{
		$fields['title_products_inventories'] =
		[
			'title' => __('Products (goods): inventories', 'wc1c'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating inventories based on data from the offer package.', 'wc1c'),
		];

		$fields['products_inventories_by_offers_quantity'] =
		[
			'title' => __('Filling inventories based on quantity from offers', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('It will be allowed to fill in the quantity of product stocks in WooCommerce based on the quantity received in 1C offers.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products dimensions
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsDimensions($fields)
	{
		$fields['title_products_dimensions'] =
		[
			'title' => __('Products (goods): dimensions', 'wc1c'),
			'type' => 'title',
			'description' => __('The main settings for filling in the dimensions of products (goods) according to data from 1C. Dimensions include: weight, length, width, height.', 'wc1c'),
		];

		$fields['products_dimensions_by_requisites'] =
		[
			'title' => __('Filling dimensions based on requisites', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Filling in the dimensions will be performed from the given details of the products. For the setting to work, you must specify the correspondence of the details in the fields below.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_dimensions_by_requisites_weight_from_name'] =
		[
			'title' => __('Dimensions based on requisites: weight', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the weight in 1C, which is used for filling to WooCommerce as the weight.', 'wc1c'),
			'default' => __('Weight', 'wc1c'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_length_from_name'] =
		[
			'title' => __('Dimensions based on requisites: length', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the length in 1C, which is used for filling to WooCommerce as the length.', 'wc1c'),
			'default' => __('Length', 'wc1c'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_width_from_name'] =
		[
			'title' => __('Dimensions based on requisites: width', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the width in 1C, which is used for filling to WooCommerce as the width.', 'wc1c'),
			'default' => __('Width', 'wc1c'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_height_from_name'] =
		[
			'title' => __('Dimensions based on requisites: height', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the height in 1C, which is used for filling to WooCommerce as the height.', 'wc1c'),
			'default' => __('Height', 'wc1c'),
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: logs
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsLogs($fields)
	{
		$fields['title_logger'] =
		[
			'title' => __('Event logs', 'wc1c'),
			'type' => 'title',
			'description' => __('Maintaining event logs for the current configuration. You can view the logs through the extension or via FTP.', 'wc1c'),
		];

		$fields['logger_level'] =
		[
			'title' => __('Level for events', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => '300',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wc1c'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 30 days.', 'wc1c'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products create
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsCreate($fields)
	{
		$fields['title_products_create'] =
		[
			'title' => __('Products (goods): creating', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s',
				__('Regulation of algorithms for creating products in WooCommerce according to 1C data.', 'wc1c'),
				__('These settings only apply to the creation of new products, ie. those products that are not found by the keys for product synchronization.', 'wc1c')
			),
		];

		$product_statuses = get_post_statuses();
		unset($product_statuses['private']);

		$fields['products_create_status'] =
		[
			'title' => __('Status of the created product', 'wc1c'),
			'type' => 'select',
			'description' => __('Newly created products will have selected status. It is recommended to select the status: Draft.', 'wc1c'),
			'default' => 'draft',
			'options' => $product_statuses
		];

		$fields['products_create_stock_status'] =
		[
			'title' => __('The stock status of product created', 'wc1c'),
			'type' => 'select',
			'description' => __('Newly created products will have the selected stock status. It is recommended to select the status: Out of stock.', 'wc1c'),
			'default' => 'outofstock',
			'options' => wc_get_product_stock_status_options()
		];

		$fields['products_create_adding_category'] =
		[
			'title' => __('Assigning categories of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c'),
			'description' => __('When this setting is enabled, the processing of categories based on classifier groups must be enabled and configured.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_adding_attributes'] =
		[
			'title' => __('Assigning attributes of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c'),
			'description' => __('Newly created products will have attributes added based on the attribute settings. Attribute settings in a separate block.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_adding_sku'] =
		[
			'title' => __('Filling the SKU of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c'),
			'description' => __('The product SKU will be added according to data from 1C. It is recommended to enable this feature.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_adding_description'] =
		[
			'title' => __('Filling the description of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('In the data that came from 1C, there may be descriptions of products that will be placed in a short description.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_adding_description_full'] =
		[
			'title' => __('Filling a full description of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('The data received from 1C may contain full descriptions of products that will be placed in the full description.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_create_adding_images'] =
		[
			'title' => __('Adding the images of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c'),
			'description' => __('If the setting is disabled, new products will not be assigned images that come from 1C.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_adding_category_fill_parent'] =
		[
			'title' => __('Filling the parent categories of the created product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Fill in the categories that are higher in level for the product? It is recommended to enable this setting.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['products_create_set_featured'] =
		[
			'title' => __('Featured product', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('The created product will be marked as recommended.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_create_set_sold_individually'] =
		[
			'title' => __('Individual sale', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Enable to have the product sold individually in one order. Two units of a product in one order will be impossible to order.', 'wc1c'),
			'default' => 'no'
		];

		$options = wc_get_product_visibility_options();

		$fields['products_create_set_catalog_visibility'] =
		[
			'title' => __('Product visibility', 'wc1c'),
			'type' => 'select',
			'description' => __('This setting determines which pages products will be displayed on.', 'wc1c'),
			'default' => 'visible',
			'options' => $options
		];

		$fields['products_create_set_reviews_allowed'] =
		[
			'title' => __('Allow reviews', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('It will be allowed to leave reviews for created products.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products update
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsUpdate($fields)
	{
		$fields['title_products_update'] =
		[
			'title' => __('Products (goods): updating', 'wc1c'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s',
				__('Regulation of algorithms for updating products in WooCommerce according to 1C data.', 'wc1c'),
				__('These settings only apply to the updating of old products, ie. those products that are found by the keys for product synchronization.', 'wc1c')
			),
		];

		$fields['products_update_only_configuration'] =
		[
			'title' => __('Consider configuration when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When updating products data, the update will only occur if the product was created through the current configuration.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_only_schema'] =
		[
			'title' => __('Consider schema when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When updating products data, the update will only occur if the product was created through the current schema.', 'wc1c'),
			'default' => 'no'
		];

		$default_statuses =
		[
			'' => __('Do not update', 'wc1c')
		];

		$post_statuses = get_post_statuses();
		unset($post_statuses['private']);

		$statuses = array_merge($default_statuses, $post_statuses);

		$fields['products_update_status'] =
		[
			'title' => __('Product status update when requesting product updates', 'wc1c'),
			'type' => 'select',
			'description' => __('The selected status will be assigned to all upgraded products when requesting product upgrades.', 'wc1c'),
			'default' => '',
			'options' => $statuses
		];

		$stock_statuses =
		[
			'' => __('Do not update', 'wc1c')
		];

		$stock_statuses = array_merge($stock_statuses, wc_get_product_stock_status_options());

		$fields['products_update_stock_status'] =
		[
			'title' => __('Product stock status update when requesting product updates', 'wc1c'),
			'type' => 'select',
			'description' => __('Upgradable products will change the balance status to the selected option from the list. It is recommended to select the status: Out of stock. Residues in this case will be restored with further processing of the residues.', 'wc1c'),
			'default' => '',
			'options' => $stock_statuses
		];

		$fields['products_update_categories'] =
		[
			'title' => __('Product categories update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the setting is disabled, new categories will not be assigned to old products. Categories can be edited manually and the data will remain unchanged.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_attributes'] =
		[
			'title' => __('Product attributes update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Existing synced products will have their attributes updated based on their attribute settings. Attribute settings in a separate block.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_name'] =
		[
			'title' => __('Product name update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When changing the product name in 1C, the data will be changed on the site.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_sku'] =
		[
			'title' => __('Product SKU update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When changing the product SKU in 1C, the data will be changed on the site.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_description'] =
		[
			'title' => __('Product description update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When changing the product description in 1C, the data will be changed on the site.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_description_full'] =
		[
			'title' => __('Product full description update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When changing the product full description in 1C, the data will be changed on the site.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_images'] =
		[
			'title' => __('Product images update when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If the setting is disabled, new images will not be assigned to old products and the old ones will not be deleted either. In this case, you can edit images from WooCommerce.', 'wc1c'),
			'default' => 'no'
		];

		$fields['products_update_categories_fill_parent'] =
		[
			'title' => __('Filling the parent categories when requesting product updates', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Enabled by default.', 'wc1c'),
			'description' => __('Fill in the categories that are higher in level for the product? It is recommended to enable this setting.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}
}