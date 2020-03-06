# Biblioteca para emissão de NFSE BH

API de emissão, consulta e cancelamento de notas fiscais de serviço via webservice da prefeitura de Belo Horizonte - MG


### Pré-requisitos

O que você precisa para instalar o software e como instalá-lo

Mínimo ```PHP 7.2```

### Instalando

Efetue download utilizando composer

```composer require wsilva94/nfse-bh-sdk```

## Começando

* Siga os passos abaixo para consumir corretamente a biblioteca

1 - Adicione seu certificado digital .PFX no seguinte diretório

 ```storage/certificates/{{56142462000106}}/certificate.pfx```

 "56142462000106" é o CNPJ da empresa.

2 - Sempre que desejar usar algum ENDPOINT implementado, passe sempre os parâmetros de configuração.

 ```tests/systemSettings```

```php
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
        $settings->certificate->folder = __DIR__ . '/../storage/certificates/' . $settings->issuer->cnpj . '/';
        $settings->certificate->certFile = 'certificate.pfx';
        $settings->certificate->mixedKey = 'mixedKey.pem';
        $settings->certificate->privateKey = 'privateKey.pem';
        $settings->certificate->publicKey = 'publicKey.pem';
        $settings->certificate->password = '215424958751';
        $settings->certificate->noValidate = true;

        //dev
        if ($settings->environment == 'homologacao') {
            Utils::xdebugMode();
        }

        //efetua o boot no lib
        $system = new Boot($settings);
        $system->init();

    } catch (Exception $e) {
        throw $e;
    }
```

## Execução dos testes

Siga os casos de testes dentro da pasta "tests"

## Autor

* **Wander Alves** - [Linkedin](https://www.linkedin.com/in/wander-alves-935b6314b)

## Contribuintes

* **Alef Carvalho**  - [GitLab](https://gitlab.com/alefcarvalho)
* **José Francisco**  - [GitHub](https://github.com/josefcts)

## Licença

Este projeto está licenciado sob a licença MIT - consulte o arquivo [LICENSE.md] (LICENSE.md) para obter detalhes

## Agradecimentos

* [Link Certificação Digital](https://www.linkcertificacao.com.br/) pelo apoio ao me conceder tempo e recursos para a implementação desta biblioteca.




