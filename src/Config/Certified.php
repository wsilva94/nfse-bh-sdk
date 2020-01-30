<?php namespace NFse\Config;

use Exception;
use NFse\Models\Settings;

class Certified
{
    /**
     * lê os certificados
     *
     * @param NFse\Models\Settings;
     */
    public function load(Settings $settings): void
    {
        if (empty($settings->certified->password)) {
            throw new Exception("A senha de acesso para o certificado pfx não pode ser vazia.", 400);
        }
        $this->checkCertificates($settings);
    }

    /**
     * checka os arquivos em disco
     * @param NFse\Models\Settings;
     */
    private function checkCertificates(Settings $settings): bool
    {
        if (!file_exists($settings->certified->folder . $settings->certified->certFile)) {
            throw new Exception("Certificado " . $settings->certified->folder . $settings->certified->certFile . " não foi encontrado", 404);
        }

        return true;
    }
}
