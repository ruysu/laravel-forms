<?php namespace Ruysu\Forms\Renderers;

use Ruysu\Forms\FormField;

class BootstrapRenderer implements RendererInterface {
	public function render(FormField $field, $value = null, array $attributes = array()) {
		if (!in_array($field->type(), ['file', 'radio', 'checkbox'])) {
			$field->addClass('form-control');
		}

		$input = $field->getInput($value, $attributes);
		$label = $field->getLabel(['class' => 'form-label']);
		$error = $field->getError();

		if (in_array($field->type(), ['radio', 'checkbox'])) {
			$input = '<div class="' . $type . '"><label>' . $input . e($label) . '</label></div>';
			$label = '';
		}

		return '<div class="form-group">' . $label . $input . $error . '</div>';
	}

	public function before() {

	}

	public function after() {

	}

	public function submit($text = 'Send') {
		return '<button type="submit" class="btn btn-primary">Send</button>';
	}
}