# Treasure Hunt (CLI Game)

This is a simple game of command line interface where a player is in a labyrinth. The player have to find a treasure somewhere in the map.  Here is the map.
```
########
#......#
#.###..#
#...#.##
#X#....#
########
```

Where 

 - `#` represents obstacle
 - `.` represents clear path
 - `X` represents player starting position

The player should find the hidden treasure by moving using these buttons

 - **Up arrow** for moving **North**
 - **Right arrow** for moving **East**
 - **Down Arrow** for moving **South**, and
 - **Left Arrow** for moving **West**

If the player move to a path where the treasure is, the player sign (`X`) will become `$` and it signs that the player has found the treasure. After that, the player will be given choice to go another round by pressing `y` or quit the game by pressing other keys.

And of course, I provide a help for players in form of a hint where they should move from the current position for reaching the treasure. The player just have to press `h` button to toggle the help (it's secret, don't tell the others). The hints should be like this.

```
The treasure is at 6,2

Path to the treasure:
1. NORTH 3 step(s), then
2. EAST 5 step(s), then
3. SOUTH 1 step(s)
```

Enjoy the game by cloning this repo and just run 
```
$ php TreasureHunt.php
```

Good luck ;)