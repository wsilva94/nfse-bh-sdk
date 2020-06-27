<?php

namespace Nfse\Config;

use Exception;
use Nfse\Provider\Settings;
use Symfony\Component\Filesystem\Filesystem;

class Certificate
{
    private $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @param Nfse\Provider\Settings;
     */
    public function load(Settings $settings)
    {
        if (empty($settings->getCertificatPassword())) {
            throw new Exception('A senha de acesso para o certificado pfx não pode ser vazia.', 400);
        }
        $this->checkCertificates($settings);
    }

    /**
     * @param Nfse\Provider\Settings;
     * @return bool
     */
    private function checkCertificates(Settings $settings)
    {
        $issetCertificate = $this->filesystem->exists($settings->getCertificateDirName() . $settings->getNameCertificateFile());
        if (!$issetCertificate) {
            throw new Exception('Certificado ' . $settings->getCertificateDirName() . $settings->getNameCertificateFile() . ' não foi encontrado', 404);
        }
        return true;
    }
}
