<?php namespace Nfse\Signature;

// Classe para tratamento e uso dos certificados digitais modelo A1 (PKCS12)

use Nfse\Provider\Settings;

class Pkcs12
{
    //id do documento sendo assinado
    public $docId = '';

    //diretório de certificados
    private $folderCerts = null;

    //arquivo PFX do certificado
    private $pfxCertFileName = null;
    private $pfxCertContents = null;

    //propriedades da chave pública
    private $pbCertFileName = null;
    private $pbCertContents = null;

    //propriedades da chave privada
    private $pvCertFileName = null;
    private $pvCertContents = null;

    //propriedades da chave mista
    private $mxCertFileName = null;
    private $mxCertContents = null;

    //timestamp da validade do certificado
    private $expireTimestamp = 0;
    private $error = '';

    //objetos de configuração
    private $settings;

    /**
     * Método de construção da classe.
     *
     * @param Nfse\Provider\Settings;
     */
    public function __construct(Settings $settings)
    {
        //inicializa as classes de configuração
        $this->settings = $settings;
        //recupera o nome dos arquivos de chave pública e privada
        $this->pbCertFileName = $this->settings->getCertificatPublicKey();
        $this->pvCertFileName = $this->settings->getCertificatePrivateKey();
        $this->mxCertFileName = $this->settings->getCertificateMixedKey();
        $this->folderCerts = $this->settings->getCertificateDirName();
        $this->pfxCertFileName = $this->settings->getNameCertificateFile();
    }

    //retorna o error
    public function getError()
    {
        return $this->error;
    }

    // Método de verificação dos arquivos de certificado
    public function loadPFX()
    {
        //faz um append de uma '/' no caminho da pasta de certificados
        if (substr($this->folderCerts, -1) !== \DIRECTORY_SEPARATOR) { //faz o append de uma '/' no caminho do arquivo caso precise
            $this->folderCerts .= \DIRECTORY_SEPARATOR;
        }

        //verifica se o PFX existe.
        if (!is_file($this->folderCerts . $this->pfxCertFileName)) {
            $this->error = 'O arquivo de certificado PFX não foi encontrado.';

            return false;
        }

        //verifica se as chaves existem. Caso não então gera
        $fullPathPublic = $this->folderCerts . $this->pbCertFileName;
        $fullPathPrivate = $this->folderCerts . $this->pvCertFileName;
        $fullPathMixed = $this->folderCerts . $this->mxCertFileName;

        //carrega o conteúdo do certificado PFX
        $this->pfxCertContents = file_get_contents($this->folderCerts . $this->pfxCertFileName);

        if (!is_file($fullPathPublic) || !is_file($fullPathPrivate) || !is_file($fullPathMixed)) {
            //carrega os certificados e chaves para um array denominado $x509certdata
            $x509certdata = [];
            if (!openssl_pkcs12_read($this->pfxCertContents, $x509certdata, $this->settings->getCertificatPassword())) {
                $this->error = 'O certificado pfx não pode ser lido. Senha errada ou arquivo corrompido ou formato inválido.';

                return false;
            }

            //checka o CNPJ do certificado
            $cnpjCert = Asn::getCNPJCert($x509certdata['cert']);
            if (substr($this->settings->getIssuerCnpj(), 0, 8) != substr($cnpjCert, 0, 8)) {
                $this->error = 'O Certificado fornecido pertence a outro CNPJ.';

                return false;
            }

            //remove todos os arquivos PEM antigos
            $this->removePEMFiles();

            //recria os arquivos pem com o arquivo pfx
            if (!file_put_contents($this->folderCerts . $this->pvCertFileName, $x509certdata['pkey'])) {
                $this->error = 'Falha de permissão de escrita na pasta dos certificados.';

                return false;
            }

            file_put_contents($this->folderCerts . $this->pbCertFileName, $x509certdata['cert']);
            file_put_contents($this->folderCerts . $this->mxCertFileName, $x509certdata['pkey'] . "\r\n" . $x509certdata['cert']);

            $this->pbCertContents = $x509certdata['cert'];
            $this->pvCertContents = $x509certdata['pkey'];
            $this->mxCertContents = $x509certdata['pkey'] . "\r\n" . $x509certdata['cert'];
        } else {
            //carrega o conteúdo dos arquivos
            $this->mxCertContents = file_get_contents($this->folderCerts . $this->mxCertFileName);
            $this->pbCertContents = file_get_contents($this->folderCerts . $this->pbCertFileName);
            $this->pvCertContents = file_get_contents($this->folderCerts . $this->pvCertFileName);
        }

        //retorna a validação do vencimento ou um bypass
        if (!$this->settings->getCertificatNoValidate()) {
            return true;
        }

        return $this->checkValidity();
    }

