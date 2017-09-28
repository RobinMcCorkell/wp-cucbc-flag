<?php

/*
Plugin Name: CUCBC River Status
Version: 0.1.0
Description: Displays the current CUCBC flag status and hours of darkness.
Plugin URI: https://github.com/Xenopathic/wp-cucbc-flag
Author: Robin McCorkell
Author URI: https://github.com/Xenopathic
License: Public Domain
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WP_CUCBC_Flag extends WP_Widget {
	public function __construct() {
		$widget_ops = [
			'classname' => 'wp_cucbc_flag',
			'description' => 'CUCBC River Status',
		];
		parent::__construct('wp_cucbc_flag', 'CUCBC River Status', $widget_ops);
	}

	public function widget($args, $instance) {
		wp_enqueue_style('wp_cucbc_flag');
		$filename = 'http://www.cucbc.org/flag.txt';

		$flag_raw = "";
		$flag = "";
		$date = "";
		$lightings = array("","","","");

		// Get the flag status from CUCBC
		if ($fp = fopen($filename, "r")) {
			while(!feof($fp)) {
				$flag_raw = fgets($fp, 1024);
			}
			fclose($fp);
		} else {
			$flag = "";
		}

		// Output appropriate flag
		switch($flag_raw) {
		case("grn"):
			$flag = "green";
			break;
		case("yel"):
			$flag = "yellow";
			break;
		case("ryl"):
			$flag = "red/yellow";
			break;
		case("red"):
			$flag = "red";
			break;
		case("blu"):
			$flag = "Cambridge Blue";
			break;
		case("gdb"):
			$flag = "GDBO!";
			break;
		case("nop"):
			$flag = "not operational";
			break;
		default:
			$flag = "unavailable";
			$flag_raw = "nop"; // Use the grey flag icon if flag status is unavailable
		}

		// Get today's & tomorrw's lightings from CUCBC
		$filename = 'http://www.cucbc.org/darkness.txt';
		if($fp = fopen($filename, "r")) {
			while(!feof($fp)) {
				$date = fgets($fp, 1024);
				$lightings[0] = fgets($fp, 1024);
				$lightings[1] = fgets($fp, 1024);
				$lightings[2] = fgets($fp, 1024);
				$lightings[3] = fgets($fp, 1024);
			}
			fclose($fp);
			$lighting_up = plugins_url('images/lighting_up.gif', __FILE__);
			$lighting_down = plugins_url('images/lighting_down.gif', __FILE__);
			$lightingtable = '<table style="border-collapse:collapse;width:100%;" cell-spacing=0>'
				.'<tr><td>Today</td><td><img src="'.$lighting_down.'" alt="Lighting Down" style="vertical-align:text-bottom;"> '.$lightings[0].'</td>'
				.'<td><img src="'.$lighting_up.'" alt="Lighting Up" style="vertical-align:text-bottom;"> '.$lightings[1].'</td></tr>'
				.'<tr><td>Tomorrow</td><td><img src="'.$lighting_down.'" alt="Lighting Down" style="vertical-align:text-bottom;"> '.$lightings[2].'</td>'
				.'<td><img src="'.$lighting_up.'" alt="Lighting Up" style="vertical-align:text-bottom;"> '.$lightings[3].'</td></tr></table>';
		} else {
			$lightingtable = "<p><em>Sorry, lighting times unavailable</em></p>";
		}

		// Output HTML
		$flag_url = plugins_url('images/flag/'.$flag_raw.'.gif', __FILE__);
?>
<?= $args['before_widget'] ?>
	<?= $args['before_title'] ?>River Status<?= $args['after_title'] ?>
	<p><img class="wp_cucbc_flag_flag" src="<?= $flag_url ?>" />Flag is <strong><?= $flag ?></strong></p>
	<?= $lightingtable ?>
<?= $args['after_widget'] ?>
<?php
	}
}

add_action( 'widgets_init', function(){
	wp_register_style('wp_cucbc_flag', plugins_url('style.css', __FILE__));
	register_widget( 'WP_CUCBC_Flag' );
});
