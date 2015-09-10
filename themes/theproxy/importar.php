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

/** Start Page **/

if (isset($_GET['delete'])) {
    if (is_numeric($_GET['delete'])) {
        $result = $avdb->deleteData($table, ['ID' => $_GET['delete']]);
        $avdb->refreshIdExample($table);
    }
} else {

    if (isset($_POST['send-registry'])) {
        $ip_origem = $_POST['ip_origem'];
        $ip_destino = $_POST['ip_destino'];
        $protocolo = $_POST['protocolo'];
        $porta = $_POST['porta'];
        $dados = $_POST['dados'];
        
        $dados = serialize(encode($dados));

        if ( !empty($ip_origem) && !empty($ip_destino) &&  !empty($protocolo) &&  !empty($porta) &&  !empty($dados) ) {

            if (isset($_POST['ID'])) {
                $id = $_POST['ID'];

                $values = array($ip_origem, $ip_destino, $protocolo, $porta, $dados);
                $avdb->updateData($table, $fields, $values, 'ID', $id);
                $toast = 'Registro alterado com sucesso.';
            } else {
                $values = array($ip_origem, $ip_destino, $protocolo, $porta, $dados);
                $avdb->insertData($table, $fields, $values);
                $toast = 'Registro adicionado com sucesso.';
            }
        }
    }

}
set_meta(['title' => __('The PROXY - Sistema de Gerenciamento de Pacotes!')]);
include_header();
?>

<section class="list">
    <div class="row">
        <div class="col s12 section-title">
            <h3 class="left">Importar Registros</h3>
        </div>
        <div class="col s12">
            <?php if ( !isset($_POST['imported']) ): ?>

            <div class="row">
                <p class="flow-text">Você pode adicionar registros importando-os através de um arquivo de texto. Caso tenha alguma dúvida de como formatar esse arquivo, você pode dar uma olhada no arquivo de exemplo <a href="<?php echo BASE_URL . 'pacote.sample.txt' ?>" target="_blank">clicando aqui</a>.<span class="black-text" style="display:block; font-size:65%;">Observação: os IDs dos registros serão ignorados em favor da contagem atual.</span></p> 
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
                
                foreach ($stream as $line) {
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
                
                if (!$error) :                 
                    if (count($registries) > 0) : 
                        $toast = count($registry) . ' registro(s) importado(s) com sucesso.'; ?>
                        <p style="margin-top:0;font-size: 1.1em">Os registros abaixo foram importados:</p> 

                        <table class="striped responsive-table">
                            <thead>
                                <tr>
                                    <th>IP de Origem</th>
                                    <th>IP de Destino</th>
                                    <th>Protocolo</th>
                                    <th>Porta</th>
                                    <th>Dados</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    foreach ($registries as $registry) {
                                        $dados = $registry["dados"];

                                        echo '<tr class="registry">';
                                        echo '<td class="registry-ip_origem" data-value="' . checkEmpty($registry["ip_origem"]) . '">' . checkEmpty($registry["ip_origem"]) . '</td>';
                                        echo '<td class="registry-ip_destino" data-value="' . checkEmpty($registry["ip_destino"]) . '">' . checkEmpty($registry["ip_destino"]) . '</td>';
                                        echo '<td class="registry-protocolo" data-value="' . checkEmpty($registry["protocolo"]) . '">' . checkEmpty($registry["protocolo"]) . '</td>';
                                        echo '<td class="registry-porta" data-value="' . checkEmpty($registry["porta"]) . '">' . checkEmpty($registry["porta"]) . '</td>';
                                        echo '<td class="registry-dados" data-value="' . checkEmpty($dados) . '">' . checkEmpty($dados) . '</td>';
                                        echo '</tr>';
                                        
                                        $dados = serialize(encode($dados));
                                        
                                        $values = array($registry["ip_origem"], $registry["ip_destino"], $registry["protocolo"], $registry["porta"], $dados);
                                        $avdb->insertData($table, $fields, $values);
                                    }
                                ?>
                            </tbody>
                        </table>
                    
                    <?php endif; 
                    
                    if (count($badLines) > 0): 
                        $toast = 'Arquivo importado com sucesso, mas houveram erros.'; ?>
                    
                        <p style="margin-top:40px;font-size: 1.1em">As linhas abaixo continham algum erro e não puderam ser importadas:</p> 

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
            <a class="right" href="<?php echo BASE_URL ?>" >Voltar</a>
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