<?php namespace Nfse\Provider;

use Nfse\Config\Boot;
use Nfse\Helpers\Utils;
use Nfse\Models\Settings as mdlSettings;
use Nfse\Provider\ISettings;

class Settings implements ISettings
{
    private $settings;

    public function __construct()
    {
        $this->settings = new mdlSettings();
    }   

    public function startSettings(Settings $thisClassInstance)
    {
        $system = new Boot($thisClassInstance);
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
        $this->settings->issuer->cnpj = Utils::removerMaskCpfCnpj($cnpj);
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
    public function setcertificateDirName(string $certificateDirName)
    {
        $this->settings->certificate->certificateDirName = $certificateDirName;
    }

    /**
     * @param string $certFile
     */
    public function setNameCertificateFile(string $certificateFileName)
    {
        $this->settings->certificate->certificateFileName = $certificateFileName;
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

    public function getEnvironment()
    {
        return $this->settings->environment;
    }

    public function getStorage()
    {
        return $this->settings->storage;
    }

    public function getIssuerName()
    {
        return $this->settings->issuer->name;
    }

    public function getIssuerCnpj()
    {
        return $this->settings->issuer->cnpj;
    }

    public function getIssuerImun()
    {
        return $this->settings->issuer->imun;
    }

    public function getIssuerCodMun()
    {
        return $this->settings->issuer->codMun;
    }

    public function getCertificateDirName()
    {
        return $this->settings->certificate->certificateDirName;
    }

    public function getNameCertificateFile()
    {
        return $this->settings->certificate->certificateFileName;
    }

    public function getCertificateMixedKey()
    {
        return $this->settings->certificate->mixedKey;
    }

    public function getCertificatePrivateKey()
    {
        return $this->settings->certificate->privateKey;
    }

    public function getCertificatPublicKey()
    {
        return $this->settings->certificate->publicKey;
    }

    public function getCertificatPassword()
    {
        return $this->settings->certificate->password;
    }

    public function getCertificatNoValidate()
    {
        return $this->settings->certificate->noValidate;
    }
}
