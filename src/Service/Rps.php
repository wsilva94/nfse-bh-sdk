<?php namespace NFse\Service;

use Exception;
use NFse\Helpers\Utils;
use NFse\Models\Lot;
use NFse\Models\Settings;
use NFse\Sanitizers\Num;
use NFse\Sanitizers\Text;
use NFse\Signature\Subscriber;

class Rps
{

    private $xml;
    private $rps;
    private $infRps;
    private $servico;
    private $prestador;
    private $tomador;
    private $num, $text;
    private $settings;
    private $subscriber;

    const CNPJ = 1;
    const CPF = 2;

    /**
     * constroi a tag de RPS
     *
     * @param NFse\Models\Settings;
     * @param string;
     */
    public function __construct(Settings $settings, string $idRps)
    {
        try {
            $this->settings = $settings;
            $this->subscriber = new Subscriber($settings);

            //cria o documento XML
            $this->xml = new \DOMDocument('1.0', 'utf-8');
            $this->xml->preserveWhiteSpace = false;
            $this->xml->formatOutput = true;

            //cria as tags mãe
            $this->rps = $this->xml->createElement("Rps");
            $this->servico = $this->xml->createElement("Servico");
            $this->prestador = $this->xml->createElement("Prestador");
            $this->tomador = $this->xml->createElement("Tomador");

            //seta os ids para assinatura posterior
            $this->rps->setAttribute('xmlns', 'http://www.abrasf.org.br/nfse.xsd');
            $this->rps->setAttribute('Id', $idRps);
            $this->infRps = $this->xml->createElement('InfRps');
            $this->infRps->setAttribute('Id', 'rps:' . $idRps);

            //inicia os validators
            $this->num = new Num();
            $this->text = new Text();

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * monta a tag de indentificação da RPS
     *
     * @param NFse\Models\Lot;
     */
    public function setRpsIdentification(Lot $lot): void
    {
        try {
            $this->validateRpsIdentification($lot);

            //cria os elementos DOM
            $tagIdentfRps = $this->xml->createElement('IdentificacaoRps');
            $tagNumRps = $this->xml->createElement('Numero', $lot->rps->number);
            $tagSerieRps = $this->xml->createElement('Serie', $lot->rps->serie);
            $tagTipoRps = $this->xml->createElement('Tipo', $lot->rps->type);
            $tagDtEmissao = $this->xml->createElement('DataEmissao', str_replace(' ', 'T', $lot->rps->date));
            $tagNatOperacao = $this->xml->createElement('NaturezaOperacao', $lot->rps->nature);
            $tagRegTributacao = $this->xml->createElement('RegimeEspecialTributacao', $lot->rps->regime);
            $tagSimplesNac = $this->xml->createElement('OptanteSimplesNacional', $lot->rps->simple);
            $tagIncentivCult = $this->xml->createElement('IncentivadorCultural', $lot->rps->culturalPromoter);
            $tagStatus = $this->xml->createElement('Status', $lot->rps->status);

            //append da identificação de RPS
            $tagIdentfRps->appendChild($tagNumRps);
            $tagIdentfRps->appendChild($tagSerieRps);
            $tagIdentfRps->appendChild($tagTipoRps);
            $this->infRps->appendChild($tagIdentfRps);

            //apend dos dados restantes
            $this->infRps->appendChild($tagDtEmissao);
            $this->infRps->appendChild($tagNatOperacao);
            $this->infRps->appendChild($tagRegTributacao);
            $this->infRps->appendChild($tagSimplesNac);
            $this->infRps->appendChild($tagIncentivCult);
            $this->infRps->appendChild($tagStatus);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * monta a tag de indentificação da RPS
     *
     * @param NFse\Models\Lot;
     */
    private function validateRpsIdentification(Lot $lot): void
    {
        try {

            //faz as validações nescessarias
            if (empty($lot->rps->number) || empty($lot->rps->serie) || empty($lot->rps->type)) {
                throw new \Exception("O RPS não contém número, serie ou tipo.");
            }

            // datetime posteriormente será convertido para o padrão do lote
            if (empty($lot->rps->date) || !Utils::isDate($lot->rps->date, 'Y-m-d H:i:s', 'America/Sao_Paulo')) {
                throw new \Exception("A data de emissão do RPS é inválida ou está em branco.");
            }

            // 1 – Tributação no município |  2 - Tributação fora do município | 3 - Isenção |  4 - Imune | 5 – Exigibilidade suspensa por decisão judicial | 6- Exigibilidade suspensa por procedimento administrativo
            if (empty($lot->rps->nature) || !in_array($lot->rps->nature, [1, 2, 3, 4, 5, 6])) {
                throw new \Exception("A natureza de operação do RPS é inválida ou está em branco.");
            }

            // 1 – Microempresa municipal | 2 - Estimativa | 3 – Sociedade de profissionais | 4 – Cooperativa | 5 – MEI – Simples Nacional | 6 – ME EPP – Simples Nacional
            if (empty($lot->rps->regime) || !in_array($lot->rps->regime, [1, 2, 3, 4, 5, 6])) {
                throw new \Exception("O regime especial de tributação não foi definido.");
            }

            // 1 - Sim | 2 - Não
            if (empty($lot->rps->simple) || !in_array($lot->rps->simple, [1, 2])) {
                throw new \Exception("Não foi definido se o prestador é optante do Simples Nacional.");
            }

            // 1 - Sim | 2 - Não
            if (empty($lot->rps->culturalPromoter) || !in_array($lot->rps->culturalPromoter, [1, 2])) {
                throw new \Exception("Não foi definido se o prestador é incentivador cultural.");
            }

            // 1 – Normal | 2 – Cancelado
            if (empty($lot->rps->status) || !in_array($lot->rps->status, [1, 2])) {
                throw new \Exception("Não foi definido o status do RPS.");
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * monta a tag com os dados do serviço
     *
     * @param NFse\Models\Lot;
     */
    public function setService(Lot $lot): void
    {
        try {
            $this->validateService($lot);
            //cria as tags
            $tagItemLista = $this->xml->createElement("ItemListaServico", $lot->rps->service->itemList);
            $tagCodTribut = $this->xml->createElement("CodigoTributacaoMunicipio", $lot->rps->service->municipalityTaxationCode);
            $tagDiscriminacao = $this->xml->createElement("Discriminacao", $lot->rps->service->description);
            $tagCodMunicipio = $this->xml->createElement("CodigoMunicipio", $lot->rps->service->municipalCode);
            $tagValores = $this->xml->createElement("Valores");
            $tagValServicos = $this->xml->createElement('ValorServicos', $lot->rps->service->serviceValue);
            $tagValDeducoes = $this->xml->createElement('ValorDeducoes', $lot->rps->service->valueDeductions);
            $tagValPis = $this->xml->createElement('ValorPis', $lot->rps->service->valuePis);
            $tagValCofins = $this->xml->createElement('ValorCofins', $lot->rps->service->valueConfis);
            $tagValInss = $this->xml->createElement('ValorInss', $lot->rps->service->valueINSS);
            $tagValIR = $this->xml->createElement('ValorIr', $lot->rps->service->valueIR);
            $tagValCSLL = $this->xml->createElement('ValorCsll', $lot->rps->service->valueCSLL);
            $tagISSRetido = $this->xml->createElement('IssRetido', $lot->rps->service->issWithheld);
            $tagOutrasRet = $this->xml->createElement('OutrasRetencoes', $lot->rps->service->otherDeductions);
            $tagAliquota = $this->xml->createElement('Aliquota', $lot->rps->service->aliquot);
            $tagDescontoInc = $this->xml->createElement('DescontoIncondicionado', $lot->rps->service->unconditionedDiscount);
            $tagDescontoCond = $this->xml->createElement('DescontoCondicionado', $lot->rps->service->discountCondition);

            //faz o append
            $tagValores->appendChild($tagValServicos);
            $tagValores->appendChild($tagValDeducoes);
            $tagValores->appendChild($tagValPis);
            $tagValores->appendChild($tagValCofins);
            $tagValores->appendChild($tagValInss);
            $tagValores->appendChild($tagValIR);
            $tagValores->appendChild($tagValCSLL);
            $tagValores->appendChild($tagISSRetido);
            $tagValores->appendChild($tagOutrasRet);
            $tagValores->appendChild($tagAliquota);
            $tagValores->appendChild($tagDescontoInc);
            $tagValores->appendChild($tagDescontoCond);

            $this->servico->appendChild($tagValores);
            $this->servico->appendChild($tagItemLista);
            $this->servico->appendChild($tagCodTribut);
            $this->servico->appendChild($tagDiscriminacao);
            $this->servico->appendChild($tagCodMunicipio);
        } catch (Exception $e) {
            throw $e;
        }

    }

    /**
     * Realiza a validação dos parametros informado em serviço
     *
     * @param NFse\Models\Lot;
     */
    private function validateService(Lot $lot): void
    {
        try {
            //faz o sanitize das entradas
            $codMun = $this->num->with($lot->rps->service->municipalCode)->sanitize()->get();
            $codTribMun = $this->num->with($lot->rps->service->municipalityTaxationCode)->sanitize()->get();
            $disc = $this->text->with($lot->rps->service->description)->maxL(2000)->get();

            // verifica o código do item da lista de serviço
            if (empty($lot->rps->service->itemList) || strlen($lot->rps->service->itemList) > 5) {
                throw new \Exception("O item da lista de serviço não existe ou excede o limite de 5 caracteres.");
            }

            // verifica o cod do municipio
            if (empty($codTribMun) || strlen($codTribMun) > 20) {
                throw new \Exception("O código de tributaçaõ do municipio é inválido.");
            }

            // verifica o cod do municipio
            if (empty($codMun) || strlen($codMun) != 7) {
                throw new \Exception("O código do municipio de prestação do serviço é inválido.");
            }

            //verifica a descrição
            if (empty($disc)) {
                throw new \Exception("A descrição do serviço prestado está em branco ou excede o limite de 2000 caracteres.");
            }

            //verifica o valor do serviço
            if (empty($lot->rps->service->serviceValue) || $lot->rps->service->serviceValue == 0) {
                throw new \Exception("O valor do serviço não pode ser igual a 0.");
            }

            // 1 - Sim | 2 - Não
            if (empty($lot->rps->service->issWithheld) || !in_array($lot->rps->service->issWithheld, [1, 2])) {
                throw new \Exception("Não foi definido a informação de ISS retido.");
            }

            //aliquota em valor percentual.Formato: 0.0000 Ex: 1% = 0.01 | 25,5% = 0.255 | 100% = 1.0000 ou 1
            if (strlen($lot->rps->service->aliquot) > 5) {
                throw new \Exception("O valor da aliquota é inválida.");
            }

            //faz uma última validação nos valores da tag serviço
            $checkValues = [
                'valor dos serviços ' => $lot->rps->service->serviceValue,
                'valor das deduções' => $lot->rps->service->valueDeductions,
                'valor do PIS' => $lot->rps->service->valuePis,
                'valor do COFINS' => $lot->rps->service->valueConfis,
                'valor do INSS' => $lot->rps->service->valueINSS,
                'valor do IR' => $lot->rps->service->valueIR,
                'valor do CSLL' => $lot->rps->service->valueCSLL,
                'desconto condicionado' => $lot->rps->service->discountCondition,
                'desconto incondicionado' => $lot->rps->service->unconditionedDiscount,
            ];
            foreach ($checkValues as $k => $valor) {
                if (!Utils::isValor($valor)) {
                    throw new Exception("O campo {$k} não é composto de um valor monetário válido.");
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * seta o prestador de serviços
     */
    public function setProvider(): void
    {
        try {
            $tagCnpj = $this->xml->createElement('Cnpj', $this->settings->issuer->cnpj);
            $tagIm = $this->xml->createElement('InscricaoMunicipal', $this->settings->issuer->imun);
            $this->prestador->appendChild($tagCnpj);
            $this->prestador->appendChild($tagIm);
            $this->infRps->appendChild($this->prestador);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *seta o tomador de serviços
     *
     * @param NFse\Models\Lot;
     */
    public function setTaker(Lot $lot)
    {
        try {
            $this->validateTaker($lot);

            $tagIdentifTomador = $this->xml->createElement('IdentificacaoTomador');
            $tagCpfCnpj = $this->xml->createElement('CpfCnpj');
            $tagCpf = $this->xml->createElement('Cpf', $lot->rps->taker->document);
            $tagCnpj = $this->xml->createElement('Cnpj', $lot->rps->taker->document);
            $tagInscMunicipal = $this->xml->createElement('InscricaoMunicipal', $lot->rps->taker->municipalRegistration);
            $tagRzSocial = $this->xml->createElement('RazaoSocial', $lot->rps->taker->name);

            $tagEndereco = $this->xml->createElement('Endereco');
            $tagRua = $this->xml->createElement('Endereco', $lot->rps->taker->address->address);
            $tagNumero = $this->xml->createElement('Numero', $lot->rps->taker->address->number);
            $tagComplemento = $this->xml->createElement('Complemento', $lot->rps->taker->address->complement);
            $tagBairro = $this->xml->createElement('Bairro', $lot->rps->taker->address->neighborhood);
            $tagCodMunicipio = $this->xml->createElement('CodigoMunicipio', $lot->rps->taker->address->municipalityCode);
            $tagUf = $this->xml->createElement('Uf', $lot->rps->taker->address->state);
            $tagCep = $this->xml->createElement('Cep', $lot->rps->taker->address->zipCode);

            //faz o append das tags
            $tagCpfCnpj->appendChild(($lot->rps->taker->type == Self::CNPJ) ? $tagCnpj : $tagCpf);
            $tagIdentifTomador->appendChild($tagCpfCnpj);

            if (!empty($lot->rps->taker->municipalRegistration) && preg_match('/^[0-9]+$/', $lot->rps->taker->municipalRegistration)) {
                //não vazia e somente números
                $tagIdentifTomador->appendChild($tagInscMunicipal);
            }

            $this->tomador->appendChild($tagIdentifTomador);
            $this->tomador->appendChild($tagRzSocial);
            $tagEndereco->appendChild($tagRua);
            $tagEndereco->appendChild($tagNumero);

            if (!empty($lot->rps->taker->address->complement)) {
                $tagEndereco->appendChild($tagComplemento);
            }

            $tagEndereco->appendChild($tagBairro);
            $tagEndereco->appendChild($tagCodMunicipio);
            $tagEndereco->appendChild($tagUf);
            $tagEndereco->appendChild($tagCep);

            $this->tomador->appendChild($tagEndereco);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *seta o tomador de serviços
     *
     * @param NFse\Models\Lot;
     */
    private function validateTaker(Lot $lot)
    {
        //sanitiza as entradas
        $razaoSocial = $this->text->with($lot->rps->taker->name)->sanitize()->toUpper()->get();
        $docTomador = $this->num->with($lot->rps->taker->address->address)->sanitize()->get();
        $endereco['endereco'] = $this->text->with($lot->rps->taker->address->address)->sanitize()->maxL(125)->toUpper()->get();
        $endereco['numero'] = $this->text->with($lot->rps->taker->address->number)->sanitize()->maxL(10)->toUpper()->get();
        $endereco['complemento'] = $this->text->with($lot->rps->taker->address->complement)->sanitize()->maxL(60)->toUpper()->get();
        $endereco['codMunicipio'] = $this->num->with($lot->rps->taker->address->municipalityCode)->sanitize()->maxL(7)->get();
        $endereco['uf'] = $this->text->with($lot->rps->taker->address->state)->sanitize()->maxL(2)->toUpper()->get();
        $endereco['cep'] = $this->num->with($lot->rps->taker->address->zipCode)->sanitize()->maxL(8)->get();

        //faz as validações nescessárias
        if (empty($lot->rps->taker->type) || !in_array($lot->rps->taker->type, [1, 2])) {
            throw new Exception("O tipo do tomador de serviços é inváldo.");
        }

        if (empty($lot->rps->taker->name) || strlen($lot->rps->taker->name) > 115) {
            throw new \Exception("A razão social do tomador de serviços está em branco ou excede o limite de 115 caractéres.");
        }

        if ($lot->rps->taker->type == self::CPF && strlen($docTomador) > 11) {
            throw new Exception("O CPF do tomador de serviços está em branco ou excede o limite de 11 caractéres.");
        } elseif ($lot->rps->taker->type == self::CNPJ && strlen($docTomador) > 14) {
            throw new Exception("O CNPJ do tomador de serviços está em branco ou excede o limite de 14 caractéres.");
        }

        if (empty($lot->rps->taker->municipalRegistration) || strlen($lot->rps->taker->municipalRegistration) > 15) {
            throw new \Exception("A inscrição municipal do tomador de serviços está em branco ou excede o limite de 15 caractéres.");
        }
    }

    /**
     * retorna o xml ou o domNode da RPS
     *
     * @param string
     */
    public function getRps(string $mode = 'xml'): string
    {
        try {
            $this->infRps->appendChild($this->servico);
            $this->infRps->appendChild($this->prestador);
            $this->infRps->appendChild($this->tomador);
            $this->rps->appendChild($this->infRps);
            $this->xml->appendChild($this->rps);

            return ($mode == 'xml') ? $this->xml->saveXML() : $this->xml->documentElement;
        } catch (Exception $e) {
            throw $e;
        }
    }

    //retorna o xml da RPS assinado
    public function getSignedRps()
    {
        try {
            $xmlRps = Utils::xmlFilter($this->getRps());
            $this->subscriber->loadPFX();
            $xmlSigned = $this->subscriber->assina($xmlRps, 'Rps');

            return $xmlSigned;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
