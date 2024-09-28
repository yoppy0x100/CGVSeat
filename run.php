<?php
date_default_timezone_set('asia/jakarta');

require 'vendor/autoload.php';

use CGVSeat\Config;
use CGVSeat\Movie;
use CGVSeat\Location;
use CGVSeat\Schedule;
use League\CLImate\Climate;

class Check extends Config
{

    private $movieName = '';

    private $climate,$location;
    
    public function __construct()
    {
        $this->climate = new Climate;
        $this->location = new Location;
    }

    public function dateRange($startDate = 'now')
    {
        $dateAr = [];
        $count = ($startDate != 'now') ? 5 : 6;
        for ($i = 0; $i < $count; $i++) {
            $dateAr[] = $menu = date('d-m-Y', strtotime('+' . $i . ' day', strtotime($startDate)));
            $this->climate->out('<background_blue> ' . $i . '. </background_blue> ' . $menu);
        }
        $this->climate->br();
        $input = $this->climate->input('Pilih Tanggal : ')->accept(range(0, count($dateAr)));
        $tgl = $input->strict()->prompt();

        $this->climate->clear();
        $this->climate->addArt('./src/Banner');
        $this->climate->draw('loading')->br();
        return date('Ymd', strtotime($dateAr[$tgl]));
    }

    public function showMovie()
    {
        $dataMovie = [];
        $movies = (new Movie)->nowPlaying();
        foreach ($movies->data as $key => $movie) {
            $dataMovie[] = $text = $movie->name;
            $this->climate->out('<background_magenta><green> ' . $key . '. </green></background_magenta> ' . $text);
        }
        $this->climate->br();
        $input = $this->climate->input('Pilih Film : ')->accept(range(0, count($dataMovie)));
        $prompt = $input->strict()->prompt();
        $this->climate->clear();

        $this->setMovieName($movies->data[$prompt]->name);
        $playing = $movies->data[$prompt];
        $date = ($playing->type != 2) ? $this->dateRange() : $this->dateRange($playing->opening_date);

        return (object) [
            'id' => $playing->id,
            'date' => $date
        ];
    }

    public function menu()
    {
        $this->showBanner();
        $movie = $this->showMovie();
        $schedule = (new Schedule)->show($movie->date, $movie->id, '029');

        $this->climate->clear();
        foreach ($schedule as $dataSeat) {
            $this->climate->backgroundGreenBlackBlink($this->getMovieName() . ' - ' . $dataSeat['location'])->br();
            unset($dataSeat['location']);

            foreach ($dataSeat as $seat) {
                $this->climate->backgroundCyan($seat['auditorium'] . ' - ' . $seat['format'] . ' | <light_yellow>' . $seat['price'] . '</light_yellow>');
                (self::DETAILS == true) ? $this->renderDetails($seat['seat']) : $this->renderSimple($seat['seat']);
                $this->climate->br();
            }
            $this->climate->br();
        }
    }

    /**
     * @param mixed $seat
     * 
     * @return null
     */
    private function renderDetails($seat)
    {
        $dataColor = [
            'regular' => 'magenta',
            'sweetbox' => 'light_blue',
            'cushion' => 'cyan',
            'velvet' => 'light_red',
        ];
        foreach ($seat as $key => $data) {
            $first = $this->climate->inline('<green><bold>Start</bold></green> : ' . $data['start'] . ' ')->inline('<red><bold>End</bold></red> : ' . $data['end']);
            foreach ($data['data'] as $key => $value) {
                $color = (array_key_exists($key, $dataColor)) ? $dataColor[$key] : 'blue';
                $first->inline(' <' . $color . '>' . $key . '</' . $color . '> : ' . count($data['data'][$key]));
            }
            $first->br();
        }
    }

    private function renderSimple($seat)
    {
        foreach($seat as $dataSchedule) {
            $first = $this->climate->inline('<green><bold>Start</bold></green> : ' . $dataSchedule->start_time . ' ')->inline('<red><bold>End</bold></red> : ' . $dataSchedule->end_time . ' ');
            $first->inline('<light_blue><bold>Remaining</bold></light_blue> : <blink>' . $dataSchedule->remaining_seat_count . '</blink> ')->inline('<magenta><bold>Total</bold></magenta> : ' . $dataSchedule->total_seat_count);
            $first->br();
        }
    }

    /**
     * @return null
     */
    private function showBanner()
    {
        if(strtolower(self::TYPE) == 'api') {
            $this->climate->error('Ubah tipenya gobloug di config !!!');
            throw new Exception("Missconfiguration on config.php, Please change the type", 1);
        }
        $this->climate->addArt('./src/Banner');
        $this->climate->draw('banner')->br();
    }

    /**
     * Get the value of movieName
     */ 
    public function getMovieName()
    {
        return $this->movieName;
    }

    /**
     * Set the value of movieName
     *
     * @return  self
     */ 
    public function setMovieName($movieName)
    {
        $this->movieName = $movieName;

        return $this;
    }
}

$seat = new Check;
$seat->menu();
