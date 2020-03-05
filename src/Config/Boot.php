<?php namespace NFse\Config;

use Exception;
use NFse\Config\API;
use NFse\Config\Certificate;
use NFse\Models\Settings;
use NFse\Signature\Subscriber;

class Boot
{

    private $api, $cert, $subscriber, $settings;

    /**
     * construtor que verifica os parâmetros básicos para funcionamento da lib
     *
     * @param NFse\Models\Settings;
     */
    public function __construct(Settings $settings)
    {
        try {
            $this->settings = $settings;

            $this->api = new API($this->settings);
            $this->cert = new Certificate($this->settings);
            $this->subscriber = new Subscriber($this->settings);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Função que inicia o sistema
     */
    public function init(): bool
    {
        try {
            $this->api->checkFolders();
            $this->cert->load($this->settings);
            $this->subscriber->loadPFX($this->settings);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
