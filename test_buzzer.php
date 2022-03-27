<?php
    function writebuzzerstate($pin, $duration){
        $pin = $pin;
        $state = 0;
        $duration = $duration;
        while (true){
            if ($state == 1){
                $state = 0;
            }
            else{
                $state = 1;
            }
            exec("gpio write $pin $state");
            sleep($duration);
        }
    }
    writebuzzerstate(0,0.25);
?>