<?php namespace NFse\Models;

use NFse\Models\Provader;
use NFse\Models\Service;
use NFse\Models\Taker;

class NFse
{
    /**
     *@var string ano
     */
    public $year;

    /**
     *@var string numero da NFse
     */
    public $number;

    /**
     *@var string data de emissão da NFse
     */
    public $dateEmission;

    /**
     *@var string hora de emissão da NFse
     */
    public $timeEmission;

    /**
     *@var string data competência representa a data em que o serviço foi prestado
     */
    public $competence;

    /**
     *@var string codigo de verificação da NFse
     */
    public $verificationCode;

    /**
     *@var int numero da NFse de substitução
     *
     */
    public $nfseNumberReplaced;

    /**
     *@var NFse\Models\Provader;
     *
     */
    public $provider;

    /**
     *@var NFse\Models\Taker;
     *
     */
    public $taker;

    /**
     *@var NFse\Models\Service;
     *
     */
    public $service;

    public function __construct()
    {
        $this->provider = new Provader();
        $this->taker = new Taker();
        $this->service = new Service();
    }

}
