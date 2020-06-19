<?php namespace Nfse\Helpers;

class XML
{
    private static $folder = "";
    private static $content = "";
    private static $instance = null;

    public static function load($file, $fromString = false)
    {
        if ($fromString) {
            self::$content = $file;
        } else {
            self::$folder = __DIR__ . '/../../storage/' . "cdn" . DIRECTORY_SEPARATOR . "xml" . DIRECTORY_SEPARATOR;
            self::$content = file_get_contents(self::$folder . $file . '.xml');
        }

        self::$instance = (self::$instance === null) ? new self : self::$instance;

        return self::$instance;
    }

    public function __toString(): string
    {
        return self::$content;
    }

    public function set($tag, $value)
    {
        self::$content = str_ireplace("{{" . $tag . "}}", $value, self::$content);
        return new self;
    }

    public static function filter()
    {
        self::$content = Utils::xmlFilter(self::$content);
        return new self;
    }

    public function save(): string
    {
        return self::$content;
    }

    public function setFolder($folder)
    {
        self::$folder = $folder;
        return $this;
    }

    public function append($xml, $at)
    {
        $this->content = str_replace($at, $xml, $this->content);
        return $this;
    }
}
