<?php
/**
 * Checkout payment description
 */

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}
if (!$data['description']) {
    return;
}
?>
<p><?php echo $data['description'] ?> </p>
