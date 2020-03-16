<?php namespace NFse\Config;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use NFse\Config\WebService;
use NFse\Models\Settings;

class API
{
    private $webservice;
    private $settings;

    /**
     * construtor
     *
     * @param NFse\Models\Settings;
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->webservice = new WebService($this->settings);
    }

    /**
     * retorna um subdiretório de storage
     */
    public function getFolder($folder, $subfolder = false):string
    {
        try {
            if ($subfolder) {
                return $this->webservice->env . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $subfolder;
            } else {
                return $this->webservice->env . DIRECTORY_SEPARATOR . $folder;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * monta a arvore de diretórios em storage e estabelece permissões de acesso
     */
    public function checkFolders(): void
    {
        try {
            $adapter = new Local(__DIR__ . "/../../storage/{$this->webservice->env}");
            $filesystem = new Filesystem($adapter);

            //cria a pasta de ambiente
            if (!is_dir(__DIR__ . "/../../storage/{$this->webservice->env}")) {
                die("Não foi possivel criar o diretorio $filesystem. Verifique as permissões");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
