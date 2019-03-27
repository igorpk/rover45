<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Mockery\Exception;

/**
 * RoverControl
 *
 * Receives movement instructions.
 *
 * Responsible for decoding an incoming movement instruction, and
 * issuing the instruction to the rover.
 *
 */
class RoverControl
{
    // Raw movement sequence
    protected $master_sequence;

    // Cartesian position and direction
    protected $position = ['x' => 0, 'y' => 0, 'd' => 0];

    // Cartesian points representing area boundaries
    protected $boundary = ['x' => 0, 'y' => 0];

    // Integer-indexed directions for orientation
    protected $direction_reference = ['N', 'E', 'S', 'W'];

    // Stores all moves performed in sequence.
    protected $move_sequence;

    /**
     * Initialise the Rover.
     * Populates the rover's boundary, start position as well as the movement sequence.
     *
     * @param String $master_command
     */
    public function __construct( String $master_command ) {

        $this->master_command = $master_command;

        $this->initRover();

        if($this->checkCommand()) {
            // Engage
            $this->computeSequence();
        }
        else {
            throw new Exception('ABORT. Master Command malformed.');
        }
    }

    /**
     * Sets the rover to start conditions as described in the input string
     *
     * @return void
     */
    protected function initRover() {
        // Separate the movement command out into an array
        $split = explode(" ", $this->master_command);

        // Set initial rover state from input
        $this->setBoundary( [$split[0], $split[1]] );
        $this->setPosition( ['x' => $split[2], 'y' => $split[3], 'd' => $this->convertDirectionToInt($split[4])] );

        // Set the set of instructions for the rover to follow
        $this->setMasterSequence( $split[5] );

        Log::info("Rover primed and ready. Start Point: ". implode(',', $this->position));
   }


    /**
     * Returns a set of coordinates corresponding to the instructions provided
     *
     * Iterates over the master move sequence and sets $this->position accordingly
     * for each move.
     *
     * @return void
     */
    protected function computeSequence() {

        // Loop over movement sequence.
        foreach(str_split($this->master_sequence) as $instruction) {
            // Move or Rotate, depending on instruction
            $this->setPosition(($instruction == 'M') ? $this->move() : $this->rotate($instruction));
            $this->move_sequence []= $this->position;
        }
    }

    /**
     * Directions are internally represented as integers, but are
     * provided as strings (N, E, S, W)
     *
     * Convert these to integers as per corresponding array key is $this->direction
     *
     * @param String $direction
     * @return int
     */
    protected function convertDirectionToInt( String $direction ) {
        $directions_flipped = array_flip( $this->direction_reference );

        return $directions_flipped[$direction];
    }

    /**
     * Rotates the rover in a given direction (Left or Right)
     * This does not change the coordinates.
     *
     * @return array [x,y,d]
     */
    protected function rotate( String $instruction ) {

        $pos = $this->position;

        switch($instruction) {
            case 'L':
                // Left turn from N wraps the end of the array, i.e W
                if($pos['d'] == 0) {
                    $pos['d'] = count($this->direction_reference) - 1;
                }
                else {
                    $pos['d']--;
                }
            break;
            case 'R' :
                // Right turn from W wraps to the beginning of the array, i.e N
                if($pos['d'] >= count($this->direction_reference) - 1) {
                    $pos['d'] = 0;
                }
                else {
                    $pos['d']++;
                }
            break;
        }

        Log::info("Got instruction: {$instruction}. Rotated to: ". implode(',', $pos));

        return $pos;
    }

    /**
     * Advance the rover 1 step forward in the direction it is currently facing.
     *
     * @return array [x, y, d]
     */
    protected function move() {

        $pos = $this->position;
        switch($this->direction_reference[$pos['d']]) {
            case 'N' :
                $pos['y']++;
            break;
            case 'E' :
                $pos['x']++;
            break;
            case 'S' :
                $pos['y']--;
            break;
            case 'W' :
                $pos['x']--;
            break;
        }

        Log::info("Got instruction: M. Moved to: ". implode(',', $pos));

        return $pos;
    }

    /**
     * Return the current position in the same format as provided
     * in the initial command (e.g '1 2 S')
     *
     * @return String
     */
    public function getFormattedPosition() {
        return $this->position['x'] .' '. $this->position['y'] .' '. $this->direction_reference[$this->position['d']];
    }

    /**
     * Compares a set of coordinates to the set boundary.
     * If out of bounds, issues an abort error and re-sets the rover.
     */
    protected function checkBoundary() {
        if($this->boundary['x'] > $this->position['x'] ||
            $this->boundary['y'] > $this->position['y']) {
                Log::critical('ABORT. Rover out of bounds, resetting to start.');
                $this->initRover();
            }
    }

    /**
     * Validates that the input command:
     *  Has 6 elements in total,
     *  Has 4 integer coordinates for boundary and start position
     *  Has a string for start direction
     *  Has a movement sequence that contains only (M|R|L)
     */
    protected function checkCommand() {
        $split_command = explode(' ', $this->master_command);

        $split_command = array_map('trim', $split_command);

        if(! count($split_command) == 6) return false;

        // Check coordinates
        $arr = array_filter(array_slice($split_command,0,4), 'is_numeric');
        if(! count($arr) == 4) return false;

        if(! in_array($split_command[4], $this->direction_reference)) return false;

        if(! preg_match('/^[M|R|L]+$/', $split_command[5])) return false;

        return true;
    }
    /**
     * Mutator for $this->position
     *
     * @param array $position
     * @return void
     */
    public function setPosition(Array $position) {
        $this->position = $position;
    }

    /**
     * Mutator for $this->boundary
     *
     * @param array $boundary
     * @return void
     */
    public function setBoundary(Array $boundary) {
        $this->boundary = $boundary;
    }

    /**
     * Mutator for $this->master_sequence
     *
     * @param array $master_sequence
     * @return void
     */
    public function setMasterSequence(String $master_sequence) {
        $this->master_sequence = $master_sequence;
    }
}
