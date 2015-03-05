<?php namespace Ruysu\Forms;

use InvalidArgumentException;
use Illuminate\Html\FormBuilder as Form;
use Str;

class FormField {
	protected $name;
	protected $builder;
	protected $bag = 'default';
	protected $type = 'text';
	protected $label = false;
	protected $options = array();
	protected $attributes = array();

	public function __construct($name, $label = false, $type = 'text', $attributes = array()) {
		$this->name($name);
		$this->type($type);
		$this->label($label);
		$this->attributes((array) $attributes);
	}

	public function addClass($class) {
		$current_class = array_get($this->attributes, 'class', '');

		if(!in_array($class, explode(' ', $current_class))) {
			$this->attributes['class'] = trim($current_class . ' ' . $class);
		}
	}

	public function getInput($value = null, array $attributes = array()) {
		if (!$this->builder) {
			$this->builder = App::make('form');
		}

		$type = $this->type();
		$name = $this->name();
		$params = array('id' => Str::slug("{$this->bag}-{$name}"));
		$attributes = array_merge($params, $this->attributes(), $attributes);
		unset($attributes['value']);

		switch ($type) {
			case 'select':
				if (in_array('multiple', array_keys($attributes), true) || in_array('multiple', $attributes, true)) {
					$name .= '[]';
				}

				$input = $this->builder->select($name, $this->options(), $value, $attributes);
			break;
			case 'radiogroup':
			case 'checkboxgroup':
				$input = $this->builder->$type($name, $this->options());
			break;
			case 'radio':
			case 'checkbox':
				$input = $this->builder->$type($name, '1', null, $attributes);
			break;
			case 'file':
			case 'password':
				$input = $this->builder->$type($name, $attributes);
			break;
			default :
				$input = $this->builder->$type($name, $value, $attributes);
			break;
		}

		return $input;
	}

	public function getLabel(array $attributes = array()) {
		if (!$this->builder) {
			$this->builder = App::make('form');
		}

		if ($this->label()) {
			$name = $this->name();
			return $this->builder->label(Str::slug("{$this->bag}-{$name}"), $this->label(), $attributes);
		}

		return '';
	}

	public function getError(array $attributes = array()) {
		if (!$this->builder) {
			$this->builder = App::make('form');
		}

		$name = $this->name();

		return $this->builder->error($name, Str::slug("{$this->bag}-{$name}"), $attributes, $this->bag());
	}

	public function setBuilder(Form $builder) {
		$this->builder = $builder;
	}

	public function setAttribute($attribute, $value) {
		return array_set($this->attributes, $attribute, $value);
	}

	/* SETTERS AND GETTERS */

	public function name($name = null) {
		if($name === null) {
			return $this->name;
		}

		if (!is_string($name)) {
			throw new InvalidArgumentException('$name must be a string');
		}

		$this->name = $name;
		return $this;
	}

	public function type($type = null) {
		if($type === null) {
			return $this->type;
		}

		if (!is_string($type)) {
			throw new InvalidArgumentException('$type must be a string');
		}

		$this->type = $type;
		return $this;
	}

	public function bag($bag = null) {
		if($bag === null) {
			return $this->bag;
		}

		if (!is_string($bag)) {
			throw new InvalidArgumentException('$bag must be a string');
		}

		$this->bag = $bag;
		return $this;
	}

	public function label($label = null) {
		if($label === null) {
			return $this->label;
		}

		if (!(is_string($label) || $label === false)) {
			throw new InvalidArgumentException('$label must be a string or false if no label should be printed');
		}

		$this->label = $label;
		return $this;
	}

	public function attributes($attributes = null) {
		if($attributes === null) {
			return $this->attributes;
		}

		if (!is_array($attributes)) {
			throw new InvalidArgumentException('$attributes must be an array');
		}

		$this->attributes = $attributes;
		return $this;
	}

	public function options($options = null) {
		if($options === null) {
			return $this->options;
		}

		if (!is_array($options)) {
			throw new InvalidArgumentException('$options must be an array');
		}

		$this->options = $options;
		return $this;
	}

	/* echo the field */

	public function __tostring() {
		return $this->render();
	}

	public function __set($attribute, $value) {
		return $this->setAttribute($attribute, $value);
	}

	public function __get($attribute) {
		return array_get($this->attributes, $attribute, null);
	}
}
