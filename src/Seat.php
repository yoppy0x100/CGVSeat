<?php

namespace CGVSeat;

class Seat extends Handler
{
    const URI = 'seat_info/';

    /**
     * @var array $seatt
     */
    private $seat = [];

    /**
     * @var array $gradeSeat
     */
    private $gradeSeat = [];

    /**
     * getting data seat from api and pasing data
     * 
     * @param string $id
     * @param callable|null $class
     * 
     * @return array
     */
    public function getSeats(string $scheduleID, callable $class = null)
    {
        // $response = $this->fetch('get', self::URI . $id)->seats->rows;
        $response = $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("movie-schedules/" . $scheduleID . "/seats"),
            "params" => "",
        ])->data->rows;
        foreach ($response as $key => $seat) {
            foreach ($seat->seats as $data) {
                if ($data->is_available == true) {
                    $grade = strtolower($data->grade);
                    $dataSeat = trim($data->row_name . $data->number);
                    $this->setSeat($grade, $dataSeat);
                    in_array($grade, $this->gradeSeat) ?: $this->setGradeSeat($grade);
                }
            }
        }

        if (is_callable($class)) {
            call_user_func($class, $this->getGradeSeat());
        }

        return $this->seat;
    }

    /**
     * @param string $data
     * 
     * @return Seat
     */
    protected function setGradeSeat(string $data)
    {
        $this->gradeSeat[] = $data;
        return $this;
    }

    /**
     * @return propety $gradeSeate
     */
    protected function getGradeSeat()
    {
        return $this->gradeSeat;
    }

    /**
     * @return array|property $seat
     */
    public function getSeat()
    {
        return $this->seat;
    }

    /**
     * @param string $key
     * @param string $data
     * 
     * @return Seat
     */
    private function setSeat(string $key, string $data)
    {
        $this->seat[$key][] = $data;
        return $this;
    }
}
