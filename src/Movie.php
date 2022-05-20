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
     * @param mixed $id
     * 
     * @return object return of curl response
     */
    public function getMovie($id)
    {
        $response = $this->fetch('post', 'movies/playing', [
            'location_id' => $id,
        ]);

        if (isset($response->message)) {
            @unlink('token.txt');
            return $this->getMovie($id);
        }
        return $response;
    }
}
