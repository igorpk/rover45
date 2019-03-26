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

    public $current_pos;
    
    protected $direction_reference = array['N', 'E', 'S', 'W'];

    /**
     * Initialise the Rover
     */
    public function __construct( String $master_command ) {
        $this->master_command = $master_command;
        $this->current_pos = [0,0,0];
        $this->initialise();
        // BEEP, RECEIVED COMMAND
    }

    public function initialise() {
        $split = explode(" ", $this->master_command);
        
        $this->boundary = [$split[0], $split[1]];
        $this->start_pos = $this->current_pos = [$split[2], $split[3], $this->convertDirection($split[4])];
        $this->master_sequence = $split[5]; 
    }

    /**
     * Directions are internally represented as integers, but are
     * provided as strings (N, E, S, W)
     * 
     * Convert these to integers as per corresponding array key is $this->direction 
     */
    protected function convertDirection(String $direction) {
        $directions_flipped = array_flip($this->direction_reference);
        return $directions_flipped[$direction];
    }

    /**
     * Returns a set of coordinates corresponding to the instructions provided
     */
    protected function computeSequence() {

        $pos = $this->current_pos;

        foreach($this->master_sequence as $instruction) {
            if($instruction != 'M') {
                switch($instruction) {
                    case 'L':
                        if($pos[2] == 0) {
                            return size($this->direction_reference)
                        }
                        else {
                            return $pos[2]++;
                        }
                    break;
                    case 'R' :
                        if($pos[2] >= size($this->direction_reference) {
                            return size()
                        }
                        else {
                            return $pos[2]++;
                        }
                    break;
                    default:
                      // Do nothing
                    break;
                }
            }
            else {
                //engage
            }
        }
    } 


    protected function rotate() {

    }

    protected function move() {

    }
}
