<?php
if (!defined('ABSPATH')) exit;
$cssfix_front = get_option('cssfix_front');
$output .= '<style type="text/css">
				'.$cssfix_front.'
			</style>';
?>