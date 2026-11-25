<?php

namespace WPFormsSlack\Provider\Actions;

use WPFormsSlack\Api\Api;
use WPFormsSlack\Provider\FieldMapper;

/**
 * Class Action.
 *
 * @since 1.0.0
 */
abstract class Action {

	/**
	 * Connection with the Slack API.
	 *
	 * @since 1.0.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * Field mapper.
	 *
	 * @since 1.0.0
	 *
	 * @var FieldMapper
	 */
	protected $field_mapper;

	/**
	 * Connection data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $connection_data;

	/**
	 * Form data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $form_data;

	/**
	 * Form fields.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Action constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Api         $api             Slack API connection.
	 * @param FieldMapper $field_mapper    Field mapper.
	 * @param array       $connection_data Connection data.
	 * @param array       $form_data       Form data.
	 * @param array       $fields          Form fields.
	 */
	public function __construct( Api $api, FieldMapper $field_mapper, array $connection_data, array $form_data, array $fields ) {

		$this->api             = $api;
		$this->field_mapper    = $field_mapper;
		$this->connection_data = $connection_data;
		$this->form_data       = $form_data;
		$this->fields          = $fields;
	}

	/**
	 * Run action.
	 *
	 * @since 1.0.0
	 */
	abstract public function run();
}
