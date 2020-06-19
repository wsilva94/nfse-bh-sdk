<?php

require_once __DIR__ . '/vendor/autoload.php';

use Exception;
use Service\Settings;

try {

    //As configurações sempre derá ser informado
    $settings = new Settings;
    $settings->setEnvironment('homologacao');
    $settings->setStorage(__DIR__ . '/../certificados/nfse/XML');
    $settings->xdebugMode(true);
    $settings->setIssuerName('LINK SERVICOS DE CERTIFICACAO DIGITAL LTDA');
    $settings->setIssuerCnpj(11508222000136);
    $settings->setIssuerImun(2530360019);
    $settings->setIssuerCodMun(3106200);
    $settings->setCertificateFolder(__DIR__ . '/../certificados/nfse/');
    $settings->setCertificateCertFile('certificate.pfx');
    $settings->setCertificateMixedKey('mixedKey.pem');
    $settings->setCertificatePrivateKey('privateKey.pem');
    $settings->setCertificatPublicKey('publicKey.pem');
    $settings->setCertificatPassword('11508222');
    $settings->setCertificatNoValidate(true);
    $settings->startSettings();

    

} catch (Exception $e) {
    dd($e);
}