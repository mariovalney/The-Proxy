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

/** Post Value Errors ? **/
$postErros = FALSE;

/** Start Page **/

if (isset($_GET['delete'])) {
    if (is_numeric($_GET['delete'])) {
        $result = $avdb->deleteData($table, ['ID' => $_GET['delete']]);
        $avdb->refreshIdExample($table);

        $redirect = BASE_URL;
        header("location:$redirect");
    }
} else {

    if (isset($_POST['send-registry'])) {
        $ip_origem = $_POST['ip_origem'];
        $ip_destino = $_POST['ip_destino'];
        $protocolo = $_POST['protocolo'];
        $porta = $_POST['porta'];
        $dados = completeData($_POST['dados']);

        if ( !empty($ip_origem) && !empty($ip_destino) &&  !empty($protocolo) &&  (!empty($porta) || $porta == 0) &&  !empty($dados) ) {

            if (!checkIP($ip_origem)) {
                $postErros = TRUE;
            }

            if (!checkIP($ip_destino)) {
                $postErros = TRUE;
            }

            if (!checkProtocol($protocolo)) {
                $postErros = TRUE;
            }

            if (!checkDoor($porta)) {
                $postErros = TRUE;
            }

            if (!checkData($dados)) {
                $postErros = TRUE;
            }

            if (!$postErros) {
                $dados = serialize(encode($dados));
                        
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
            } else {
                $toast = 'Algum dado enviado está errado, por favor verifique o formulário.';
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
            <h3 class="left">Registros</h3>
            <a class="waves-effect waves-light right btn modal-trigger" href="#addregistry">
                <i class="material-icons left">add</i>Adicionar Registro
            </a>
        </div>
        <div class="col s12">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>IP de Origem</th>
                        <th>IP de Destino</th>
                        <th>Protocolo</th>
                        <th>Porta</th>
                        <th>Dados</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        /** Selecting $columns from $table **/
                        $registries = $avdb->selectData($table, $columns);
                        if (count($registries) > 0) {
                            foreach ($registries as $registry) {
                                $dados = unserialize($registry["dados"]);
                                $dados = decode($dados);
                                
                                echo '<tr class="registry">';
                                echo '<td class="registry-ID" data-value="' . checkEmpty($registry["ID"]) . '">' . checkEmpty($registry["ID"]) . '</td>';
                                echo '<td class="registry-ip_origem" data-value="' . checkEmpty($registry["ip_origem"]) . '">' . checkEmpty($registry["ip_origem"]) . '</td>';
                                echo '<td class="registry-ip_destino" data-value="' . checkEmpty($registry["ip_destino"]) . '">' . checkEmpty($registry["ip_destino"]) . '</td>';
                                echo '<td class="registry-protocolo" data-value="' . checkEmpty($registry["protocolo"]) . '">' . checkEmpty($registry["protocolo"]) . '</td>';
                                echo '<td class="registry-porta" data-value="' . checkEmpty($registry["porta"]) . '">' . checkEmpty($registry["porta"]) . '</td>';
                                echo '<td class="registry-dados" data-value="' . checkEmpty($dados) . '">' . checkEmpty($dados) . '</td>';
                                echo '<td>
                                        <ul class="table-actions">
                                            <li>
                                                <span class="edit">EDITAR</span>
                                            </li>
                                            <li>
                                                <span class="delete">
                                                    <i class="material-icons">delete</i>
                                                </span>
                                            </li>
                                        </ul>
                                    </td>';
                                echo '</tr>';
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
            <a class="right" href="<?php echo BASE_URL . 'download' ?>" onclick="Materialize.toast('Download Iniciado', 4000)" target="_blank">Fazer download do arquivo</a>
            <a class="right" href="<?php echo BASE_URL . 'aes' ?>" style="margin-right: 10px;padding-right: 10px;border-right: 1px solid #CCCCCC;">Ver dados criptografados</a>
            <a class="right" href="<?php echo BASE_URL . 'importar' ?>" style="margin-right: 10px;padding-right: 10px;border-right: 1px solid #CCCCCC;">Importar dados</a>
        </div>
    </div>
</section>

<?php if ($postErros == TRUE) {
    echo '<div id="addregistry" class="modal open-on-ready">';
    echo '<input id="ID" name="ID" type="hidden" value="' . $_POST['ID'] . '">';
} else {
    echo '<div id="addregistry" class="modal">';
} ?>

    <form id="addregistryform" method="post" action="">
        <div class="modal-content">
            <h4>Adicionar Registro</h4>
            <div class="row">
                <div class="input-field col s4">
                    <input id="ip_origem" name="ip_origem" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_origem'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_origem">Origem</label>
                </div>
                <div class="input-field col s4">
                    <input id="ip_destino" name="ip_destino" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_destino'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_destino">Destino</label>
                </div>
                <div class="input-field col s2">
                    <input id="protocolo" name="protocolo" type="text" class="validate mask-protocol" <?php if($postErros) { echo 'value="' . $_POST['protocolo'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="protocolo">Protocolo</label>
                </div>
                <div class="input-field col s2">
                    <input id="porta" name="porta" type="text" class="validate mask-door" <?php if($postErros) { echo 'value="' . $_POST['porta'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="porta">Porta</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="dados" name="dados" class="mask-dados" type="text" <?php if($postErros) { echo 'value="' . $_POST['dados'] . '"'; } ?> length="50" autocomplete="off" required="required">
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
                    <input id="protocolo" name="protocolo" type="text" class="validate mask-protocol" autocomplete="off" required="required">
                    <label for="protocolo">Protocolo</label>
                </div>
                <div class="input-field col s2">
                    <input id="porta" name="porta" type="text" class="validate mask-door" autocomplete="off" required="required">
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