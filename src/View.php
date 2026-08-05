<?php
/**
 * A very simple templating class
 */

namespace Paytrail\WooCommercePaymentGateway;

/**
 * View
 */
class View {

	/**
	 * The template to render.
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * The data to render the view with.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param string $template The template to render.
	 */
	public function __construct( $template ) {
		$this->template = $this->get_template_path( $template );
	}

	/**
	 * Render the wanted template with given data.
	 *
	 * @param mixed $data The data to render the view with.
	 * @return void
	 */
	public function render( $data = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- $data is consumed by the required template.
		// @codingStandardsIgnoreLine
		require $this->template;
	}

	/**
	 * Get a complate template path by a template name.
	 *
	 * @param string $template Template name to work with.
	 * @return string The complete path.
	 *
	 * @throws \Exception An exception if the template file given was not found.
	 */
	protected function get_template_path( $template ) {
		$plugin_instance = Plugin::instance();

		$plugin_dir = $plugin_instance->get_plugin_dir();

		$template_file = $plugin_dir . '/src/View/' . $template . '.php';

		// Check the existence of the template.
		if ( file_exists( $template_file ) ) {
			return $template_file;
		} else {
			throw new \Exception( esc_html( "Template $template ($template_file) could not be found." ) );
		}
	}
}
