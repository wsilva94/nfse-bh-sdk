<?php namespace NFse\Models;

use NFse\Models\Address;

class Provader
{
    /**
     *@var string razão social do prestador
     */
    public $name;

    /**
     *@var int cnpj do prestador
     */
    public $cnpj;

    /**
     *@var int inscrição do prestador
     */
    public $inscription;

    /**
     *@var int numero do telefone do prestador
     */
    public $phone;

    /**
     *@var int Email do prestador
     */
    public $email;

    /**
     *@var NFse\Models\Address;
     */
    public $address;

    public function __construct()
    {
        return $this->address = new Address();
    }

}
