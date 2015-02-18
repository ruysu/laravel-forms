<?php namespace Ruysu\Forms\Renderers;

use Ruysu\Forms\FormField;

interface RendererInterface {
	public function render(FormField $field, $value = null, array $attributes = array());
	public function before();
	public function after();
	public function submit();
}