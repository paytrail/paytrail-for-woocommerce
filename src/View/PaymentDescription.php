<?php
/**
 * Checkout payment description
 */

$allowedHtml = array(
	'a'          => array(
		'href'  => true,
		'title' => true,
	),
	'abbr'       => array(
		'title' => true,
	),
	'acronym'    => array(
		'title' => true,
	),
	'b'          => array(),
	'blockquote' => array(
		'cite' => true,
	),
	'cite'       => array(),
	'code'       => array(),
	'del'        => array(
		'datetime' => true,
	),
	'em'         => array(),
	'i'          => array(),
	'q'          => array(
		'cite' => true,
	),
	's'          => array(),
	'strike'     => array(),
	'strong'     => array(),
	'img'        => array(
		'src'      => true,
		'class'    => true,
		'alt'      => true,
		'title'    => true,
		'longdesc' => true,
	),
);

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! $data['description'] ) {
	return;
}
?>
<p><?php echo wp_kses( $data['description'], $allowedHtml ); ?> </p>
