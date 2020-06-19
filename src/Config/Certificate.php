<?php

namespace Nfse\Config;

use Exception;
use Nfse\Models\Settings;
use Symfony\Component\Filesystem\Filesystem;

class Certificate
{
    private $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @param Nfse\Models\Settings;
     */
    public function load(Settings $settings)
    {
        if (empty($settings->certificate->password)) {
            throw new Exception('A senha de acesso para o certificado pfx não pode ser vazia.', 400);
        }
        $this->checkCertificates($settings);
    }

    /**
     * @param Nfse\Models\Settings;
     * @return bool
     */
    private function checkCertificates(Settings $settings)
    {
        $issetCertificate = $this->filesystem->exists($settings->certificate->folder . $settings->certificate->certFile);
        if (!$issetCertificate) {
            throw new Exception('Certificado ' . $settings->certificate->folder . $settings->certificate->certFile . ' não foi encontrado', 404);
        }
        return true;
    }
}
