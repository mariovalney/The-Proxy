<?php

/* 
 * Index of theme "AvantDoc"
 * 
 * @package Documentation
 */

include ('inc/functions.php');

global $avdb;
global $toast;
global $aes;

/** The Database e Talz **/
$table = 'tp_pacotes';
$columns = array('ID', 'ip_origem', 'ip_destino', 'protocolo', 'porta', 'dados');
$fields = array('ip_origem', 'ip_destino', 'protocolo', 'porta', 'dados');

$registryFileStream = '';

set_meta(['title' => __('The PROXY - Sistema de Gerenciamento de Pacotes!')]);
include_header();
?>

<section class="list">
    <div class="row">
        <div class="col s12 section-title">
            <h3 class="left">Tabela de Criptografia</h3>
        </div>
        <div class="col s12">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dados Criptografados</th>
                        <th>Dados Decriptografados</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        /** Selecting $columns from $table **/
                        $registries = $avdb->selectData($table, $columns);
                        if (count($registries) > 0) {
                            foreach ($registries as $registry) {
                                $dadosRaw = unserialize($registry["dados"]);
                                $dados = decode($dadosRaw);
                                $dadosRaw = implode('<br>', $dadosRaw);

                                echo '<tr class="registry">';
                                echo '<td class="registry-ID" data-value="' . checkEmpty($registry["ID"]) . '">' . checkEmpty($registry["ID"]) . '</td>';
                                echo '<td class="registry-dados">' . checkEmpty($dadosRaw) . '</td>';
                                echo '<td class="registry-dados">' . checkEmpty($dados) . '</td>';
                                echo '</tr>';

                                $registryFileStream .= checkEmpty($registry["ID"]) . ',';
                                $registryFileStream .= checkEmpty($registry["ip_origem"]) . ',';
                                $registryFileStream .= checkEmpty($registry["ip_destino"]) . ',';
                                $registryFileStream .= checkEmpty($registry["protocolo"]) . ',';
                                $registryFileStream .= checkEmpty($registry["porta"]) . ',';
                                $registryFileStream .= checkEmpty($registry["dados"]) . '\n';
                            }
                        } else {
                            echo '<tr><td class="center" colspan="7">Não há nenhum registro cadastrado.</td><tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <a class="right" href="<?php echo BASE_URL ?>">Voltar</a>
        </div>
    </div>
</section>

<?php include_footer();