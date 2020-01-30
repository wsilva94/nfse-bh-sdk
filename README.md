# Biblioteca para emissão de NFSE BH

API de emissão, consulta e cancelamento de notas fiscais de serviço via webservice da prefeitura de Belo Horizonte - MG


### Pré-requisitos

O que você precisa para instalar o software e como instalá-lo

`` ``
PHP 7.2
`` ``
### Instalando

Efetue download utilizando composer

`` ``
composer require nfse/nfse-bh-sdk
`` ``

## Começando

* Siga os passos abaixo para consumir corretamente a biblioteca <br><br>

1 - Adicione seu certificado digital .PFX no seguinte diretório <br>
    * EX: storage/certificates/56142462000106/certificate.pfx <br><br>
    * pasta: 56142462000106 é numero do CNPJ da sua empresa. <br><br>

2 - Sempre que desejar usar algum END-POINT implementado, passe sempre os parâmetros de configuração.<br>

EX: tests/systemSettings <br>

    use Exception;
    use NFse\Config\Boot;
    use NFse\Helpers\Utils;
    use NFse\Models\Settings;

    try {
        //ambiente
        $settings = new Settings();
        $settings->environment = 'homologacao';

        //Emitente
        $settings->issuer->name = 'LINK SERVICOS DE CERTIFICACAO DIGITAL LTDA';
        $settings->issuer->cnpj = 11508222000136;
        $settings->issuer->imun = 2530360019;
        $settings->issuer->codMun = 3106200;

        //certificado digital
        $settings->certified->folder = __DIR__ . '/../storage/certificates/' . $settings->issuer->cnpj . '/';
        $settings->certified->certFile = 'certificate.pfx';
        $settings->certified->mixedKey = 'mixedKey.pem';
        $settings->certified->privateKey = 'privateKey.pem';
        $settings->certified->publicKey = 'publicKey.pem';
        $settings->certified->password = '215424958751';
        $settings->certified->noValidate = true;

        //dev
        if ($settings->environment == 'homologacao') {
            Utils::xdebugMode();
        }

        //efetua o boot no lib
        $system = new Boot($settings);
        $system->init();

    } catch (Exception $e) {
        dd($e);
        throw $e;
    }

## Executando os testes

Siga os casos de testes dentro da pasta "tests"

## Desdobramento, desenvolvimento

Adicione notas adicionais sobre como implantar isso em um sistema ativo

## Contribuindo

* [alefcarvalho] (https://gitlab.com/alefcarvalho/nfs-bh-legacy/tree/master) - A estrutura da web usada

## Autores

* ** Wander Alves ** - * Trabalho final * - [Linkedin] (https://www.linkedin.com/in/wander-alves-935b6314b)

## Licença

Este projeto está licenciado sob a licença MIT - consulte o arquivo [LICENSE.md] (LICENSE.md) para obter detalhes

## Agradecimentos

* Link Certificação Digital pelo apoio ao conceder tempo e recurso para a implementação da biblioteca.

  https://www.linkcertificacao.com.br/