    //assina uma tag em um documento XML
    public function signXML($docxml, $tagid = '', $fromFile = false)
    {
        //caso não seja informada a tag a ser assinada cai fora
        if (empty($tagid)) {
            $this->error = ('A tag a ser assinada deve ser indicada.');

            return false;
        }

        //carrega a chave privada no openssl
        $objSSLPriKey = openssl_get_privatekey($this->pvCertContents, null);
        if ($objSSLPriKey === false) {
            $this->error = $this->getOpenSSLError('Houve erro no carregamento da chave privada.');

            return false;
        }

        $xml = $docxml;
        if ($fromFile == true && is_file($docxml)) {
            $xml = file_get_contents($docxml);
        }

        //remove sujeiras do xml
        $order = ["\r\n", "\n", "\r", "\t"];
        $xml = str_replace($order, '', $xml);

        $xmldoc = new Dom();
        $xmldoc->loadXMLString($xml);

        //coloca o node raiz em uma variável
        $root = $xmldoc->documentElement;

        //extrair a tag com os dados a serem assinados
        $node = $xmldoc->getElementsByTagName($tagid)->item(0);
        if (!isset($node)) {
            $this->error = "A tag < $tagid > não existe no arquivo XML.";

            return false;
        }

        $this->docId = $node->getAttribute('Id');
        $xmlResp = $xml;
        $xmlResp = $this->zSignXML($xmldoc, $root, $node, $objSSLPriKey);

        //libera a chave privada
        openssl_free_key($objSSLPriKey);

        return $xmlResp;
    }

    //verifica a validade da assinatura digital contida no xml
    public function verifySignature($docxml = '', $tagid = '')
    {
        if ($docxml == '') {
            $this->error = 'Não foi passado um xml para a verificação.';

            return false;
        }
        if ($tagid == '') {
            $this->error = 'Não foi indicada a TAG a ser verificada.';

            return false;
        }
        $xml = $docxml;
        if (is_file($docxml)) {
            $xml = file_get_contents($docxml);
        }
        $dom = new Dom();
        $dom->loadXMLString($xml);
        $flag = $this->zDigCheck($dom, $tagid);
        $flag = $this->zSignCheck($dom);

        return $flag;
    }

    //verifica a data de validade do certificado digital e compara com a data de hoje.
    //Caso o certificado tenha expirado o mesmo será removido das pastas e o método irá retornar false.
    protected function checkValidity()
    {
        if (!$data = openssl_x509_read($this->pbCertContents)) {
            $this->removePEMFiles();
            $this->error = 'A chave pública do certificado está corrompida.';

            return false;
        }

        $certData = openssl_x509_parse($data);
        // reformata a data de validade;
        $ano = substr($certData['validTo'], 0, 2);
        $mes = substr($certData['validTo'], 2, 2);
        $dia = substr($certData['validTo'], 4, 2);
        //obtem o timestamp da data de validade do certificado
        $dValid = gmmktime(0, 0, 0, $mes, $dia, $ano);
        // obtem o timestamp da data de hoje
        $dHoje = gmmktime(0, 0, 0, date('m'), date('d'), date('Y'));
        // compara a data de validade com a data atual
        $this->expireTimestamp = $dValid;
        if ($dHoje > $dValid) {
            $this->removePEMFiles();
            $this->error = "O certificado digital venceu em {$dia}/{$mes}/{$ano}";

            return false;
        }

        return true;
    }

    //Remove a informação de inicio e fim do certificado contido no formato PEM, deixando o certificado (chave publica) pronta para ser anexada ao xml da NFe
    protected function zCleanPubKey()
    {
        //inicializa variavel
        $data = '';
        //carregar a chave publica
        $pubKey = $this->pbCertContents;
        //carrega o certificado em um array usando o LF como referencia
        $arCert = explode("\n", $pubKey);
        foreach ($arCert as $curData) {
            //remove a tag de inicio e fim do certificado
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0
                && strncmp($curData, '-----END CERTIFICATE', 20) != 0
            ) {
                //carrega o resultado numa string
                $data .= trim($curData);
            }
        }

