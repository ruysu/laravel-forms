<?php namespace Ruysu\Forms\Renderers;

use Ruysu\Forms\FormField;

class BootstrapRenderer implements RendererInterface {
	public function render(FormField $field, $value = null, array $attributes = array()) {
		$field->addClass('form-control');
		$input = $field->getInput($value, $attributes);
		$label = $field->getLabel(['class' => 'form-label']);
		$error = $field->getError();
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