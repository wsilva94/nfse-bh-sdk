<?php namespace Nfse\Sanitizers;

use Carbon\Carbon;

class Date
{
    private $date;

    public function init($date)
    {
        $this->date = $date;
        return $this;
    }

    public function formatYmd($date)
    {
        return $this->date = Carbon::parse($date)->format('Y-m-d');
    }

    public function formatYmdTHis($date)
    {
        return $this->date = Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function formatYm($date)
    {
        return $this->date = Carbon::parse($date)->format('Y-m');
    }

    public function get()
    {
        return $this->date;
    }
}
