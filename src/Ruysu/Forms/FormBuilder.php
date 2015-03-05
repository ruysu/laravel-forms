<?php namespace Ruysu\Forms;

use Illuminate\Html\FormBuilder as Form;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Exception;

abstract class FormBuilder implements FormBuilderInterface {
	protected $builder;
	protected $request;
	protected $fields;
	protected $action;
	protected $method = 'post';
	private $renderer;

	public function __construct(Form $builder, Request $request) {
		$this->builder = $builder;
		$this->request = $request;
		$this->fields = new Collection;
		$this->boot();
	}

	public function addField(FormField $field) {
		$name = $field->name();
		$field->setBuilder($this->builder);
		$this->fields->put($name, $field);
	}

	protected function hasFile() {
		return $this->fields->filter(function($field) {
			return $field->type() == 'file';
		})->count() > 0;
	}

	public function open(array $attributes = array(), $renderer = null) {
		$rendererClass = $this->getRenderer($renderer);
		$this->renderer = new $rendererClass;

		return $this->builder->open(array_merge($attributes, ['method' => $this->method, 'url' => $this->action, 'files' => $this->hasFile()])) . $this->renderer->before();
	}

	public function close() {
		return $this->renderer->after() . $this->builder->close();
	}

	public function getFields() {
		return $this->fields;
	}

	public function getInput() {
		$input = [];

		foreach ($this->fields as $name => $field) {
			if(in_array($field->type(), ['checkbox', 'radio'])) {
				$input[$name] = $this->request->has($name);
			}
			elseif ($field->type() == 'file') {
				$input[$name] = $this->request->file($name);
			}
			else {
				$input[$name] = $this->request->get($name);
			}
		}

		return $input;
	}

	public function render(array $attributes = array(), $renderer = null) {
		$form = $this->open($attributes, $renderer);

		foreach ($this->fields as $key => $field) {
			$form .= $this->field($key);
		}

		$form .= $this->renderer->submit();

		$form .= $this->close();

		return $form;
	}

	public function field($key, $value = null, array $attributes = array()) {
		$field = $this->fields->get($key);
		return $this->renderer->render($field, $value, $attributes);
	}

	public function setAction($action) {
		if (!is_string($action)) {
			throw new InvalidArgumentException('$action must be a string');
		}

		$this->action = $action;
	}

	public function setMethod($method) {
		if (!(is_string($method) && in_array(strtolower($method), ['put', 'delete', 'patch', 'post', 'get']))) {
			throw new InvalidArgumentException('$method must be a valid HTTP method');
		}

		$this->method = $method;
	}

	protected function clearFields() {
		$keys = $this->fields->keys();

		foreach ($keys as $key) {
			$this->fields->forget($key);
		}
	}

	protected function getRenderer($renderer = null) {
		return $renderer ?: __NAMESPACE__ . '\Renderers\BootstrapRenderer';
	}

	public function __tostring() {
		try {
			return $this->render();
		}
		catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function __call($method, $args) {
		if (!method_exists($this, $method)) {
			return call_user_func_array([$this->builder, $method], $args);
		}
		else {
			return call_user_func_array([$this, $method], $args);
		}
	}
}
