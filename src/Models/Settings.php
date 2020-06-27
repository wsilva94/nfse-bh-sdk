<?php

namespace Nfse\Models;

use Nfse\Models\Issuer;
use Nfse\Models\Certificate;

class Settings extends DefaultModel
{
    /**
     *@var string ambiente H - homologação | P - produção
     */
    public $environment;

    /**
     *@var string pasta onde guarda os XML
     */
    public $storage;

    /**
     * Issuer.
     * @var Nfse\Models\Issuer
     */
    public $issuer;

    /**
     * Certificate.
     * @var Nfse\Models\Certificate
     */
    public $certificate;

    public function __construct()
    {
        $this->issuer = new Issuer();
        $this->certificate = new Certificate();
    }
}
