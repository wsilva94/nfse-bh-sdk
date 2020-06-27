<?php namespace Nfse\Provider;

use Service\Settings;

class Batch
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function consult(string $protocol)
    {
        # code...
    }
}