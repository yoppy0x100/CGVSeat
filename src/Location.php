<?php

namespace CGVSeat;

use League\CLImate\CLImate;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Location extends Handler
{

    /**
     * show of location  of get location if exists
     * 
     * @return string id
     */
    public function getId()
    {
        if (file_exists('location.txt')) {
            return file_get_contents('location.txt');
        }

        $climate = new CLImate;
        $location = $this->getLocation()->cities;
        $i = 0;
        foreach ($location as $data) {
            $climate->inline('<yellow>' . $i . '.</yellow>')->tab()->inline($data->name)->br();
            $i++;
        }
        $climate->br();
        $input = $climate->input('Pilih Lokasi : ')->accept(range(0, count($location)));
        $num = $input->strict()->prompt();
        $climate->clear();

        $id = $location[$num]->id;
        @file_put_contents('location.txt', $id);
        return $id;
    }

    /**
     * get data location
     * 
     * @return object Curl response
     */
    public function getLocation()
    {
        $response = $this->fetch('post', 'cities-cgv');

        if (isset($response->message)) {
            @unlink('token.txt');
            return $this->getLocation();
        }
        return $response;
    }
}
