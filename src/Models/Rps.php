<?php namespace NFse\Models;

use NFse\Models\Service;

class Rps
{
    /**
     *@var int número do RPS
     */
    public $number;

    /**
     *@var string serie RPS
     */
    public $serie;

    /**
     *@var int tipo RPS
     */
    public $type;

    /**
     *@var data data da emissão RPS
     */
    public $date;

    /**
     *@var int natureza da operação
     *
     * 1 – Tributação no município |  2 - Tributação fora do município | 3 - Isenção |  4 - Imune
     * 5 – Exigibilidade suspensa por decisão judicial | 6- Exigibilidade suspensa por procedimento administrativo
     */
    public $nature;

    /**
     *@var int tipo de regime
     *
     * 1 – Microempresa municipal | 2 - Estimativa | 3 – Sociedade de profissionais | 4 – Cooperativa
     * 5 – MEI – Simples Nacional | 6 – ME EPP – Simples Nacional
     */
    public $regime;

    /**
     *@var int opitante pelo simples nacional
     *
     * 1 - Sim | 2 - Não
     */
    public $simple;

    /**
     *@var int incentivador cultural
     *
     * 1 - Sim | 2 - Não
     */
    public $culturalPromoter;

    /**
     *@var int status RPS
     *
     * 1 - normal | 2 - cancelado
     */
    public $status;

    /**
     *@var NFse\Models\Service
     */
    public $service;

    /**
     *@var NFse\Models\Taker
     */
    public $taker;

    public function __construct()
    {
        $this->service = new Service();
        $this->taker = new Taker();
    }
}
