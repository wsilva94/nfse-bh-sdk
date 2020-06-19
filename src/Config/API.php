<?php namespace Nfse\Config;

use Exception;
use Nfse\Config\WebService;
use Nfse\Models\Settings;
use Symfony\Component\Filesystem\Filesystem;

class API
{
    private $webservice;
    private $settings;
    private $filesystem;

    /**
     * @param Nfse\Models\Settings;
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->webservice = new WebService($this->settings);
        $this->filesystem = new Filesystem();
    }

    /**
     * retorna um subdiretório de storage
     */
    public function getFolder($folder, $subfolder = false): string
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
     * @return bool;
     */
    public function checkFolders(string $directory)
    {
        try {
            return $this->filesystem->exists($directory);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function makeDirStorage(string $directory)
    {
        try {
            $this->filesystem->mkdir($directory, 0770);
            if (!$this->checkFolders($directory)) {
                throw new Exception("Não foi possivel criar o pasta no seguinte diretório " . $directory . ".Verifique as permissões", 400);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
