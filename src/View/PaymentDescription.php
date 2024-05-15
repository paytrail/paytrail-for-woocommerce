<?php
/**
 * Checkout payment description
 */

$allowedHtml = [
	'a'          => [
		'href'  => true,
		'title' => true
	],
	'abbr'       => [
		'title' => true
	],
	'acronym'    => [
		'title' => true
	],
	'b'          => [],
	'blockquote' => [
		'cite' => true
	],
	'cite'       => [],
	'code'       => [],
	'del'        => [
		'datetime' => true
	],
	'em'         => [],
	'i'          => [],
	'q'          => [
		'cite' => true
	],
	's'          => [],
	'strike'     => [],
	'strong'     => [],
	'img'        => [
		'src'      => true,
		'class'    => true,
		'alt'      => true,
		'title'    => true,
		'longdesc' => true
	]
];

// Ensure that the file is being run within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! $data['description'] ) {
	return;
}
?>
<p><?php echo wp_kses( $data['description'], $allowedHtml ); ?> </p>
