<?php

namespace App;

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
    protected $master_command;

    protected $master_sequence;
    protected $start_pos;
    protected $boundary;
    protected $move_sequence;

    protected $direction_reference = ['N', 'E', 'S', 'W'];

    public $current_pos;

    /**
     * Initialise the Rover.
     *
     * @param String $master_command
     */
    public function __construct( String $master_command ) {
        // BEEP, RECEIVED COMMAND
        $this->master_command = $master_command;
        $this->current_pos = [0,0,0];
        $this->initialise();
    }

    /**
     * Populates the rover's boundary, start position as well as the movement sequence.
     */
    public function initialise() {
        // Separate the movement sequence out into an array
        $split = explode(" ", $this->master_command);

        $this->boundary = [$split[0], $split[1]];
        $this->start_pos = $this->current_pos = [$split[2], $split[3], $this->convertDirection($split[4])];

        $this->master_sequence = $split[5];

        // ROVER READY
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
    protected function convertDirection( String $direction ) {
        $directions_flipped = array_flip( $this->direction_reference );
        return $directions_flipped[$direction];
    }

    /**
     * Returns a set of coordinates corresponding to the instructions provided
     */
    protected function computeSequence() {

        // Locate where the rover is
        $pos = $this->current_pos;

        // Loop over movement sequence.
        foreach($this->master_sequence as $instruction) {

        }
    }

    /**
     * Rotates the rover in a given direction (Left or Right)
     * This does not change the coordinates.
     *
     * @return array [x,y,d]
     */
    protected function rotate() {

        $pos = $this->current_position;
        $direction = $pos[2];

        switch($direction) {
            case 'L':
                // Left turn from N wraps the end of the array, i.e W
                if($pos[2] == 0) {
                    $pos = size($this->direction_reference);
                }
                else {
                    $pos[2]++;
                }
            break;
            case 'R' :
                if($pos[2] >= size($this->direction_reference)) {
                    $pos[2] = size($this->direction_reference);
                }
                else {
                    $pos[2]++;
                }
            break;
        }

        return $pos;
    }

    /**
     * Advance the rover 1 step forward in the direction it is currently facing.
     *
     * @return array [x, y, d]
     */
    protected function move() {

        $pos = $this->current_position;
        $direction = $pos[2];

        switch($direction) {
            case 'N' :
                $pos[1]++;
            break;
            case 'E' :
                $pos[0]++;
            break;
            case 'S' :
                $pos[1]--;
            break;
            case 'W' :
                $pos[0]++;
            break;
        }

        return $pos;
    }

}
