<?php namespace Service;

interface ISettings
{
    function startSettings();
    function setEnvironment(string $environment);
    function setIssuerName(string $name);
    function setIssuerCnpj(string $cnpj);
    function setIssuerImun(int $imun);
    function setIssuerCodMun(int $codMun);
    function setCertificateFolder(string $dir);
    function setCertificateCertFile(string $certFile);
    function setCertificateMixedKey(string $mixedKey);
    function setCertificatePrivateKey(string $privateKey);
    function setCertificatPublicKey(string $publicKey);
    function setCertificatPassword(string $password);
    function setCertificatNoValidate(string $noValidate);
    function setStorage(string $directory);
}
