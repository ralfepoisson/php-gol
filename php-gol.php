<?php
/**
 * Game Of Life (PHP Implementation by Ralfe Poisson)
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.0
 * @copyright Copyright (C) Ralfe Poisson 2012
 * @license GPLv3
 */

# ==============================================================================
# SCRIPT SETUP
# ==============================================================================

# Start the Session
session_start();

# Configuration
$size = 200;
$world = array();
$new_world = array();
$rand_sample = 10000;
$rand_alpha = 9979;
$rule1 = 2;
$rule2 = 3;
$cell_size = "4px";

# ==============================================================================
# FUNCTIONS
# ==============================================================================

/**
 * This is the default function that is run the first time this script is called.
 * This function creates an initial state of the world, and displays it within an HTML structure.
 */
function display() {
	# Global Variables
	global $world, $cell_size;
	
	# Create Initial Random Chaos within the world
	start();
	$_SESSION['world'] = $world;
	
	# Create a graphical representation of the world
	$content = display_world($world);
	
	# Generaet the HTML for the page
	$html = "
	<html>
		<head>
			<title>Game of Life | Implementation by Ralfe Poisson</title>
			<style>
				* {
					font-size: 10px;
				}
				table tr td {
					width: {$cell_size};
					height: {$cell_size};
					font-size: 1px;
				}
			</style>
			<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
			<script>
				function refresh_gof() {
					var new_html = $.ajax({
						url: 'php-gol.php?action=refresh',
						type: 'html',
						async: false
					}).responseText;
					$('#gof').html(new_html);
				}
				setInterval('refresh_gof()', 1000);
			</script>
		</head>
		<body>
			<div id='gof'>
				{$content}
			</div>
		</body>	
	</html>";
	
	# Display the html page
	print $html;
}

/**
 * This function generates an HTML Table representation of the 2D World array.
 * @param Array $w: 2D Array of the World to display
 * @return String $html
 */
function display_world($w) {
	# Local Variables
	$html = "";
	$contents = "";
	
	# Generate Cells and Rows based on the World 2D Array
	foreach ($w as $row) {
		$contents .= "<tr>";
		foreach ($row as $cell) {
			$contents .= ($cell)? "<td style='background-color:black'>&nbsp;</td>" : "<td>&nbsp;</td>";
		}
		$contents .= "</tr>\n";
	}
	
	# Compile the HTML Table representation
	$html = "
		<table cellpadding='0' cellspacing='0' border='0'>
			{$contents}
		</table>";
	
	# Return the HTML Table representation of the World
	return $html;
}

# ==============================================================================
# CALCULATION FUNCTIONS
# ==============================================================================

/**
 * Calculates the next 'tick' or iteration of the World's evolution.
 * !! THIS IS WHERE THE MAGIC HAPPENS !!
 * @param Array $w: 2D Array of the World to evolve
 * @return Array $a: The next step in the World's evolution
 */
function calc_new_world($w) {
	# Global Variables
	global $size, $rule1, $rule2;
	
	# Local Variables
	$i = 0;
	$j = 0;
	$a = array();
	
	# Cycle through cells (i = row | j = column)
	for ($i=0; $i < $size; $i++) {
		for ($j = 0; $j < $size; $j++) {
			# Count how many neighbours the current cell has
			$neighbours	= $w[$j - 1][$i + 0]
						+ $w[$j + 1][$i + 0]
						+ $w[$j + 0][$i - 1]
						+ $w[$j + 0][$i + 1]
						+ $w[$j - 1][$i - 1]
						+ $w[$j - 1][$i + 1]
						+ $w[$j + 1][$i - 1]
						+ $w[$j + 1][$i + 1];
			
			# Rule 1: If the current cell has $rule1 number of neighbours, and is alive, it will stay alive
			# Rule 2: If the current cell has $rule2 number of neighbours, and is dead, it will become alive
			if ($neighbours == $rule1 || $neighbours == $rule2) {
				$a[$j][$i] = 1;
			}
			# Rule 3: If it has more neighbours than $rule2 or less neighbours than $rule1, it will die 
			else {
				$a[$j][$i] = 0;
			}
		}
	}
	
	# Return the new World
	return $a;
}

/**
 * Generate an initial, random chaos as a starting state for the world.
 */
function start() {
	# Global Variables
	global $world, $size, $rand_sample, $rand_alpha;
	
	# CInitialize the World 2D array with cells that are randomly dead or alive
	for ($x = 0; $x < $size; $x++) {
		for ($y = 0; $y < $size; $y++) {
			$world[$x][$y] = (rand(0,$rand_sample) > $rand_alpha)? 1 : 0;
		}
	}
}

/**
 * Retrieves the next iteration of the World's evolution and return's the HTML rendering
 */
function refresh() {
	# Global Variables
	global $world, $new_world;
	
	# Calculate a new World 2D Array
	$new_world = calc_new_world($_SESSION['world']);
	$world = $new_world;
	$_SESSION['world'] = $world;
	
	# Display an HTML Table representation of the new World
	print display_world($world);
}

# ==============================================================================
# ACTION HANDLER
# ==============================================================================

if (isset($_GET['action'])) {
	$action = $_GET['action'];
	if ($action == "refresh") {
		refresh();
	}
}
else {
	display();
}

# ==============================================================================
# THE END
# ==============================================================================

?>