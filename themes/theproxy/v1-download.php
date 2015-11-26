<?php

/* 
 * Página Download of theme "TheProxy"
 *
 * @verson: v1
 * @package TheProxy
 */

include ('inc/functions.php');

global $avdb;
global $aes;

/** The Database e Talz **/
$table = 'tp_pacotes';
$columns = array('ID', 'ip_origem', 'ip_destino', 'protocolo', 'porta', 'dados');
$fields = array('ip_origem', 'ip_destino', 'protocolo', 'porta', 'dados');

$registryFileStream = '';

$registries = $avdb->selectData($table, $columns);
if (count($registries) > 0) {
    $registryFileStream .= count($registries) . "\r\n";

    foreach ($registries as $registry) {
        $dados = unserialize($registry["dados"]);
        $dados = decode($dados);

        $registryFileStream .= checkEmpty($registry["ID"]) . ',';
        $registryFileStream .= checkEmpty($registry["ip_origem"]) . ',';
        $registryFileStream .= checkEmpty($registry["ip_destino"]) . ',';
        $registryFileStream .= checkEmpty($registry["protocolo"]) . ',';
        $registryFileStream .= checkEmpty($registry["porta"]) . ',';
        $registryFileStream .= checkEmpty($dados) . "\r\n";
    }

    $registryFileStream .= "EOF";
} else {
    $registryFileStream = 'Não há nenhum registro cadastrado.';
}

$filename = 'PACOTE2012207121.txt';
$handle = fopen($filename, "w");
fwrite($handle, $registryFileStream);
fclose($handle);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($filename));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);
exit;