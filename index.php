<?php

require_once __DIR__ . '/vendor/autoload.php';

use Exception;
use Nfse\Service\Batch;
use Nfse\Provider\Settings;

try {

    //Settings
    $settings = new Settings;
    $settings->setEnvironment('homologacao');
    $settings->xdebugMode(true);
    //Data Company
    $settings->setStorage(__DIR__ . '/../certificados/nfse/XML');
    $settings->setIssuerName('LINK SERVICOS DE CERTIFICACAO DIGITAL LTDA');
    $settings->setIssuerCnpj(11508222000136);
    $settings->setIssuerImun(2530360019);
    $settings->setIssuerCodMun(3106200);
    //Data Certficate
    $settings->setcertificateDirName(__DIR__ . '/../certificados/nfse/');
    $settings->setNameCertificateFile('certificate.pfx');
    $settings->setCertificateMixedKey('mixedKey.pem');
    $settings->setCertificatePrivateKey('privateKey.pem');
    $settings->setCertificatPublicKey('publicKey.pem');
    $settings->setCertificatPassword('11508222');
    $settings->setCertificatNoValidate(true);
    //Start
    $settings->startSettings($settings);

    //consulta lote
    $batch = new Batch($settings, 'AF0775697M20e0304bR5pJYe');
    $result = $batch->sendConsultation();

} catch (Exception $e) {
    dd($e);
}