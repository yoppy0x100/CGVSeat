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
    public function nowPlaying($LocationID = '029')
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "movies/home",
            "params" => [
                "location_id" => $LocationID,
            ]
        ]);
    }

    /**
     * @param string $LocationID
     * 
     * @return object
     */
    public function upcoming($LocationID = '029') {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => "movies/upcoming",
            "params" => [
                "location_id" => $LocationID,
            ]
        ]);
    }

    /**
     * @param mixed $movieID
     * 
     * @return object
     */
    public function info($movieID)
    {
        return $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("movies/" . $movieID),
            "params" => ''
        ]);
    }
}
