<?php namespace Nfse\Provider;

interface ISettings
{
    public function startSettings(Settings $thisClassInstance);
    public function setEnvironment(string $environment);
    public function setStorage(string $directory);
    public function xdebugMode(bool $enable);
    public function setIssuerName(string $name);
    public function setIssuerCnpj(string $cnpj);
    public function setIssuerImun(int $imun);
    public function setIssuerCodMun(int $codMun);
    public function setcertificateDirName(string $certificateDirName);
    public function setNameCertificateFile(string $certificateFileName);
    public function setCertificateMixedKey(string $mixedKey);
    public function setCertificatePrivateKey(string $privateKey);
    public function setCertificatPublicKey(string $publicKey);
    public function setCertificatPassword(string $password);
    public function setCertificatNoValidate(string $noValidate);
    public function getEnvironment();
    public function getStorage();
    public function getIssuerName();
    public function getIssuerCnpj();
    public function getIssuerImun();
    public function getIssuerCodMun();
    public function getCertificateDirName();
    public function getNameCertificateFile();
    public function getCertificateMixedKey();
    public function getCertificatePrivateKey();
    public function getCertificatPublicKey();
    public function getCertificatPassword();
    public function getCertificatNoValidate();
}
