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

        // Set the default values from constructor argument
        $this->initialise();

        $this->computeSequence();
    }



    /**
     * Populates the rover's boundary, start position as well as the movement sequence.
     */
    public function initialise() {
        // Separate the movement sequence out into an array
        $split = explode(" ", $this->master_command);

        $this->boundary = [$split[0], $split[1]];
        $this->start_pos = $this->current_pos = [$split[2], $split[3], $this->convertDirectionToInt($split[4])];

        $this->master_sequence = $split[5];
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
     * Returns a set of coordinates corresponding to the instructions provided
     */
    protected function computeSequence() {

        // Locate where the rover is
        $pos = $this->current_pos;

        // Loop over movement sequence.
        foreach(str_split($this->master_sequence) as $instruction) {
            if($instruction == 'M') {
                $this->current_pos = $this->move();
            }
            else {
                $this->current_pos = $this->rotate($instruction);
            }
        }
    }

    /**
     * Rotates the rover in a given direction (Left or Right)
     * This does not change the coordinates.
     *
     * @return array [x,y,d]
     */
    protected function rotate( String $instruction ) {

        echo "Rotate Before: ". implode(',', $this->current_pos) ."<br />";

        $pos = $this->current_pos;

        switch($instruction) {
            case 'L':
                // Left turn from N wraps the end of the array, i.e W
                if($pos[2] == 0) {
                    $pos[2] = count($this->direction_reference) - 1 ;
                    echo $pos[2];
                }
                else {
                    $pos[2]--;
                }
            break;
            case 'R' :
                // Right turn from W wraps to the beginning of the array, i.e N
                if($pos[2] >= count($this->direction_reference) - 1 ) {
                    $pos[2] = 0;
                }
                else {
                    $pos[2]++;
                }
            break;
        }

        echo "Rotate After: ". implode(',', $pos) ."<br />";

        return $pos;
    }

    /**
     * Advance the rover 1 step forward in the direction it is currently facing.
     *
     * @return array [x, y, d]
     */
    protected function move() {
        echo "Move Before: ". implode(',', $this->current_pos) ."<br />";
        $pos = $this->current_pos;
        $direction = $pos[2];
        echo($this->direction_reference[$direction]);
        switch($this->direction_reference[$direction]) {
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
                $pos[0]--;
            break;
        }

        echo "Move After: ". implode(',', $pos) ."<br />";;

        return $pos;
    }

}
