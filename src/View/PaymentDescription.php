<?php
/**
 * Checkout payment description
 */

// Ensure that the file is being run within the WordPress context.
if (! defined('ABSPATH')) {
	die;
}
if (!$data['description']) {
	return;
}
?>
<p><?php echo esc_html($data['description']); ?> </p>
