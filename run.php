<?php
date_default_timezone_set('asia/jakarta');

require 'vendor/autoload.php';

use CGVSeat\Location;
use CGVSeat\Movie;
use CGVSeat\Schedule;
use League\CLImate\Climate;

class Check
{

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
        $schedule = (new Schedule)->getAvailableSeat($movie->date, $movie->id, '000');

        foreach ($schedule as $dataSeat) {
            $this->climate->backgroundGreenBlackBlink($dataSeat['location'])->br();
            unset($dataSeat['location']);

            foreach ($dataSeat as $seat) {
                $this->climate->backgroundCyan($seat['auditorium'] . ' - ' . $seat['format'] . ' | <light_yellow>' . $seat['price'] . '</light_yellow>');
                $this->showDataSeat($seat['seat']);
                $this->climate->br();
            }
            $this->climate->br();
        }
    }

    private function showDataSeat($seat)
    {
        $dataColor = [
            'regular' => 'magenta',
            'sweetbox' => 'light_blue',
            'cushion' => 'cyan'
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

    private function showBanner()
    {
        $this->climate->addArt('./src/Banner');
        $this->climate->draw('banner')->br();
    }
}

$seat = new Check;
$seat->menu();