        return $data;
    }

    //Divide a string do certificado publico em linhas com 76 caracteres (padrão original)
    protected function zSplitLines($cntIn = '')
    {
        if ($cntIn != '') {
            $cnt = rtrim(chunk_split(str_replace(["\r", "\n"], '', $cntIn), 76, "\n"));
        } else {
            $cnt = $cntIn;
        }

        return $cnt;
    }

    //getOpenSSLError
    protected function getOpenSSLError($msg = '')
    {
        while ($erro = openssl_error_string()) {
            $msg .= $erro . "\n";
        }

        return $msg;
    }

    //Apaga os arquivos PEM do diretório isso deve ser feito quando um novo certificado é carregado
    //ou quando a validade do certificado expirou.
    private function removePEMFiles()
    {
        //chave pública
        if (is_file($this->folderCerts . $this->pbCertFileName)) {
            unlink($this->folderCerts . $this->pbCertFileName);
        }
        //chave privada
        if (is_file($this->folderCerts . $this->pvCertFileName)) {
            unlink($this->folderCerts . $this->pvCertFileName);
        }
        //chave mista
        if (is_file($this->folderCerts . $this->mxCertFileName)) {
            unlink($this->folderCerts . $this->mxCertFileName);
        }
    }

    //Método que provê a assinatura do xml conforme padrão SEFAZ
    private function zSignXML($xmldoc, $root, \DOMElement $node, $objSSLPriKey)
    {
        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsTransformMethod1 = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';

        //pega o atributo id do node a ser assinado
        $idSigned = trim($node->getAttribute('Id'));

        //extrai os dados da tag para uma string na forma canonica
        $dados = $node->C14N(true, false, null, null);

        //calcular o hash dos dados
        $hashValue = hash('sha1', $dados, true);
        //converter o hash para base64
        $digValue = base64_encode($hashValue);
        //cria o node <Signature>
        $signatureNode = $xmldoc->createElementNS($nsDSIG, 'Signature');
        //adiciona a tag <Signature> ao node raiz
        $root->appendChild($signatureNode);

        //cria o node <SignedInfo>
        $signedInfoNode = $xmldoc->createElement('SignedInfo');
        //adiciona o node <SignedInfo> ao <Signature>
        $signatureNode->appendChild($signedInfoNode);
        //cria no node com o método de canonização dos dados
        $canonicalNode = $xmldoc->createElement('CanonicalizationMethod');
        //adiona o <CanonicalizationMethod> ao node <SignedInfo>
        $signedInfoNode->appendChild($canonicalNode);
        //seta o atributo ao node <CanonicalizationMethod>
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        //cria o node <SignatureMethod>
        $signatureMethodNode = $xmldoc->createElement('SignatureMethod');
        //adiciona o node <SignatureMethod> ao node <SignedInfo>
        $signedInfoNode->appendChild($signatureMethodNode);
        //seta o atributo Algorithm ao node <SignatureMethod>
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        //cria o node <Reference>
        $referenceNode = $xmldoc->createElement('Reference');
        //adiciona o node <Reference> ao node <SignedInfo>
        $signedInfoNode->appendChild($referenceNode);
        //seta o atributo URI a node <Reference>
        $referenceNode->setAttribute('URI', '#' . $idSigned);
        //cria o node <Transforms>
        $transformsNode = $xmldoc->createElement('Transforms');
        //adiciona o node <Transforms> ao node <Reference>
        $referenceNode->appendChild($transformsNode);
        //cria o primeiro node <Transform> OBS: no singular
        $transfNode1 = $xmldoc->createElement('Transform');
        //adiciona o primeiro node <Transform> ao node <Transforms>
        $transformsNode->appendChild($transfNode1);
        //set o atributo Algorithm ao primeiro node <Transform>
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        //cria outro node <Transform> OBS: no singular
        $transfNode2 = $xmldoc->createElement('Transform');
        //adiciona o segundo node <Transform> ao node <Transforms>
        $transformsNode->appendChild($transfNode2);
        //set o atributo Algorithm ao segundo node <Transform>
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        //cria o node <DigestMethod>
        $digestMethodNode = $xmldoc->createElement('DigestMethod');
        //adiciona o node <DigestMethod> ao node <Reference>
        $referenceNode->appendChild($digestMethodNode);
        //seta o atributo Algorithm ao node <DigestMethod>
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        //cria o node <DigestValue>
        $digestValueNode = $xmldoc->createElement('DigestValue', $digValue);
        //adiciona o node <DigestValue> ao node <Reference>
        $referenceNode->appendChild($digestValueNode);
        //extrai node <SignedInfo> para uma string na sua forma canonica
        $cnSignedInfoNode = $signedInfoNode->C14N(true, false, null, null);
        //cria uma variavel vazia que receberá a assinatura
        $signature = '';
        //calcula a assinatura do node canonizado <SignedInfo>
        //usando a chave privada em formato PEM
        if (!openssl_sign($cnSignedInfoNode, $signature, $objSSLPriKey)) {
            $this->error = ($this->getOpenSSLError("Houve erro durante a assinatura digital.\n"));

            return false;
        }
        //converte a assinatura em base64
        $signatureValue = base64_encode($signature);
        //cria o node <SignatureValue>
        $signatureValueNode = $xmldoc->createElement('SignatureValue', $signatureValue);
        //adiciona o node <SignatureValue> ao node <Signature>
        $signatureNode->appendChild($signatureValueNode);
        //cria o node <KeyInfo>
        $keyInfoNode = $xmldoc->createElement('KeyInfo');
        //adiciona o node <KeyInfo> ao node <Signature>
        $signatureNode->appendChild($keyInfoNode);
        //cria o node <X509Data>
        $x509DataNode = $xmldoc->createElement('X509Data');
        //adiciona o node <X509Data> ao node <KeyInfo>
        $keyInfoNode->appendChild($x509DataNode);
        //remove linhas desnecessárias do certificado
        $pubKeyClean = $this->zCleanPubKey();
        //cria o node <X509Certificate>
        $x509CertificateNode = $xmldoc->createElement('X509Certificate', $pubKeyClean);
        //adiciona o node <X509Certificate> ao node <X509Data>
        $x509DataNode->appendChild($x509CertificateNode);
        //salva o xml completo em uma string
        $xmlResp = $xmldoc->saveXML();
        //retorna o documento assinado
        return $xmlResp;
    }

    //Check se o xml possi a tag Signature
    private function zSignatureExists($dom)
    {
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        if (!isset($signature)) {
            return false;
        }

        return true;
    }

    //zSignCheck
    private function zSignCheck($dom)
    {
        // Obter e remontar a chave publica do xml
        $x509Certificate = $dom->getNodeValue('X509Certificate');
        $x509Certificate = "-----BEGIN CERTIFICATE-----\n" .
        $this->zSplitLines($x509Certificate) .
            "\n-----END CERTIFICATE-----\n";

        //carregar a chave publica remontada
        $objSSLPubKey = openssl_pkey_get_public($x509Certificate);
        if ($objSSLPubKey === false) {
            $this->error = $this->getOpenSSLError('Ocorreram problemas ao carregar a chave pública. Certificado incorreto ou corrompido!!');

            return false;
        }

        //remontando conteudo que foi assinado
        $signContent = $dom->getElementsByTagName('SignedInfo')->item(0)->C14N(true, false, null, null);

        // validando assinatura do conteudo
        $signatureValueXML = $dom->getElementsByTagName('SignatureValue')->item(0)->nodeValue;
        $decodedSignature = base64_decode(str_replace(["\r", "\n"], '', $signatureValueXML));
        $resp = openssl_verify($signContent, $decodedSignature, $objSSLPubKey);
        if ($resp != 1) {
            $this->error = $this->getOpenSSLError("Problema ({$resp}) ao verificar a assinatura do digital!!");

            return false;
        }

        return true;
    }

    //zDigCheck
    private function zDigCheck($dom, $tagid = '')
    {
        $node = $dom->getNode($tagid, 0);
        if (empty($node)) {
            $this->error = "A tag < $tagid > não existe no XML!!";

            return false;
        }

        if (!$this->zSignatureExists($dom)) {
            $this->error = 'O xml não contêm nenhuma assinatura para ser verificada.';

            return false;
        }

        //carregar o node em sua forma canonica
        $tagInf = $node->C14N(true, false, null, null);
        //calcular o hash sha1
        $hashValue = hash('sha1', $tagInf, true);
        //converter o hash para base64 para obter o digest do node
        $digestCalculado = base64_encode($hashValue);
        //pegar o digest informado no xml
        $digestInformado = $dom->getNodeValue('DigestValue');
        //compara os digests calculados e informados
        if ($digestCalculado != $digestInformado) {
            $this->error = "O conteúdo do XML não confere com o Digest Value.\n
                Digest calculado [{$digestCalculado}], digest informado no XML [{$digestInformado}].\n
                O arquivo pode estar corrompido ou ter sido adulterado.";

            return false;
        }

        return true;
    }
}
