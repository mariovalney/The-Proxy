<?php

/* 
 * Página Importar of theme "TheProxy"
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

set_meta(['title' => __('The PROXY - Sistema de Teste de Pacotes!')]);
include_header();
?>

<section class="list">
    <div class="row">
        <div class="col s12 section-title">
            <h3 class="left">Testar Pacotes</h3>
        </div>
        <div class="col s12">
            <?php if ( !isset($_POST['imported']) ): ?>

            <div class="row">
                <p class="flow-text">Você pode fazer upload de um pacote para testar se ele passará pelas regras do nosso Proxy. <br> Caso tenha alguma dúvida de como formatar esse arquivo, você pode dar uma olhada no exemplo <a href="<?php echo BASE_URL . 'pacote.sample.txt' ?>" target="_blank">clicando aqui</a></p> 
            </div>

            <form id="importfile" class="row center" action="" method="POST" enctype="multipart/form-data">
                <span class="waves-effect waves-light btn btn-import-trigger">
                    <i class="material-icons left">add</i>Fazer upload do arquivo
                </span>
                <input type="file" id="file" name="file" form="importfile">
                <input type="hidden" name="imported" value="imported" form="importfile">
            </form>

            <?php else : 

                $stream = array();
                $error = FALSE;
                $msg = 'Nenhum registro foi importado. Verifique se seu arquivo contém algum registro ou se está formatado corretamente.';

                if ( isset($_FILES['file']) ) {
                    $file = $_FILES['file'];
                    if ($file['error'] != 0) {
                        $error = TRUE;
                        $msg = 'Houve um erro ao ler o arquivo, por favor, tente novamente.';
                    } else {
                        $stream = file( $file['tmp_name'] );
                    }
                } else {
                    $error = TRUE;
                    $msg = 'Houve um erro no envio do arquivo, por favor, tente novamente.';
                }
                
                $registries = array();
                $badLines = array();
                
                if ($stream[0] != (count($stream) - 2)  && !$error) {
                    $error = TRUE;
                    $msg = 'A primeira linha deve conter a quantidade de pacotes';
                }

                if ($stream[(count($stream) - 1)] != 'EOF' && !$error) {
                    $error = TRUE;
                    $msg = 'A última linha deve sinalizar o fim do arquivo com "EOF"';
                }

                if (!$error) :
                    foreach ($stream as $key => $line) {
                        if ($key != 0 && $key != count($stream) - 1) {
                            $lineErrors = array();
                            $line = explode(',', $line);

                            if (count($line) == 6) {

                                $line[1] = trim($line[1]);
                                $line[2] = trim($line[2]);
                                $line[3] = trim($line[3]);
                                $line[4] = trim($line[4]);
                                $line[5] = trim($line[5], "\r\n");

                                if (checkIP($line[1])) {
                                    $registry["ip_origem"] = $line[1];
                                } else {
                                    $line[1] = '<span class="red-text">' . $line[1] . '</span>';
                                    array_push($lineErrors, "IP de Origem mal formatado.");
                                }

                                if (checkIP($line[2])) {
                                    $registry["ip_destino"] = $line[2];
                                } else {
                                    $line[2] = '<span class="red-text">' . $line[2] . '</span>';
                                    array_push($lineErrors, "IP de Destino mal formatado.");
                                }

                                if (checkProtocol($line[3])) {
                                    $registry["protocolo"] = $line[3];
                                } else {
                                    $line[3] = '<span class="red-text">' . $line[3] . '</span>';
                                    array_push($lineErrors, "Protocolo mal formatado.");
                                }

                                if (checkDoor($line[4])) {
                                    $registry["porta"] = $line[4];
                                } else {
                                    $line[4] = '<span class="red-text">' . $line[4] . '</span>';
                                    array_push($lineErrors, "Porta mal formatada.");
                                }

                                if (checkData($line[5])) {
                                    $registry["dados"] = $line[5];
                                } else {
                                    $line[5] = '<span class="red-text">' . $line[5] . '</span>';
                                    array_push($lineErrors, "String de Dados inválida.");
                                }

                                if (count($lineErrors) == 0) {
                                    array_push($registries, $registry);
                                } else {
                                    $line = '<td>' . implode(',', $line) . '</td><td>';
                                    
                                    foreach ($lineErrors as $lineError) {
                                        $line .= $lineError . '<br>';
                                    }
                                    
                                    $line .= '</td>';
                                    array_push($badLines, $line);
                                }
                            } else {
                                $line = '<td class="red-text">' . implode(',', $line) . '</td><td>Linha mal formatada</td>';
                                array_push($badLines, $line);
                            }
                        }
                    }
                
                    if (count($registries) > 0) : 
                        $toast = count($registries) . ' registro(s) testado(s) com sucesso.'; ?>
                        <p style="margin-top:0;font-size: 1.1em">Os registros abaixo foram testados:</p> 

                        <table class="striped responsive-table">
                            <thead>
                                <tr>
                                    <th>IP de Origem</th>
                                    <th>IP de Destino</th>
                                    <th>Protocolo</th>
                                    <th>Porta</th>
                                    <th>Dados</th>
                                    <th>RESULTADO</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    // Todas as regras
                                    $rules = $avdb->selectData($table, $columns);
                                    foreach ($rules as $key => $rule) {
                                        $newRules[$rule['prioridade']] = $rule;
                                    }
                                    ksort($newRules);
                                    $rules = $newRules;

                                    foreach ($registries as $registry) {
                                        $dados = $registry["dados"];
                                        $result = 'BLOQUEADO';

                                        $canFinish = false;
                                        foreach ($rules as $key => $rule) {
                                            if (!$canFinish) {
                                                if (($registry["protocolo"] == $rule["protocolo"]) || ($rule["protocolo"] == '*') ) {
                                                    if ($rule['direction'] == 'E') {
                                                        $ip_pass = checkIPinRange($registry["ip_origem"], $rule['ip_origem'], $rule['ip_destino']);
                                                    } else {
                                                        $ip_pass = checkIPinRange($registry["ip_destino"], $rule['ip_origem'], $rule['ip_destino']);
                                                    }

                                                    if ($ip_pass) {
                                                        $door_pass = checkDoorinRange($registry["porta"], $rule['porta_inicial'], $rule['porta_final']);
                                                    }

                                                    if ($door_pass) {
                                                        switch ($rule['action']) {
                                                            case 'P':
                                                                $result = 'AUTORIZADO <span class="tooltipped" style="cursor:pointer!important" data-position="left" data-delay="50" data-tooltip="' . $rule['nome'] . '"><i class="material-icons" style="position: relative; padding: 2px 0px 6px 4px; vertical-align: middle; font-size: 21px;">info</i></span>';
                                                                $canFinish = true;
                                                                break;
                                                            case 'B':
                                                                $result = 'BLOQUEADO <span class="tooltipped" style="cursor:pointer!important" data-position="left" data-delay="50" data-tooltip="' . $rule['nome'] . '"><i class="material-icons" style="position: relative; padding: 2px 0px 6px 4px; vertical-align: middle; font-size: 21px;">info</i></span>';
                                                                break;
                                                            default:
                                                                $result = 'ERROR';
                                                                break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        echo '<tr class="registry">';
                                        echo '<td class="registry-ip_origem" data-value="' . checkEmpty($registry["ip_origem"]) . '">' . checkEmpty($registry["ip_origem"]) . '</td>';
                                        echo '<td class="registry-ip_destino" data-value="' . checkEmpty($registry["ip_destino"]) . '">' . checkEmpty($registry["ip_destino"]) . '</td>';
                                        echo '<td class="registry-protocolo" data-value="' . checkEmpty($registry["protocolo"]) . '">' . checkEmpty($registry["protocolo"]) . '</td>';
                                        echo '<td class="registry-porta" data-value="' . checkEmpty($registry["porta"]) . '">' . checkEmpty($registry["porta"]) . '</td>';
                                        echo '<td class="registry-dados" data-value="' . checkEmpty($dados) . '">' . checkEmpty($dados) . '</td>';
                                        echo '<td class="registry-result">' . $result . '</td>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    
                    <?php endif; 
                    
                    if (count($badLines) > 0): 
                        $toast = 'Arquivo importado com sucesso, mas houveram erros.'; ?>
                    
                        <p style="margin-top:40px;font-size: 1.1em">As linhas abaixo continham algum erro e não puderam ser testadas:</p> 

                        <table class="striped responsive-table">
                            <thead>
                                <tr>
                                    <th>Linha</th>
                                    <th>Erro</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    foreach ($badLines as $line) {
                                        echo '<tr>' . $line . '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-darken-2 red-text flow-text"><?php echo $msg ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <a class="right" href="<?php echo BASE_URL . 'v1/' ?>" >Voltar</a>
            <?php if ((isset($error) && $error) || ((isset($badLines) && (count($badLines) > 0))) ) {
                echo '<span class="right" style="margin: 0 10px;"> | </span><a class="right" href="' . BASE_URL . 'testar/" >Tentar Novamente</a>';
            } ?>
        </div>
    </div>
</section>

<div id="addregistry" class="modal">
    <form id="addregistryform" method="post" action="">
        <div class="modal-content">
            <h4>Adicionar Registro</h4>
            <div class="row">
                <div class="input-field col s4">
                    <input id="ip_origem" name="ip_origem" type="text" class="validate mask-ip" autocomplete="off" required="required">
                    <label for="ip_origem">Origem</label>
                </div>
                <div class="input-field col s4">
                    <input id="ip_destino" name="ip_destino" type="text" class="validate mask-ip" autocomplete="off" required="required">
                    <label for="ip_destino">Destino</label>
                </div>
                <div class="input-field col s2">
                    <input id="protocolo" name="protocolo" type="text" class="validate" autocomplete="off" required="required">
                    <label for="protocolo">Protocolo</label>
                </div>
                <div class="input-field col s2">
                    <input id="porta" name="porta" type="text" class="validate" autocomplete="off" required="required">
                    <label for="porta">Porta</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="dados" name="dados" class="mask-dados" type="text" length="50" autocomplete="off" required="required">
                    <label for="dados">Dados</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="btn btn-large waves-effect waves-light btn-input">
                <input type="submit" name="send-registry" class="search-submit" form="addregistryform" value="Adicionar Registro">
            </span>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        </div>
    </form>
</div>

<div id="editregistry" class="modal">
    <form id="editregistryform" method="post" action="">
        <input id="ID" name="ID" type="hidden">
        <div class="modal-content">
            <h4>Alterar Registro (<span class="editing-id">ID</span>)</h4>
            <div class="row">
                <div class="input-field col s4">
                    <input id="ip_origem" name="ip_origem" type="text" class="validate mask-ip" autocomplete="off" required="required">
                    <label for="ip_origem">Origem</label>
                </div>
                <div class="input-field col s4">
                    <input id="ip_destino" name="ip_destino" type="text" class="validate mask-ip" autocomplete="off" required="required">
                    <label for="ip_destino">Destino</label>
                </div>
                <div class="input-field col s2">
                    <input id="protocolo" name="protocolo" type="text" class="validate" autocomplete="off" required="required">
                    <label for="protocolo">Protocolo</label>
                </div>
                <div class="input-field col s2">
                    <input id="porta" name="porta" type="text" class="validate" autocomplete="off" required="required">
                    <label for="porta">Porta</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="dados" name="dados" class="mask-dados" type="text" length="50" autocomplete="off" required="required">
                    <label for="dados">Dados</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="btn btn-large waves-effect waves-light btn-input">
                <input type="submit" name="send-registry" class="search-submit" form="editregistryform" value="Atualizar Registro">
            </span>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        </div>
    </form>
</div>

<div id="deleteregistry" class="modal">
    <div class="modal-content">
        <h4>Excluir Registro</h4>
        <p>Você tem certeza que deseja fazer isso? <strong>Não será possível desfazer essa ação.</strong></p>
    </div>
    <div class="modal-footer">
        <a href="#!" class="btn btn-large waves-effect waves-red red darken-4 btn-delete delete-link">Apagar</a>
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
    </div>
</div>

<?php include_footer();