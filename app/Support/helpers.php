<?php

use App\Support\CurrentTeam;

if (! function_exists('current_time_id')) {
    function current_time_id(): ?int
    {
        return CurrentTeam::id();
    }
}
