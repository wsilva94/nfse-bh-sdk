<?php namespace Service;

use Nfse\Config\Boot;
use Nfse\Helpers\Utils;
use Nfse\Models\Settings as mdlSettings;
use Service\ISettings;

class Settings implements ISettings
{
    private $settings;

    public function __construct()
    {
        $this->settings = new mdlSettings();
    }

    public function startSettings()
    {
        $system = new Boot($this->settings);
        $system->init();

    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment)
    {
        $this->settings->environment = $environment;
    }

    /**
     * @param string $directory
     */
    public function setStorage(string $directory)
    {
        $this->settings->storage = $directory;
    }

    public function xdebugMode(bool $enable)
    {
        if ($enable) {
            if ($this->settings->environment == 'H') {
                Utils::xdebugMode();
            }
        }
    }

    /**
     * @param string $name
     */
    public function setIssuerName(string $name)
    {
        $this->settings->issuer->name = $name;
    }

    /**
     * @param string $cnpj
     */
    public function setIssuerCnpj(string $cnpj)
    {
        $this->settings->issuer->cnpj = $cnpj;
    }

    /**
     * @param string $imun
     */
    public function setIssuerImun(int $imun)
    {
        $this->settings->issuer->imun = $imun;
    }

    /**
     * @param string $codMun
     */
    public function setIssuerCodMun(int $codMun)
    {
        $this->settings->issuer->codMun = $codMun;
    }

    /**
     * @param string $dir
     */
    public function setCertificateFolder(string $dir)
    {
        $this->settings->certificate->folder = $dir;
    }

    /**
     * @param string $certFile
     */
    public function setCertificateCertFile(string $certFile)
    {
        $this->settings->certificate->certFile = $certFile;
    }

    /**
     * @param string $mixedKey
     */
    public function setCertificateMixedKey(string $mixedKey)
    {
        $this->settings->certificate->mixedKey = $mixedKey;
    }

    /**
     * @param string $privateKey
     */
    public function setCertificatePrivateKey(string $privateKey)
    {
        $this->settings->certificate->privateKey = $privateKey;
    }

    /**
     * @param string $publicKey
     */
    public function setCertificatPublicKey(string $publicKey)
    {
        $this->settings->certificate->publicKey = $publicKey;
    }

    /**
     * @param string $password
     */
    public function setCertificatPassword(string $password)
    {
        $this->settings->certificate->password = $password;
    }

    /**
     * @param string $noValidate
     */
    public function setCertificatNoValidate(string $noValidate)
    {
        $this->settings->certificate->noValidate = $noValidate;
    }

}
