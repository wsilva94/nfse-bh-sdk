<?php namespace Nfse\Models;

class Certificate
{
    /**
     *@var string diretorio onde se encontra os certificados
     */
    public $certificateDirName;

    /**
     *@var string nome do arquivo certificado .PFX
     */
    public $certificateFileName;

    /**
     *@var string nome da chave mesclada .PEM
     */
    public $mixedKey;

    /**
     *@var string nome da chave privada .PEM
     */
    public $privateKey;

    /**
     *@var string nome da chave publica .PEM
     */
    public $publicKey;

    /**
     *@var string senha do certificado digital
     */
    public $password;

    /**
     *@var bool parâmetro de validação do vencimento
     */
    public $noValidate;
}
