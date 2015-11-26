<?php

/* 
 * Index of theme "TheProxy"
 *
 * @verson: final
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

    if (isset($_POST['send-rule'])) {
        $nome = completeName_forRule($_POST['nome']);
        $prioridade = $_POST['prioridade'];
        $ip_origem = $_POST['ip_origem'];
        $ip_destino = $_POST['ip_destino'];
        $direction = $_POST['direction'];
        $protocolo = $_POST['protocolo'];
        $porta_inicial = $_POST['porta_inicial'];
        $porta_final = $_POST['porta_final'];
        $action = $_POST['action'];
        $dados = completeData_forRule($_POST['dados']);

        if ( !empty($nome) && !empty($prioridade) && !empty($ip_origem) && !empty($ip_destino) &&  ($direction != 'E' || $direction != 'S') && !empty($protocolo) &&  ($action != 'P' || $action != 'B') &&  !empty($dados) ) {

            if (!isset($_POST['ID'])) {
                if (!checkName_forRule($nome)) {
                    $postErros = TRUE;
                    $nameIsError = TRUE;
                }
            }
            
            if (!checkPriority_forRule($prioridade)) {
                $postErros = TRUE;
            }

            if (!checkIP_forRule($ip_origem)) {
                $postErros = TRUE;
            }

            if (!checkIP_forRule($ip_destino)) {
                $postErros = TRUE;
            }

            if (!checkDirection_forRule($direction)) {
                $postErros = TRUE;
            }

            if (!checkProtocol_forRule($protocolo)) {
                $postErros = TRUE;
            }

            if (!checkDoor_forRule($porta_inicial)) {
                $postErros = TRUE;
            }

            if (!checkDoorFinal_forRule($porta_final)) {
                $postErros = TRUE;
            }

            if ($porta_final != '') {
                if ($porta_inicial >= $porta_final) {
                    $postErros = TRUE;
                }
            }

            if (!checkAction_forRule($action)) {
                $postErros = TRUE;
            }

            if (!checkData_forRule($dados)) {
                $postErros = TRUE;
            }

            if (!$postErros) {
                $dados = serialize(encode_forRule($dados));

                // Atualizando as prioridades
                $allPriorities = $avdb->selectData('tp_regras', array('ID', 'prioridade'));
                if (!empty($allPriorities)) {
                    foreach ($allPriorities as $line) {
                        $priorities[$line['ID']] = $line['prioridade'];
                    }
                }

                $newP = $prioridade;

                if (isset($_POST['ID'])) {
                    $oldP = $priorities[$_POST['ID']];
                } else {
                    $oldP = count($priorities + 1);
                }

                if (in_array($newP, $priorities) && ($newP != $oldP)) {
                    $elMaiores = 0;

                    foreach ($priorities as $key => $value) {
                        if ($value == $oldP) {
                            $newPriorities[$key] = $newP;
                        } else {
                            if ($value < $newP) {
                                $newPriorities[$key] = $value;
                            } else if ($value == $newP) {
                                $newPriorities[$key] = ($value + 1);
                            } else {
                                $newPriorities[$key] = $value;
                                $elMaiores++;
                            }
                        }
                    }

                    $i = $newP;
                    while ($elMaiores > 0) {

                        if ( in_array(($i + 1), $newPriorities) && (($i + 1) != $oldP) ) {
                            foreach ($priorities as $key => $value) {
                                if ($value == ($i + 1) && $value != $newP) {
                                    $newPriorities[$key] = ($value + 1);
                                    $elMaiores--;
                                }
                            }

                            $i++;
                        } else {
                            $elMaiores = 0;            
                        }
                    }

                } else {
                    $newPriorities = $priorities;
                }

                foreach ($newPriorities as $id => $newPriority) {
                    $avdb->updateData($table, array('prioridade'), array($newPriority), 'ID', $id);
                }

                // Salvando dados
                if (isset($_POST['ID'])) {
                    $id = $_POST['ID'];

                    $values = array($nome, $prioridade, $ip_origem, $ip_destino, $direction, $protocolo, $porta_inicial, $porta_final, $action, $dados);
                    $avdb->updateData($table, $fields, $values, 'ID', $id);
                    $toast = 'Regra alterada com sucesso.';
                } else {
                    $values = array($nome, $prioridade, $ip_origem, $ip_destino, $direction, $protocolo, $porta_inicial, $porta_final, $action, $dados);
                    $avdb->insertData($table, $fields, $values);
                    $toast = 'Regra adicionada com sucesso.';
                }
            } else {
                $toast = 'Algum dado enviado está errado, por favor verifique o formulário.';
            }
        }
    }

}

set_meta(['title' => __('The PROXY - Sistema de Cadastro de Regras!')]);
include_header();

?>

<section class="list">
    <div class="row">
        <div class="col s12 section-title">
            <h3 class="left">Regras</h3>
            <a class="waves-effect waves-light right btn modal-trigger" href="#addrule">
                <i class="material-icons left">add</i>Adicionar Regra
            </a>
        </div>
        <div class="col s12">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Prioridade</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Direção</th>
                        <th>Protocolo</th>
                        <th>Portas</th>
                        <th>Ação</th>
                        <th>Conteúdo</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        /** Selecting $columns from $table **/
                        $rules = $avdb->selectData($table, $columns);

                        if (count($rules) > 0) {
                            foreach ($rules as $rule) {
                                $dados = unserialize($rule["dados"]);
                                $dados = decode_forRule($dados);

                                echo '<tr class="rule" data-value="' . checkEmpty($rule["ID"]) . '">';
                                    echo '<td class="rule-nome" data-value="' . checkEmpty($rule["nome"]) . '">' . checkEmpty($rule["nome"]) . '</td>';
                                    echo '<td class="rule-prioridade" data-value="' . checkEmpty($rule["prioridade"]) . '">' . checkEmpty($rule["prioridade"]) . '</td>';
                                    echo '<td class="rule-ip_origem" data-value="' . checkEmpty($rule["ip_origem"]) . '">' . checkEmpty($rule["ip_origem"]) . '</td>';
                                    echo '<td class="rule-ip_destino" data-value="' . checkEmpty($rule["ip_destino"]) . '">' . checkEmpty($rule["ip_destino"]) . '</td>';
                                    echo '<td class="rule-direction" data-value="' . checkEmpty($rule["direction"]) . '">' . checkEmpty($rule["direction"]) . '</td>';
                                    echo '<td class="rule-protocolo" data-value="' . checkEmpty($rule["protocolo"]) . '">' . checkEmpty($rule["protocolo"]) . '</td>';

                                    if ($rule["porta_final"] == '') {
                                        echo '<td class="rule-portas" data-value-initial="' . $rule["porta_inicial"] . '" data-value-final="">' . $rule["porta_inicial"] . '</td>';
                                    } else {
                                        echo '<td class="rule-portas" data-value-initial="' . $rule["porta_inicial"] . '" data-value-final="' . $rule["porta_final"] . '">' . $rule["porta_inicial"] . '-' . $rule["porta_final"] . '</td>';
                                    }

                                    echo '<td class="rule-action" data-value="' . checkEmpty($rule["action"]) . '">' . checkEmpty($rule["action"]) . '</td>';
                                    echo '<td class="rule-dados" data-value="' . checkEmpty($dados) . '">' . checkEmpty($dados) . '</td>';
                                    echo '<td>
                                            <ul class="table-actions table-actions-rules">
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
                            echo '<tr><td class="center" colspan="10">Não há nenhuma regra cadastrada.</td><tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <a class="right" href="<?php echo BASE_URL . 'download' ?>" onclick="Materialize.toast('Download Iniciado', 4000)" target="_blank">Fazer download do arquivo de regras</a>
            <a class="right" href="<?php echo BASE_URL . 'testar' ?>" style="margin-right: 10px;padding-right: 10px;border-right: 1px solid #CCCCCC;">Testar pacote de dados</a>
            <a class="right" href="<?php echo BASE_URL . 'v1' ?>" style="margin-right: 10px;padding-right: 10px;border-right: 1px solid #CCCCCC;">Ir para o Gerenciamento de Pacotes</a>
        </div>
    </div>
</section>

<?php if ($postErros == TRUE) {
    echo '<div id="addrule" class="modal modal-fixed-footer open-on-ready">';
} else {
    echo '<div id="addrule" class="modal modal-fixed-footer">';
} ?>

    <form id="addruleform" method="post" action="">
        <div class="modal-content">
            <h4>Adicionar Regra</h4>
            <div class="row">
                <div class="input-field col s3">
                    <input id="prioridade" name="prioridade" type="number" min="1" max="99" class="validate" <?php if($postErros) { echo 'value="' . $_POST['prioridade'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="prioridade">Prioridade <span class="tooltipped" style="cursor:pointer!important" data-position="right" data-delay="50" data-tooltip="Escolher uma prioriade que já esteja em uso empurrará todas as regras para baixo."><i class="material-icons" style="font-size:16px!important">live_help</i></span></label>
                </div>
                <div class="input-field col s9">
                    <input id="nome" name="nome" class="mask-nome validate <?php if(isset($nameIsError) && $nameIsError == true) { echo 'invalidated'; } ?>" type="text" <?php if($postErros) { echo 'value="' . $_POST['nome'] . '"'; } ?> length="20" autocomplete="off" required="required">
                    <label for="nome">Nome da Regra (deve ser único)</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s5">
                    <input id="ip_origem" name="ip_origem" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_origem'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_origem">Origem</label>
                </div>
                <div class="input-field col s5">
                    <input id="ip_destino" name="ip_destino" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_destino'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_destino">Destino</label>
                </div>
                <div class="input-field col s2">
                    <select id="direction" name="direction" autocomplete="off" required="required">
                        <?php if($postErros && ($_POST['direction'] == "S")) : ?>
                            <option value="E">Entrada</option>
                            <option value="S" selected="selected">Saída</option>
                        <?php else : ?>
                            <option value="E" selected="selected">Entrada</option>
                            <option value="S">Saída</option>
                        <?php endif; ?>
                    </select>
                    <label for="direction">Direção</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s3">
                    <select id="protocolo" name="protocolo" autocomplete="off" required="required">
                        <option value="*" selected="selected">*</option>
                        <option value="TCP">TCP</option>
                        <option value="UDP">UDP</option>
                        <option value="ICMP">ICMP</option>
                    </select>
                    <label for="protocolo">Protocolo</label>
                </div>
                <?php
                    if($postErros && ($_POST['porta_inicial'] >= $_POST['porta_final'])) {
                        $doorClass = 'invalidated';
                    } else {
                        $doorClass = '';
                    }
                ?>
                <div class="input-field col s3">
                    <input id="porta_inicial" name="porta_inicial" type="text" class="validate <?php echo $doorClass; ?> mask-door mask-door-initial" <?php if($postErros) { echo 'value="' . $_POST['porta_inicial'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="porta_inicial">Porta Inicial</label>
                </div>
                <div class="input-field col s3">
                    <input id="porta_final" name="porta_final" type="text" class="validate <?php echo $doorClass; ?> mask-door mask-door-final" <?php if($postErros) { echo 'value="' . $_POST['porta_final'] . '"'; } ?> autocomplete="off">
                    <label for="porta_final">Porta Final</label>
                </div>
                <div class="input-field col s3">
                    <select id="action" name="action" autocomplete="off" required="required">
                        <?php if($postErros && ($_POST['action'] == "B")) : ?>
                            <option value="P">Permitir</option>
                            <option value="B" selected="selected">Bloquear</option>
                        <?php else : ?>
                            <option value="P" selected="selected">Permitir</option>
                            <option value="B">Bloquear</option>
                        <?php endif; ?>
                    </select>
                    <label for="action">Ação</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="dados" name="dados" class="mask-dados-for-rule" type="text" <?php if($postErros) { echo 'value="' . $_POST['dados'] . '"'; } ?> length="30" autocomplete="off" required="required">
                    <label for="dados">Dados</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="btn btn-large waves-effect waves-light btn-input">
                <input type="submit" name="send-rule" class="search-submit" form="addruleform" value="Adicionar Regra">
            </span>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        </div>
    </form>
</div>

<div id="editrule" class="modal modal-fixed-footer">
    <form id="editruleform" method="post" action="">
        <input id="ID" name="ID" type="hidden">
        <div class="modal-content">
            <h4>Alterar Regra (<span class="editing-id">ID</span>)</h4>
            <div class="row">
                <div class="input-field col s3">
                    <input id="prioridade" name="prioridade" type="number" min="1" max="99" class="validate" <?php if($postErros) { echo 'value="' . $_POST['prioridade'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="prioridade">Prioridade <span class="tooltipped" style="cursor:pointer!important" data-position="right" data-delay="50" data-tooltip="Escolher uma prioriade que já esteja em uso empurrará todas as regras para baixo."><i class="material-icons" style="font-size:16px!important">live_help</i></span></label>
                </div>
                <div class="input-field col s9">
                    <input id="nome" name="nome" class="mask-nome validate <?php if(isset($nameIsError) && $nameIsError == true) { echo 'invalidated'; } ?>" type="text" <?php if($postErros) { echo 'value="' . $_POST['nome'] . '"'; } ?> length="50" autocomplete="off" required="required">
                    <label for="nome">Nome da Regra (deve ser único)</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s5">
                    <input id="ip_origem" name="ip_origem" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_origem'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_origem">Origem</label>
                </div>
                <div class="input-field col s5">
                    <input id="ip_destino" name="ip_destino" type="text" class="validate mask-ip" <?php if($postErros) { echo 'value="' . $_POST['ip_destino'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="ip_destino">Destino</label>
                </div>
                <div class="input-field col s2">
                    <select id="direction" name="direction" autocomplete="off" required="required">
                        <?php if($postErros && ($_POST['direction'] == "S")) : ?>
                            <option value="E">Entrada</option>
                            <option value="S" selected="selected">Saída</option>
                        <?php else : ?>
                            <option value="E" selected="selected">Entrada</option>
                            <option value="S">Saída</option>
                        <?php endif; ?>
                    </select>
                    <label for="direction">Direção</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s3">
                    <select id="protocolo" name="protocolo" autocomplete="off" required="required">
                        <option value="*" selected="selected">*</option>
                        <option value="TCP">TCP</option>
                        <option value="UDP">UDP</option>
                        <option value="ICMP">ICMP</option>
                    </select>
                    <label for="protocolo">Protocolo</label>
                </div>
                <?php
                    if($postErros && ($_POST['porta_inicial'] >= $_POST['porta_final']) && ($_POST['porta_final'] != '')) {
                        $doorClass = 'invalidated';
                    } else {
                        $doorClass = '';
                    }
                ?>
                <div class="input-field col s3">
                    <input id="porta_inicial" name="porta_inicial" type="text" class="validate <?php echo $doorClass; ?> mask-door mask-door-initial" <?php if($postErros) { echo 'value="' . $_POST['porta_inicial'] . '"'; } ?> autocomplete="off" required="required">
                    <label for="porta_inicial">Porta Inicial</label>
                </div>
                <div class="input-field col s3">
                    <input id="porta_final" name="porta_final" type="text" class="validate <?php echo $doorClass; ?> mask-door mask-door-final" <?php if($postErros) { echo 'value="' . $_POST['porta_final'] . '"'; } ?> autocomplete="off">
                    <label for="porta_final">Porta Final</label>
                </div>
                <div class="input-field col s3">
                    <select id="action" name="action" autocomplete="off" required="required">
                        <?php if($postErros && ($_POST['action'] == "B")) : ?>
                            <option value="P">Permitir</option>
                            <option value="B" selected="selected">Bloquear</option>
                        <?php else : ?>
                            <option value="P" selected="selected">Permitir</option>
                            <option value="B">Bloquear</option>
                        <?php endif; ?>
                    </select>
                    <label for="action">Ação</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="dados" name="dados" class="mask-dados-for-rule" type="text" <?php if($postErros) { echo 'value="' . $_POST['dados'] . '"'; } ?> length="30" autocomplete="off" required="required">
                    <label for="dados">Dados</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="btn btn-large waves-effect waves-light btn-input">
                <input type="submit" name="send-rule" class="search-submit" form="editruleform" value="Atualizar Regra">
            </span>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        </div>
    </form>
</div>

<div id="deleterule" class="modal">
    <div class="modal-content">
        <h4>Excluir Regra</h4>
        <p>Você tem certeza que deseja fazer isso? <strong>Não será possível desfazer essa ação.</strong></p>
    </div>
    <div class="modal-footer">
        <a href="#!" class="btn btn-large waves-effect waves-red red darken-4 btn-delete delete-link">Apagar</a>
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
    </div>
</div>

<?php include_footer();