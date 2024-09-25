<?php

namespace CGVSeat;

/**
 * 
 * @package CGVSeat
 * @author Yoppy Dimas <anggaraputra456@gmail.com|yoppy@sgbteam.id> 
 * */
class Schedule extends Handler
{

    /**
     * data of schedule
     * 
     * @var array
     */
    protected $dataSchedule = [];

    /**
     * @var mixed
     */
    private $gradeSeat;


    /**
     * this is the main function to get data of seat
     * 
     * @param string $date Ymd format
     * @param string|Movie $movieID
     * @param string|Location\{getId} $location_id
     * 
     * @return array 
     */
    public function getAvailableSeat($date, $movieID, $LocationID = '029')
    {
        $dataSeat = [];
        $response = $this->fetch('post', 'mw/exceute', [
            "method" => "get",
            "path" => trim("movies/" . $movieID . "/schedules"),
            "params" => [
                "date" => $date,
                "location_id" => $LocationID
            ] 
        ]);

        // Mode switch
        if(strtolower(SELF::TYPE) == 'api') {
            return $response->data;
        }
        foreach ($response->data->cinemas as $key => $data) {
            $dataSchedule = [];
            foreach ($data->schedule_types as $schedules) {
                $dataSchedule[] = [
                    'auditorium' => $schedules->auditorium_name,
                    'format' => $schedules->movie_format,
                    'price' => $this->getPrice($schedules),
                    'seat' => $this->getSchedule($schedules)
                ];
            }
            $dataSeat[] = array_merge([
                'location' => $data->name . ' - ' . $data->location_name,
            ], $dataSchedule);
        }
        return $dataSeat;
    }

    /**
     * getting data of schedule object
     * 
     * @param object $data
     * 
     * @return array retuning available seat
     */
    protected function getSchedule(object $data)
    {
        $dataSeat = [];
        foreach ($data->schedules as $key => $value) {
            $seat = $this->Seat()->getSeats($value->id, function ($resp) {
                $this->setGradeSeat($resp);
            });
            empty($seat) ?: $dataSeat[] = [
                'start' => $value->start_time,
                'end' => $value->end_time,
                'data' => $seat
            ];
        }
        return $dataSeat;
    }

    /**
     * parsing price
     * 
     * @param object $data
     * 
     * @return string
     */
    private function getPrice(object $data)
    {
        return (isset($data->price_range) ? $data->price_range : $data->price);
    }

    /**
     * get grade seat
     * 
     * @return array $gradeSeat
     */
    protected function getGradeSeat()
    {
        return $this->gradeSeat;
    }

    /**
     * @param mixed $data
     * 
     * @return Schedule
     */
    private function setGradeSeat($data)
    {
        $this->gradeSeat = $data;
        return $this;
    }
}
