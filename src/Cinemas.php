<?php

namespace CGVSeat;

use CGVSeat\Handler;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Cinemas extends Handler
{
    /**
     * @param string $locationID
     * 
     * @return object
     */
    public function getLocation(string $locationID = '023')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "cinemas",
            "params" => [
                "location_id" => $locationID,
            ] 
        ]);
    }

    /**
     * @param mixed $Date (20241023)
     * @param string $locationID
     * 
     * @return object
     */
    public function show($Date, $locationID = '023')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("cinemas/" . $locationID . "/schedules"),
            "params" => [
                "date" => $Date
            ] 
        ]);
    }
}