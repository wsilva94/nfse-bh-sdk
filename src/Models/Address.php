<?php namespace NFse\Models;

class Address
{
    /**
     *@var string endereço tomador
     */
    public $address;

    /**
     *@var string numero do endereço do tomador
     */
    public $number;

    /**
     *@var string complemento do endereço do tomador
     */
    public $complement;

    /**
     *@var string bairro do endereço do tomador
     */
    public $neighborhood;

    /**
     *@var int CEP do endereço do tomador
     */
    public $zipCode;

    /**
     *@var string estado do endereço do tomador
     */
    public $state;

    /**
     *@var string nome da cidade
     */
    public $city;

    /**
     *@var int codigo do municipio do tomador
     */
    public $municipalityCode;
}
