<?php namespace Nfse\Soap;

class BatchRPS
{

    public $wsResponse;
    public $error;
    public $dataLote;
    public $messages = '';
    public $domDocument;

    //construtor (passar o SOAP response)
    public function __construct($wsResponse)
    {
        $this->wsResponse = $wsResponse;
        $this->domDocument = new \DOMDocument('1.0');
        $this->domDocument->preserveWhiteSpace = false;
        $this->domDocument->formatOutput = true;
    }

    //retorna os erros de processamento
    public function getError()
    {
        return $this->error;
    }

    //retorna os dados de entrada do lote após o envio
    public function getDadosLote()
    {
        if (is_object($this->wsResponse) && isset($this->wsResponse->outputXML)) {

            //salva o xml do lote no array e carrega o objeto
            $this->dataLote['xml'] = $this->wsResponse->outputXML;
            $this->wsResponse = simplexml_load_string($this->wsResponse->outputXML);

            if(isset( $this->wsResponse->ListaMensagemRetorno->MensagemRetorno)){
                return ($this->wsResponse->ListaMensagemRetorno->MensagemRetorno);
            }

            $nfsList = $this->wsResponse->ListaNfse->CompNfse;
            //verifica se há mais de uma nota no lote
            if (count($nfsList) > 0) {
                foreach ($nfsList as $NFS) {
                    //adiciona a nota ao array
                    $this->dataLote['nfs'][$NFS->Nfse->InfNfse->Numero->__toString()] = $this->getInfNfse($NFS->Nfse->InfNfse);
                }
            } else {
                $this->dataLote['nfs'] = null;
            }

            //retorna o array montado
            return $this->dataLote;

        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }

    //joga os dados de uma nota dentro do lote para um array
    public function getInfNfse($InfNfse)
    {
        try {
            return [
                'numero' => $InfNfse->Numero->__toString(),
                'codigoVerificacao' => $InfNfse->CodigoVerificacao->__toString(),
                'dataEmissao' => $InfNfse->DataEmissao->__toString(),

                'identificacaoRps' => [
                    'numero' => $InfNfse->IdentificacaoRps->Numero->__toString(),
                    'serie' => $InfNfse->IdentificacaoRps->Serie->__toString(),
                    'tipo' => $InfNfse->IdentificacaoRps->Tipo->__toString(),
                ],

                'dataEmissaoRps' => $InfNfse->DataEmissaoRps->__toString(),
                'naturezaOperacao' => isset($InfNfse->NaturezaOperacao) ? $InfNfse->NaturezaOperacao->__toString() : null,
                'regimeEspecialTributacao' => isset($InfNfse->RegimeEspecialTributacao) ? $InfNfse->RegimeEspecialTributacao->__toString() : null,
                'optanteSimplesNacional' => isset($InfNfse->OptanteSimplesNacional) ? $InfNfse->OptanteSimplesNacional->__toString() : null,
                'incentivadorCultural' => isset($InfNfse->IncentivadorCultural) ? $InfNfse->IncentivadorCultural->__toString() : null,
                'competencia' => isset($InfNfse->Competencia) ? $InfNfse->Competencia->__toString() : null,
                'outrasInformacoes' => isset($InfNfse->OutrasInformacoes) ? $InfNfse->OutrasInformacoes->__toString() : null,

                'servico' => [
                    'valores' => [
                        'valorServicos' => (isset($InfNfse->Servico->Valores->ValorServicos)) ? $InfNfse->Servico->Valores->ValorServicos->__toString() : 0,
                        'valorDeducoes' => (isset($InfNfse->Servico->Valores->ValorDeducoes)) ? $InfNfse->Servico->Valores->ValorDeducoes->__toString() : 0,
                        'valorPis' => (isset($InfNfse->Servico->Valores->ValorPis)) ? $InfNfse->Servico->Valores->ValorPis->__toString() : 0,
                        'valorCofins' => (isset($InfNfse->Servico->Valores->ValorCofins)) ? $InfNfse->Servico->Valores->ValorCofins->__toString() : 0,
                        'valorInss' => (isset($InfNfse->Servico->Valores->ValorInss)) ? $InfNfse->Servico->Valores->ValorInss->__toString() : 0,
                        'valorIr' => (isset($InfNfse->Servico->Valores->ValorIr)) ? $InfNfse->Servico->Valores->ValorIr->__toString() : 0,
                        'valorCsll' => (isset($InfNfse->Servico->Valores->ValorCsll)) ? $InfNfse->Servico->Valores->ValorCsll->__toString() : 0,
                        'issRetido' => (isset($InfNfse->Servico->Valores->IssRetido)) ? $InfNfse->Servico->Valores->IssRetido->__toString() : 0,
                        'valorIss' => (isset($InfNfse->Servico->Valores->ValorIss)) ? $InfNfse->Servico->Valores->ValorIss->__toString() : 0,
                        'valorIssRetido' => (isset($InfNfse->Servico->Valores->ValorIssRetido)) ? $InfNfse->Servico->Valores->ValorIssRetido->__toString() : 0,
                        'outrasRetencoes' => (isset($InfNfse->Servico->Valores->OutrasRetencoes)) ? $InfNfse->Servico->Valores->OutrasRetencoes->__toString() : 0,
                        'baseCalculo' => (isset($InfNfse->Servico->Valores->BaseCalculo)) ? $InfNfse->Servico->Valores->BaseCalculo->__toString() : 0,
                        'aliquota' => (isset($InfNfse->Servico->Valores->Aliquota)) ? $InfNfse->Servico->Valores->Aliquota->__toString() : 0,
                        'valorLiquidoNfse' => (isset($InfNfse->Servico->Valores->ValorLiquidoNfse)) ? $InfNfse->Servico->Valores->ValorLiquidoNfse->__toString() : 0,
                        'descontoIncondicionado' => (isset($InfNfse->Servico->Valores->DescontoIncondicionado)) ? $InfNfse->Servico->Valores->DescontoIncondicionado->__toString() : 0,
                        'descontoCondicionado' => (isset($InfNfse->Servico->Valores->DescontoCondicionado)) ? $InfNfse->Servico->Valores->DescontoCondicionado->__toString() : 0,
                    ],

                    'itemListaServico' => $InfNfse->Servico->ItemListaServico->__toString(),
                    'codigoTributacaoMunicipio' => $InfNfse->Servico->CodigoTributacaoMunicipio->__toString(),
                    'discriminacao' => $InfNfse->Servico->Discriminacao->__toString(),
                    'codigoMunicipio' => isset($InfNfse->Servico->CodigoMunicipio) ? $InfNfse->Servico->CodigoMunicipio->__toString() : null,
                ],

                'prestadorServico' => [
                    'cpfCnpj' => isset($InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj) ? $InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj->__toString() : $InfNfse->PrestadorServico->IdentificacaoPrestador->Cpf->__toString(),
                    'inscricaoMunicipal' => isset($InfNfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal) ? $InfNfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal->__toString() : null,
                    'razaoSocial' => $InfNfse->PrestadorServico->RazaoSocial->__toString(),
                    'nomeFantasia' => $InfNfse->PrestadorServico->NomeFantasia->__toString(),
                    'endereco' => $InfNfse->PrestadorServico->Endereco->Endereco->__toString(),
                    'numero' => $InfNfse->PrestadorServico->Endereco->Numero->__toString(),
                    'complemento' => isset($InfNfse->PrestadorServico->Endereco->Complemento) ? $InfNfse->PrestadorServico->Endereco->Complemento->__toString() : null,
                    'bairro' => $InfNfse->PrestadorServico->Endereco->Bairro->__toString(),
                    'codigoMunicipio' => $InfNfse->PrestadorServico->Endereco->CodigoMunicipio->__toString(),
                    'uf' => $InfNfse->PrestadorServico->Endereco->Uf->__toString(),
                    'cep' => $InfNfse->PrestadorServico->Endereco->Cep->__toString(),
                    'telefone' => isset($InfNfse->PrestadorServico->Contato->Telefone) ? $InfNfse->PrestadorServico->Contato->Telefone->__toString() : null,
                    'email' => isset($InfNfse->PrestadorServico->Contato->Email) ? $InfNfse->PrestadorServico->Contato->Email->__toString() : null,
                ],

                'tomadorServico' => [
                    'cpfCnpj' => isset($InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj) ? $InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj->__toString() : $InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cpf->__toString(),
                    'inscricaoMunicipal' => isset($InfNfse->TomadorServico->IdentificacaoTomador->InscricaoMunicipal) ? $InfNfse->TomadorServico->IdentificacaoTomador->InscricaoMunicipal->__toString() : null,
                    'razaoSocial' => $InfNfse->TomadorServico->RazaoSocial->__toString(),
                    'endereco' => $InfNfse->TomadorServico->Endereco->Endereco->__toString(),
                    'numero' => $InfNfse->TomadorServico->Endereco->Numero->__toString(),
                    'complemento' => isset($InfNfse->TomadorServico->Endereco->Complemento) ? $InfNfse->TomadorServico->Endereco->Complemento->__toString() : null,
                    'bairro' => $InfNfse->TomadorServico->Endereco->Bairro->__toString(),
                    'cep' => $InfNfse->TomadorServico->Endereco->Cep->__toString(),
                ],

                'orgaoGerador' => isset($InfNfse->OrgaoGerador) ? [
                    'codigoMunicipio' => isset($InfNfse->OrgaoGerador->CodigoMunicipio) ? $InfNfse->OrgaoGerador->CodigoMunicipio->__toString() : null,
                    'uf' => isset($InfNfse->OrgaoGerador->Uf) ? $InfNfse->OrgaoGerador->Uf->__toString() : null,
                ] : null,
            ];
        } catch (\Exception $x) {
            echo $x->getFile();
            echo $x->getLine();
            echo $x->getMessage();
        }
    }

    //retorna os dados de uma pesquisa de notas
    public function getInfSearchNFSe($InfNfse)
    {
        return [
            'numero' => $InfNfse->Numero->__toString(),
            'codigoVerificacao' => $InfNfse->CodigoVerificacao->__toString(),
            'dataEmissao' => $InfNfse->DataEmissao->__toString(),
            'naturezaOperacao' => isset($InfNfse->NaturezaOperacao) ? $InfNfse->NaturezaOperacao->__toString() : null,
            'regimeEspecialTributacao' => isset($InfNfse->RegimeEspecialTributacao) ? $InfNfse->RegimeEspecialTributacao->__toString() : null,
            'optanteSimplesNacional' => isset($InfNfse->OptanteSimplesNacional) ? $InfNfse->OptanteSimplesNacional->__toString() : null,
            'incentivadorCultural' => isset($InfNfse->IncentivadorCultural) ? $InfNfse->IncentivadorCultural->__toString() : null,
            'competencia' => isset($InfNfse->Competencia) ? $InfNfse->Competencia->__toString() : null,
            'outrasInformacoes' => isset($InfNfse->OutrasInformacoes) ? $InfNfse->OutrasInformacoes->__toString() : null,

            'servico' => [
                'valores' => [
                    'valorServicos' => (isset($InfNfse->Servico->Valores->ValorServicos)) ? $InfNfse->Servico->Valores->ValorServicos->__toString() : 0,
                    'valorDeducoes' => (isset($InfNfse->Servico->Valores->ValorDeducoes)) ? $InfNfse->Servico->Valores->ValorDeducoes->__toString() : 0,
                    'valorPis' => (isset($InfNfse->Servico->Valores->ValorPis)) ? $InfNfse->Servico->Valores->ValorPis->__toString() : 0,
                    'valorCofins' => (isset($InfNfse->Servico->Valores->ValorCofins)) ? $InfNfse->Servico->Valores->ValorCofins->__toString() : 0,
                    'valorInss' => (isset($InfNfse->Servico->Valores->ValorInss)) ? $InfNfse->Servico->Valores->ValorInss->__toString() : 0,
                    'valorIr' => (isset($InfNfse->Servico->Valores->ValorIr)) ? $InfNfse->Servico->Valores->ValorIr->__toString() : 0,
                    'valorCsll' => (isset($InfNfse->Servico->Valores->ValorCsll)) ? $InfNfse->Servico->Valores->ValorCsll->__toString() : 0,
                    'issRetido' => (isset($InfNfse->Servico->Valores->IssRetido)) ? $InfNfse->Servico->Valores->IssRetido->__toString() : 0,
                    'valorIss' => (isset($InfNfse->Servico->Valores->ValorIss)) ? $InfNfse->Servico->Valores->ValorIss->__toString() : 0,
                    'valorIssRetido' => (isset($InfNfse->Servico->Valores->ValorIssRetido)) ? $InfNfse->Servico->Valores->ValorIssRetido->__toString() : 0,
                    'outrasRetencoes' => (isset($InfNfse->Servico->Valores->OutrasRetencoes)) ? $InfNfse->Servico->Valores->OutrasRetencoes->__toString() : 0,
                    'baseCalculo' => (isset($InfNfse->Servico->Valores->BaseCalculo)) ? $InfNfse->Servico->Valores->BaseCalculo->__toString() : 0,
                    'aliquota' => (isset($InfNfse->Servico->Valores->Aliquota)) ? $InfNfse->Servico->Valores->Aliquota->__toString() : 0,
                    'valorLiquidoNfse' => (isset($InfNfse->Servico->Valores->ValorLiquidoNfse)) ? $InfNfse->Servico->Valores->ValorLiquidoNfse->__toString() : 0,
                    'descontoIncondicionado' => (isset($InfNfse->Servico->Valores->DescontoIncondicionado)) ? $InfNfse->Servico->Valores->DescontoIncondicionado->__toString() : 0,
                    'descontoCondicionado' => (isset($InfNfse->Servico->Valores->DescontoCondicionado)) ? $InfNfse->Servico->Valores->DescontoCondicionado->__toString() : 0,
                ],

                'itemListaServico' => $InfNfse->Servico->ItemListaServico->__toString(),
                'codigoTributacaoMunicipio' => $InfNfse->Servico->CodigoTributacaoMunicipio->__toString(),
                'discriminacao' => $InfNfse->Servico->Discriminacao->__toString(),
                'codigoMunicipio' => isset($InfNfse->Servico->CodigoMunicipio) ? $InfNfse->Servico->CodigoMunicipio->__toString() : null,
            ],

            'prestadorServico' => [
                'cpfCnpj' => isset($InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj) ? $InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj->__toString() : $InfNfse->PrestadorServico->IdentificacaoPrestador->Cpf->__toString(),
                'inscricaoMunicipal' => isset($InfNfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal) ? $InfNfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal->__toString() : null,
                'razaoSocial' => $InfNfse->PrestadorServico->RazaoSocial->__toString(),
                'nomeFantasia' => $InfNfse->PrestadorServico->NomeFantasia->__toString(),
                'endereco' => $InfNfse->PrestadorServico->Endereco->Endereco->__toString(),
                'numero' => $InfNfse->PrestadorServico->Endereco->Numero->__toString(),
                'complemento' => isset($InfNfse->PrestadorServico->Endereco->Complemento) ? $InfNfse->PrestadorServico->Endereco->Complemento->__toString() : null,
                'bairro' => $InfNfse->PrestadorServico->Endereco->Bairro->__toString(),
                'codigoMunicipio' => $InfNfse->PrestadorServico->Endereco->CodigoMunicipio->__toString(),
                'uf' => $InfNfse->PrestadorServico->Endereco->Uf->__toString(),
                'cep' => $InfNfse->PrestadorServico->Endereco->Cep->__toString(),
                'telefone' => isset($InfNfse->PrestadorServico->Contato->Telefone) ? $InfNfse->PrestadorServico->Contato->Telefone->__toString() : null,
                'email' => isset($InfNfse->PrestadorServico->Contato->Email) ? $InfNfse->PrestadorServico->Contato->Email->__toString() : null,
            ],

            'tomadorServico' => [
                'cpfCnpj' => isset($InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj) ? $InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cnpj->__toString() : $InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj->Cpf->__toString(),
                'inscricaoMunicipal' => isset($InfNfse->TomadorServico->IdentificacaoTomador->InscricaoMunicipal) ? $InfNfse->TomadorServico->IdentificacaoTomador->InscricaoMunicipal->__toString() : null,
                'razaoSocial' => $InfNfse->TomadorServico->RazaoSocial->__toString(),
                'endereco' => $InfNfse->TomadorServico->Endereco->Endereco->__toString(),
                'numero' => $InfNfse->TomadorServico->Endereco->Numero->__toString(),
                'complemento' => isset($InfNfse->TomadorServico->Endereco->Complemento) ? $InfNfse->TomadorServico->Endereco->Complemento->__toString() : null,
                'bairro' => $InfNfse->TomadorServico->Endereco->Bairro->__toString(),
                'cep' => $InfNfse->TomadorServico->Endereco->Cep->__toString(),
                'codigoMunicipio' => isset($InfNfse->TomadorServico->Endereco->CodigoMunicipio) ? $InfNfse->TomadorServico->Endereco->CodigoMunicipio->__toString() : null,
                'uf' => isset($InfNfse->TomadorServico->Endereco->Uf) ? $InfNfse->TomadorServico->Endereco->Uf->__toString() : null,
            ],

            'orgaoGerador' => isset($InfNfse->OrgaoGerador) ? [
                'codigoMunicipio' => isset($InfNfse->OrgaoGerador->CodigoMunicipio) ? $InfNfse->OrgaoGerador->CodigoMunicipio->__toString() : null,
                'uf' => isset($InfNfse->OrgaoGerador->Uf) ? $InfNfse->OrgaoGerador->Uf->__toString() : null,
            ] : null,
        ];
    }

    //retorna a lista de erros de processamento no caso dos lotes rejeitados
    public function getErrosLote()
    {
        if (is_object($this->wsResponse) && isset($this->wsResponse->outputXML)) {
            $wsObject = simplexml_load_string($this->wsResponse->outputXML);
            $listaMensagens = $wsObject->ListaMensagemRetornoLote;
            if ($wsObject && $listaMensagens) {
                if (count($listaMensagens->MensagemRetorno) > 0) {
                    foreach ($listaMensagens->MensagemRetorno as $msg) {
                        $this->messages .= "Erro na RPS nº {$msg->IdentificacaoRps->Numero} serie: {$msg->IdentificacaoRps->Serie} tipo: {$msg->IdentificacaoRps->Tipo}. " . $msg->Codigo . ' - ' . $msg->Mensagem . '<br>';
                    }
                } else {
                    $this->messages = "Erro na RPS nº {$listaMensagens->IdentificacaoRps->Numero} serie: {$listaMensagens->IdentificacaoRps->Serie} tipo: {$listaMensagens->IdentificacaoRps->Tipo}. " . $listaMensagens->MensagemRetorno->Codigo . ' - ' . $listaMensagens->MensagemRetorno->Mensagem . '<br>';
                }

                return $this->messages;
            } else {
                $this->error = "O servidor da prefeitura não retornou nenhuma mensagem na lista.";
                return false;
            }
        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }

}
