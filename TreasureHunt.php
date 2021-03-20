<?php
	
class TreasureHunt {
	protected $map = [
		['#', '#', '#', '#', '#', '#', '#', '#'],
		['#', '.', '.', '.', '.', '.', '.', '#'],
		['#', '.', '#', '#', '#', '.', '.', '#'],
		['#', '.', '.', '.', '#', '.', '#', '#'],
		['#', '.', '#', '.', '.', '.', '.', '#'],
		['#', '#', '#', '#', '#', '#', '#', '#']
	];

	protected $wall = '#';
	protected $treasure = '$';
	protected $player = 'X';

	protected $directions = ['NORTH', 'EAST', 'SOUTH', 'WEST'];
	
	protected $start = [1, 4];
	protected $treasure_pos = [];

	// temporary properties for hunt
	protected $current_pos;
	protected $current_target;
	protected $found = false;
	protected $walked;
	protected $path;
	protected $cheat_mode = false;
	protected $last_char;

	// initialization
	public function __construct() {
		$this->setUp();
	}

	/**
	 * set up the possible treasure positions by available point (not wall or player starting point)
	 */
	protected function setUp() {
		foreach ($this->map as $i => $line) {
			foreach ($line as $j => $point) {
				if ($point == "." && ($j != 1 && $i != 4)) {
					$this->treasure_pos[] = [$j, $i];
				}

				if ($point == $this->player) {
					$this->start = [$j, $i];
				}
			}
		}
	}


	/**
	 * Draw the map
	 * @param  array $player_pos   [x, y] of map property of the class
	 * @return none
	 */
	protected function showMap($player_pos) {
		foreach ($this->map as $i => $line) {
			foreach ($line as $j => $point) {
				if ($player_pos[0] == $j && $player_pos[1] == $i) {
					echo !$this->cheat_mode && $this->found ? $this->treasure : $this->player;
				// } else if ($treasure_pos[0] == $j && $treasure_pos[1] == $i) {
				// 	echo $this->treasure;
				} else {
					echo $point;
				}
			}
			echo "\n";
		}
	}

	protected function isWall($sign) {
		return $sign == $this->wall;
	}

	/**
	 * start the hunt
	 * @return  none
	 */
	public function doHunt() {
		$c = $this->treasure_pos[array_rand($this->treasure_pos)];
		$this->current_pos = $this->start;
		
		// foreach ($this->treasure_pos as $c) {
		while (true) {
			$this->current_target = $c;
			// $this->found = false;
			// $this->current_pos = $this->start;
			$this->walked = [];
			$this->path = [];

			system("clear");

			echo "The treasure must be somewhere around.\n\n";

			$this->showMap($this->current_pos, $c);

			if (!$this->cheat_mode && $this->found) {
				echo "\nYou've found the treasure!\n\n";

				echo "Go another round? (y/n)\n\n";

				$handle = fopen('php://stdin', 'r');

				$char = ord(fgetc($stdin));
				
				if ($char != 121) {
				    echo "Thank you for playing! Have a nice day :)\n\n";
				    exit;
				}

				$this->found = false;
				$c = $this->treasure_pos[array_rand($this->treasure_pos)];
				$this->current_pos = $this->start;
				continue;
			}

			echo "\nPress h to toggle help ;)\n\n";

			if ($this->cheat_mode) {
				echo $this->cheat_message;
			}

			system('stty cbreak -echo');
	        $stdin = fopen('php://stdin', 'r');

	        while (1) {
	            $char = ord(fgetc($stdin));

	            if ($char == 65) {
	            	$this->move('NORTH');
	            	if ($this->found) {
	            		$this->cheat_mode = false;
	            	}
	            	break;
	            } else if ($char == 66) {
	            	$this->move('SOUTH');
	            	if ($this->found) {
	            		$this->cheat_mode = false;
	            	}
	            	break;
	            } else if ($char == 67) {
	            	$this->move('EAST');
	            	if ($this->found) {
	            		$this->cheat_mode = false;
	            	}
	            	break;
	            } else if ($char == 68) {
	            	$this->move('WEST');
	            	if ($this->found) {
	            		$this->cheat_mode = false;
	            	}
	            	break;
	            } else if ($char == 104) {
	            	$this->last_pos = $this->current_pos;
	            	$this->last_walked = $this->walked;
	            	$this->last_path = $this->path;

	            	$this->walked = [];
	            	$this->path = [];

	            	if (!$this->cheat_mode) {
	            		$this->cheat_mode = true;

	            		while (!$this->found) {
	            			foreach ($this->directions as $dir) {
	            				$success_moving = $this->move($dir);
	            				
	            				if ($success_moving) {
	            					break;
	            				}
	            			}
	            		}

	            		$this->cheat_message = "The treasure is at ".implode(',', $c)."\n\nPath to the treasure: \n".$this->showPath()."\n";

	            		$this->current_pos = $this->last_pos;
	            		$this->walked = $this->last_walked;
	            		$this->path = $this->last_path;
	            		$this->found = false;

	            	} else {
	            		$this->cheat_mode = false;
	            	}
	            	// let's do the hunt
	            	break;
	            }
	        }
		}
	}

