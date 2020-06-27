<?php namespace Nfse\Config;

use Exception;
use Nfse\Config\API;
use Nfse\Config\Certificate;
use Nfse\Provider\Settings;
use Nfse\Signature\Subscriber;

class Boot
{
    private $api;
    private $certificate;
    private $subscriber;
    private $settings;

    /**
     * @param Nfse\Provider\Settings;
     */
    public function __construct(Settings $settings)
    {
        try {
            $this->settings = $settings;
            $this->api = new API($this->settings);
            $this->certificate = new Certificate();
            $this->subscriber = new Subscriber($this->settings);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function init()
    {
        try {
            $directories = array(
                $this->settings->getStorage() . "/{$this->settings->getEnvironment()}",
                __DIR__ . "/../../Storage/{$this->settings->getEnvironment()}",
            );

            foreach ($directories as $directory) {
                $issetFolders = $this->api->checkFolders($directory);
                if (!$issetFolders) {
                    $this->api->makeDirStorage($directory);
                }
            }

            $this->certificate->load($this->settings);
            $this->subscriber->loadPFX($this->settings);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
