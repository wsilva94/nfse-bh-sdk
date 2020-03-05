<?php

namespace NFse\Config;

use Exception;
use NFse\Models\Settings;

class Certificate
{
    /**
     * Read certificate.
     *
     * @param NFse\Models\Settings;
     */
    public function load(Settings $settings): void
    {
        if (empty($settings->certificate->password)) {
            throw new Exception('A senha de acesso para o certificado pfx não pode ser vazia.', 400);
        }
        $this->checkCertificates($settings);
    }

    /**
     * Check filen on storage.
     *
     * @param NFse\Models\Settings;
     */
    private function checkCertificates(Settings $settings): bool
    {
        if (!file_exists($settings->certificate->folder . $settings->certificate->certFile)) {
            throw new Exception('Certificado ' . $settings->certificate->folder . $settings->certificate->certFile . ' não foi encontrado', 404);
        }

        return true;
    }
}
