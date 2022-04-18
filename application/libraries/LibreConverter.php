<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LibreConverter{

    private $bin="soffice";
    private $outputDirectory="./output"; //Diretório de saída
    private $inputDirectory="./source";  //Diretório de entrada
    private $inputExtension="docx"; //Extensão de Arquivos para converter
    private $prefixExecWithExportHome=true;

    public function __construct($setup=[])
    {
        if($setup){
            $this->setup($setup);
        }
    }

    public function setup(array $setup)
    {
       //binary location
       if(isset($setup['bin']) ){
        $this->bin = $setup['bin'];
       }
       if(isset($setup['prefixExecWithExportHome']) ){
        $this->prefixExecWithExportHome = $setup['prefixExecWithExportHome'];
       }
       if(isset($setup['outputDirectory']) ){
        $this->outputDirectory = $this->tratarNameDir($setup['outputDirectory']);
       }
       if(isset($setup['inputDirectory']) ){
        $this->inputDirectory = $this->tratarNameDir($setup['inputDirectory']);
       }
       if(isset($setup['inputExtension']) ){
        $this->inputExtension =$this->tratarNameExt($setup['inputExtension']);
       }
              
    }

    private function tratarNameDir($nameDir)
    {
        return rtrim($nameDir," /");
    }

    private function tratarNameExt($nameExt)
    {
        return ltrim($nameExt," .");
    }

    public function convertFile($fileName)
    {
        //parâmetros necessários: Diretório de entrada, de saída e extensão do arquivo a converter
        
        $filename=pathinfo($fileName,PATHINFO_FILENAME); //nome sem extensão, caso usuário passe extensão
        $extension=pathinfo($fileName,PATHINFO_EXTENSION); //extensão do fileName, caso o usuário passe extensão
        if($extension){
            //Caso usuário passe extensão, utilizar essa como InputExtension; sobrescre o setup
            $this->inputExtension=$extension;
        }
       
        $fullPathFilename=$this->inputDirectory."/".$filename.".".$this->inputExtension;
        if(!file_exists($fullPathFilename)){
            throw new \Exception('Arquivo para Conversão não existe');
        }
        $cmd=$this->makeCommand($fullPathFilename,$this->outputDirectory);
        $retorno=$this->exec($cmd);
        return $retorno;
    }

    public function convertAll()
    {
       //parâmetros necessários: Diretório de entrada, de saída e extensão do arquivo a converter
        $sourceFile=escapeshellarg($this->inputDirectory)."/*.".$this->inputExtension;
        $cmd=$this->makeCommand($sourceFile,$this->outputDirectory,false);
        $retorno=$this->exec($cmd);
        return $retorno;
    }

    public function makeCommand($sourceFile, $outputDirectory,$escapeSource=true)
    {
        setlocale(LC_CTYPE, 'pt_BR.UTF-8'); //resolve problema de acento no escapeshell
        if (!is_writable($outputDirectory)) {
            throw new \Exception('Destino sem permissão de escrita');
        }
        $sourceFile = $escapeSource?escapeshellarg($sourceFile):$sourceFile;
        $outputDirectory = escapeshellarg($outputDirectory);
       

        return "{$this->bin} --headless --convert-to pdf {$sourceFile} --outdir {$outputDirectory}";
    }

    public function exec($cmd, $input = '')
    {
       if ($this->prefixExecWithExportHome) {
           //Bloco útil pois se estiver rodando no apache, o apache não tem HOME definido e o libreoffice precisa escrever no HOME
           //coisas como cache, config do usuário,etc.
            $home = getenv('HOME');
            $is_windows=stristr(PHP_OS, 'WIN')?true:false;
            $DS=DIRECTORY_SEPARATOR ;
            if (!is_writable($home)) {
                $cmdExport=$is_windows?"SET":"export"; //No Windows não tem comando 'export' e sim 'SET'
                $cmd = "$cmdExport HOME={$DS}tmp && ".$cmd;
            }
        }
        //echo $cmd ; exit();
        $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if (false === $process) {
            throw new \Exception('Erro ao abrir processo shell');
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($process);

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn,
        ];
    }
    /**
     * Retorna Lista de arquivos não convertidos.
     * Se basea no diretório de entrada e no diretório de saída.
     * Verifica se algum arquivo do diretório de entrada não está no diretório de saída
     * @return array $lista : Lista de arquivos não convertidos
     */
    public function getFilesNotConverted()
    {
        $lista=[];
        $filesSource=scandir($this->inputDirectory);
        foreach($filesSource as $fileSource):
            $fullPathFileSource=$this->inputDirectory."/".$fileSource;
            if(is_file($fullPathFileSource)){
                $filename=pathinfo($fullPathFileSource,PATHINFO_FILENAME);
                $outputPDF=$this->outputDirectory."/".$filename.".pdf";
                if(!file_exists($outputPDF)){
                    $lista[]=$fileSource;
                }
                
               
            }

        endforeach;
        return $lista;
    }

}