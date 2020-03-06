<?php

namespace NFse\Models;

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
     * Certificate.
     *
     * @var NFse\Models\Certificate
     */
    public $certificate;

    public function __construct()
    {
        $this->issuer = new Issuer();
        $this->certificate = new Certificate();
    }
}
