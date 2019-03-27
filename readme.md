# Next45 Technical Challenge

Mars Rover Guidance 

## Requirements

PHP 7.0 or newer

## Installation

1) Clone the git repo: https://github.com/igorpk/rover45.git
2) Run: `composer update`
3) Run a local PHP webserver from inside the project folder: `php -S localhost:8080 -t public/`
4) Open your browser and point to http://localhost:8080

## Use

Enter the command string as described in the brief into the text box and click 'Submit'.
The app will print out the resulting position of the rover.

# Files of interest:
`app/RoverControl.php` Class file containing all logic
`app/Http/Controllers/RoverController.php` app/Http/Controllers/RoverController.php HTTP Controller

# Design / Technical Considerations
I chose to use Lumen due to ease and familiarity. I fully recognise that it's overkill.

The rover has a position and direction, represented by $position **[x,y,d]**.
For movement, **x** and **y** are incremented/decremented based on the value of **d**.
For rotation, **d** is set based on the next logical array element in $direction_reference.

When the command is received to move, the script performs each instruction in order, while maintaining a
history of instructions executed in **$move_sequence**

The instruction history could allow for greater extension applications down the line (graphs, AI etc).

Internally, the cardinal points are stored in an array (**$direction_reference**) to allow for numerical manipulation of direction.

Directions are only displayed as strings (N,E,S,W) when returned to the user.

# What I'd do for Version 2
	Separation of route coordinate calculations from the rover control,
	enabling different interfaces to be used for differing input formats.
	Refactor **move()** and **rotate()** into a single function.
	Agnostic input
	Custom Logging
    Expose instruction history via JSON API
    Add hook system to perform other actions when at a certain coordinate
    Graphing

    HOOK UP AN ARDUINO AND SOME LED'S. Red for Right, Blue for Left, Green for Move.
    SIMPLE. NOTHING TO IT. *ANYTHING* TO MAKE THIS EVEN SLIGHTLY REAL.
 

# Correctness
	The rover code checks the basic validity of the command it receives vis the **checkCommand()** function.
	Furthermore, a check is done against the boundary area with every step.

	~~Unit tests are provided to ascertain:~~
		~~Correct rover initialisation~~
		~~Correct behaviour upon receiving any instructions (Move, Turn Left, Turn Right)~~
