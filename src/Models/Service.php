<?php namespace NFse\Models;

class Service
{
    /**
     *@var float código de item da lista de serviço
     */
    public $itemList;

    /**
     *@var string descrição sobre o código lista de serviço
     */
    public $itemDescription;

    /**
     *@var int código de tributação do municipio
     */
    public $municipalityTaxationCode;

    /**
     *@var string descrição do codigo de tributação
     */
    public $taxCodeDescription;

    /**
     *@var int código do municipio de prestação do serviço
     */
    public $municipalCode;

    /**
     *@var string nome do municipio de prestação do serviço
     */
    public $municipalName;

    /**
     *@var string descrição do serviço prestado
     */
    public $description;

    /**
     *@var int codigo da natureza da operação
     */
    public $nature;

    /**
     *@var bool optante pelo simples nacional
     */
    public $simpleNational;

    /**
     *@var int codigo de Regime tributário especial
     */
    public $specialTaxRegime;

    /**
     *@var float valor do serviço
     */
    public $serviceValue;

    /**
     *@var float valor de outras restituções
     */
    public $otherWithholdings;

    /**
     *@var int informação de ISS retido
     *
     * 1 - Sim | 2 - Não
     */
    public $issWithheld;

    /**
     *@var float valor de iss retido
     *
     */
    public $issValueWithheld;

    /**
     *@var float valor do iss
     *
     */
    public $issValue;

    /**
     *@var float valor da aliquota
     *
     * Aliquota em valor percentual.Formato: 0.0000 Ex: 1% = 0.01 | 25,5% = 0.255 | 100% = 1.0000 ou 1
     */
    public $aliquot;

    /**
     *@var float valor de deduções
     *
     */
    public $valueDeductions;

    /**
     *@var float valor líquido da NFse
     *
     */
    public $netValue;

    /**
     *@var float valor de outras deduções
     *
     */
    public $otherDeductions;

    /**
     *@var float valor do PIS
     *
     */
    public $valuePis;

    /**
     *@var float valor do Confis
     *
     */
    public $valueConfis;

    /**
     *@var float valor do INSS
     *
     */
    public $valueINSS;

    /**
     *@var float valor do IR
     *
     */
    public $valueIR;

    /**
     *@var float valor do CSLL
     *
     */
    public $valueCSLL;

    /**
     *@var float valor do desconto condicionado
     *
     */
    public $discountCondition;

    /**
     *@var float valor do desconto incondicionado
     *
     */
    public $unconditionedDiscount;

    /**
     *@var float valor de base de calculo
     *
     */
    public $calculationBase;
}
