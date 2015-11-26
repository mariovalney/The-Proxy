<?php

/* 
 * Página Download of theme "TheProxy"
 *
 * @verson: v1
 * @package TheProxy
 */

include ('inc/functions.php');

global $avdb;
global $toast;
global $aes;

/** The Database e Talz **/
$table = 'tp_regras';
$columns = array('ID', 'nome', 'prioridade', 'ip_origem', 'ip_destino', 'direction', 'protocolo', 'porta_inicial', 'porta_final', 'action', 'dados');
$fields = array('nome', 'prioridade', 'ip_origem', 'ip_destino', 'direction', 'protocolo', 'porta_inicial', 'porta_final', 'action', 'dados');

$ruleFileStream = '';

$rules = $avdb->selectData($table, $columns);
if (count($rules) > 0) {
    $ruleFileStream .= count($rules) . "\r\n";

    foreach ($rules as $rule) {
        $dados = unserialize($rule["dados"]);
        $dados = decode_forRule($dados);

        $ruleFileStream .= checkEmpty($rule["ID"]) . ',';
        $ruleFileStream .= checkEmpty($rule["nome"]) . ',';
        $ruleFileStream .= checkEmpty($rule["prioridade"]) . ',';
        $ruleFileStream .= checkEmpty($rule["ip_origem"]) . ',';
        $ruleFileStream .= checkEmpty($rule["ip_destino"]) . ',';
        $ruleFileStream .= checkEmpty($rule["direction"]) . ',';
        $ruleFileStream .= checkEmpty($rule["protocolo"]) . ',';
        $ruleFileStream .= checkEmpty($rule["porta_inicial"]) . ',';
        $ruleFileStream .= checkEmpty($rule["porta_final"]) . ',';
        $ruleFileStream .= checkEmpty($rule["action"]) . ',';
        $ruleFileStream .= checkEmpty($dados) . "\r\n";
        
    }

    $ruleFileStream .= "EOF";
} else {
    $ruleFileStream = 'Não há nenhum registro cadastrado.';
}

$filename = 'REGRAS2012207121.txt';
$handle = fopen($filename, "w");
fwrite($handle, $ruleFileStream);
fclose($handle);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($filename));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);
exit;