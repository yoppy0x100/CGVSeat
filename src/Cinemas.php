<?php

namespace CGVSeat;

use CGVSeat\Handler;

class Cinemas extends Handler
{
    public function nowPlaying(string $LocationID = '023')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "cinemas",
            "params" => [
                "location_id" => $LocationID,
            ] 
        ]);
    }

    public function Show($Date, $LocationID = '023')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("cinemas/" . $LocationID . "/schedules"),
            "params" => [
                "date" => $Date
            ] 
        ]);
    }
}