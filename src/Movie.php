<?php


namespace CGVSeat;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Movie extends Handler
{

    /**
     * getting movie data from api
     * 
     * @param mixed $LocationID
     * 
     * @return object return of curl response
     */
    public function nowPlaying($LocationID = '000')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "movies/home",
            "params" => [
                "location_id" => $LocationID,
            ]
        ]);
    }

    public function upcoming($LocationID = '029') {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "movies/upcoming",
            "params" => [
                "location_id" => $LocationID,
            ]
        ]);
    }

    public function info($movieID)
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("movies/" . $movieID),
            "params" => ''
        ]);
    }
}
