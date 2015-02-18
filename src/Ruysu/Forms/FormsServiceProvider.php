<?php namespace Ruysu\Forms;

use Illuminate\Support\ServiceProvider;

class FormsServiceProvider extends ServiceProvider {
	protected $defer = false;

	public function register() {}

	public function boot() {
		$session = $this->app['session'];
		$builder = $this->app['form'];

		// register field type text
		$this->app['form']->macro('tel', function($name, $default = NULL, $attrs = array()) use ($builder) {
			return $builder->input('tel', $name, $default, $attrs);
		});

		// register error macro, to easily retrieve an error wrapped in a label for the given field
		$this->app['form']->macro('error', function ($name, $id = null, $attributes = array(), $namespace = 'default') use ($builder, $session) {
			$id = $id?:$name;
			$bag = $session->get('errors');
			$error = $bag ? ($bag->getBag($namespace)->first($name)?:false) : false;

			if ($error) {
				return $builder->label($id, $error, $attributes);
			}

			return false;
		});

		// register field type radiogroup
		$this->app['form']->macro('radiogroup', function($name, $options, $selected = null, $inline = true) use ($builder) {
			$current_value = $builder->getValueAttribute($name, $selected);
			$inputs = '';

			foreach ($options as $value => $option) {
				$checked = is_null($current_value) ? false : $current_value == $value;
				$inputs .= $inline ? '' : '<div class="radio">';
				$inputs .= "\n" . '<label' . ($inline ? ' class="radio-inline"' : '') . '>' . $builder->radio($name, $value, $checked) . ' ' . e($option) . '</label>';
				$inputs .= $inline ? '' : '</div>';
			}
			unset($value, $option);

			return '<div class="radio-group">' . $inputs . '</div>';
		});

		// register field type checkboxgroup
		$this->app['form']->macro('checkboxgroup', function($name, $options, $selected = null, $inline = true) use ($builder) {
			$name .= '[]';
			$current_value = $builder->getValueAttribute($name, $selected);
			$inputs = '';

			foreach ($options as $value => $option) {
				$checked = is_null($current_value) ? false : in_array($value, $current_value);
				$inputs .= $inline ? '' : '<div class="checkbox">';
				$inputs .= "\n" . '<label' . ($inline ? ' class="checkbox-inline"' : '') . '>' . $builder->checkbox($name, $value, $checked) . ' ' . e($option) . '</label>';
				$inputs .= $inline ? '' : '</div>';
			}
			unset($value, $option);

			return '<div class="checkbox-group">' . $inputs . '</div>';
		});
	}

	public function provides() {
		return array();
	}
}
