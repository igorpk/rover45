<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
 * RoverControl
 *
 * Receives movement instructions from GNC as target coordinates
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

        // Separate the movement command out into an array
        $split = explode(" ", $master_command);

        // Set initial rover state from input
        $this->setBoundary( [$split[0], $split[1]] );
        $this->setPosition( ['x' => $split[2], 'y' => $split[3], 'd' => $this->convertDirectionToInt($split[4])] );

        // Set the set of instructions for the rover to follow
        $this->setMasterSequence( $split[5] );

        Log::info("Rover primed and ready. Start Point: ". print_r($this->position, 1));

        // Engage
        $this->computeSequence();
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

        Log::info("Rotate Before: ". implode(',', $this->position));

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

        Log::info("Rotate After: ". implode(',', $pos));

        return $pos;
    }

    /**
     * Advance the rover 1 step forward in the direction it is currently facing.
     *
     * @return array [x, y, d]
     */
    protected function move() {

        Log::info("Move Before: ". implode(',', $this->position));

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

        Log::info("Move After: ". implode(',', $pos));

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
