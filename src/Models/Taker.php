<?php namespace NFse\Models;

use NFse\Models\Address;

class Taker
{
    /**
     *@var int tipo de pessoa
     *
     * 1 - CNPJ | 2 - CPF
     */
    public $type;

    /**
     *@var string razão social tomador
     *
     */
    public $name;

    /**
     *@var string documento tomador
     */
    public $document;

    /**
     *@var int inscrição municipal do tomador
     */
    public $municipalRegistration;

    /**
     *@var int telefone do tomador
     */
    public $phone;

    /**
     *@var int email do tomador
     */
    public $email;

    /**
     *@var NFse\Models\Address
     */
    public $address;

    public function __construct()
    {
        $this->address = new Address();
    }
}
