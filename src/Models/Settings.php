<?php namespace NFse\Models;

use NFse\Models\Certified;
use NFse\Models\Issuer;

class Settings extends DefaultModel
{
    /**
     *@var string ambiente H - homologação | P - produção
     */
    public $environment;

    /**
     * Issuer.
     *
     * @var NFse\Models\Issuer
     */
    public $issuer;

    /**
     * Certified.
     *
     * @var NFse\Models\Certified
     */
    public $certified;

    public function __construct()
    {
        $this->issuer = new Issuer();
        $this->certified = new Certified();
    }

}