	/**
	 * get hint of path that should be walked
	 * @return string the hint of path
	 */
	protected function showPath() {
		$paths = [];

		$current_dir = null;
		$count = 0;
		$order = 0;

		foreach ($this->path as $i => $path) {
			if ($current_dir == null) {
				$current_dir = $path;
				$count = 1;
				continue;
			}

			if ($current_dir == $path) {
				$count++;
			} 

			if ($current_dir != $path) {
				$order++;
				$paths[] = $order . ". " . $current_dir . " " . $count . " step(s)";
				$current_dir = $path;
				$count = 1;
			}

			if ($i == count($this->path) - 1) {
				$order++;
				$paths[] = $order . ". " . $current_dir . " " . $count . " step(s)";
			}
		}

		return implode(", then \n", $paths);
	}

	/**
	 * get new coordinate by current position and direction
	 * @param array $position  [x,y] of current position
	 * @param array $direction new position
	 */
	protected function setNewCoordinate($position, $direction) {
		$x = $position[0];
		$y = $position[1];

		switch ($direction) {
			case 'NORTH':
				$x1 = $x;
				$y1 = $y - 1;
				break;
			case 'EAST':
				$x1 = $x + 1;
				$y1 = $y;
				break;
			case 'SOUTH':
				$x1 = $x;
				$y1 = $y + 1;
				break;
			case 'WEST':
				$x1 = $x - 1;
				$y1 = $y;
				break;
			default:
				echo "\nHey, there is no such direction!\n";
				exit();
		}

		return [$x1, $y1];
	}

	/**
	 * Is the coordinate visited
	 * @param  array  $pos [x,y] of the position that will be checked
	 * @return boolean      true if visited, otherwise false
	 */
	protected function isVisited($pos) {
		return in_array($pos, $this->walked);
	}

	/**
	 * check if current position is on dead end by checking around
	 * @return boolean true if on a dead and, otherwise false
	 */
	protected function isOnDeadEnd() {
		foreach ($this->directions as $dir) {
			$new_pos = $this->setNewCoordinate($this->current_pos, $dir);
			$x1 = $new_pos[0];
			$y1 = $new_pos[1];

			// echo "Checking ".strtolower($dir).": ";
			if (
				!$this->isWall($this->map[$y1][$x1]) // is there a wall in the $direction?
				&& !$this->isVisited($new_pos) // have we been there?
			) {
				// echo "ok!\n";
				return false;
			}
			// echo "NOT OK!\n";
		}

		// echo "No! It's dead end here at ".implode(',', $this->current_pos)."\n";
		return true;
	}

	/**
	 * move the player position by direction
	 * @param  String $direction NORTH, WEST, SOUTH or EAST
	 * @return boolean            true if move made, otherwise false
	 */
	protected function move($direction) {
		$x = $this->current_pos[0];
		$y = $this->current_pos[1];

		$new_pos = $this->setNewCoordinate($this->current_pos, $direction);
		$x1 = $new_pos[0];
		$y1 = $new_pos[1];

		// echo "We're going ".strtolower($direction)."\n";
		// echo "The north is at ".implode(',', $new_pos)."\n";
		
		// checking before going to $direction
		if (
			($x1 >= 0 || $y1 >= 0) // we stay on the map, right?
			&& !$this->isWall($this->map[$y1][$x1]) // is there a wall in the $direction?
			&& !$this->isVisited($new_pos) // have we been there?
		) {
			// moving $direction
			$this->current_pos = [$x1, $y1];
			
			// let's leave a trace
			$this->walked[] = $this->current_pos;
			$this->path[] = $direction;

			// echo "We're going ".strtolower($direction)." to ".implode(',', $this->current_pos)."\n";

			// is it the treasure?
			if ($this->current_pos == $this->current_target) {
				// echo "\nYeay, we found the treasure at ".implode(',', $this->current_pos)."\n";
				// we're not going anywhere, we found the treasure!
				$this->found = true;
				return true;
			} 

			// are we on dead end?
			if ($this->isOnDeadEnd()) {
				$this->current_pos = $this->walked[count($this->walked) - 2];
				
				// remove path to the dead end
				unset($this->path[count($this->path) - 1]);
				$this->path = array_values($this->path);
				// echo "We're going back to ".implode(',', $this->current_pos) . "\n";
			}
		} else {
			// echo "We can't go ".strtolower($direction)." to ".implode(',', $new_pos)."\n";
			return false;
		}

		return true;
	}
}

// initialize treasure hunt and run
$hunt = new TreasureHunt();
$hunt->doHunt();

?>