<?php require_once __DIR__ . '/../vendor/autoload.php';

//inicia o sistema
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
