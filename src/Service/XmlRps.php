<?php namespace NFse\Service;

use NFse\Helpers\XML;
use NFse\Models\Settings;
use NFse\Sanitizers\Num;
use NFse\Sanitizers\Text;

class XmlRps
{

    private $countRps = 0;
    private $signedRps = '';
    private $settings;
    private $xml, $num, $text;

    /**
     * construtor inicializando os dados do lote
     *
     * @param NFse\Models\Settings;
     * @param int;
     */
    public function __construct(Settings $settings, int $numLote)
    {
        $this->settings = $settings;
        //inicializa os validators
        $this->num = new Num;
        $this->text = new Text;

        //cria o documento XML
        $this->xml = XML::load('loteRps')
            ->set('NumeroLote', $this->text->with($numLote)->sanitize()->get())
            ->set('Cnpj', $this->num->with($this->settings->issuer->cnpj)->sanitize()->get())
            ->set('InscricaoMunicipal', $this->num->with($this->settings->issuer->imun)->sanitize()->get())
            ->filter()->save();
    }

    /**
     * adiciona uma RPS ao lote
     *
     * @param NFse\Models\Settings;
     * @param int;
     */
    public function addRps($rps)
    {
        $this->countRps++;
        $this->signedRps .= $rps;
    }

    /**
     * retorna o XML do lote pronto para assinatura
     */
    public function getLoteRps(): string
    {
        $xml = XML::load($this->xml, true)
            ->set('QuantidadeRps', $this->countRps)
            ->set('Rps', $this->signedRps)
            ->filter()->save();

        return $xml;
    }

}
