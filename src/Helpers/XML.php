<?php namespace NFse\Helpers;

class XML
{

    private static $folder = "";
    private static $content = "";
    private static $instance = null;

    /**
     * static construtor
     */
    public static function load($file, $fromString = false): xml
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

    /**
     * magic to string
     */
    public function __toString(): string
    {
        return self::$content;
    }

    /**
     * seta uma tag
     */
    public function set($tag, $value): xml
    {
        self::$content = str_ireplace("{{" . $tag . "}}", $value, self::$content);
        return new self;
    }

    /**
     * filtra o conteúdo do arquivo
     */
    public static function filter()
    {
        self::$content = Utils::xmlFilter(self::$content);
        return new self;
    }

    /**
     * retorna o xml da tag
     */
    public function save(): string
    {
        return self::$content;
    }

    /**
     * seta a pasta de tags xml
     */
    public function setFolder($folder): xml
    {
        self::$folder = $folder;
        return $this;
    }

    /**
     * append string no xml
     */
    public function append($xml, $at): xml
    {
        $this->content = str_replace($at, $xml, $this->content);
        return $this;
    }
}
