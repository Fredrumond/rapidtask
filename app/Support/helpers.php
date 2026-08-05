<?php

use App\Support\CurrentTeam;

if (! function_exists('current_time_id')) {
    function current_time_id(): ?int
    {
        return CurrentTeam::id();
    }
}

if (! function_exists('current_conta_id')) {
    function current_conta_id(): ?int
    {
        return CurrentTeam::contaId();
    }
}
