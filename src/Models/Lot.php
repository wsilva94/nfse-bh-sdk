<?php namespace NFse\Models;

use NFse\Models\Rps;

class Lot
{
    /**
     *@var string numero do lote RPS
     */
    public $rpsLot;

    /**
     *@var NFse\Models\Rps
     */
    public $rps;

    public function __construct()
    {
        $this->rps = new Rps;
    }
}
