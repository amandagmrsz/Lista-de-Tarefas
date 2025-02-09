<h1>Editar Recepção</h1>
<?php
    // Buscar os dados da recepção com base no ID
    $sql = "SELECT * FROM recepcao WHERE id_recepcao=" . $_REQUEST['id_recepcao'];
    $res = $conn->query($sql);
    $row = $res->fetch_object();
?>
<form action="?page=salvar-recepcao" method="POST">
    <input type="hidden" name="acao" value="editar">
    <input type="hidden" name="id_recepcao" value="<?php print $row->id_recepcao; ?>">

    <!-- Seleção de Paciente -->
    <div class="mb-3">
        <label>Paciente</label>
        <select name="paciente_id_paciente" class="form-control" required>
            <option>-= Escolha um paciente =-</option>
            <?php
            // Consultar todos os pacientes
            $sql_2 = "SELECT * FROM paciente";
            $res_2 = $conn->query($sql_2);

            if ($res_2->num_rows > 0) {
                while ($row_2 = $res_2->fetch_object()) {
                    // Verificar se é o paciente selecionado na recepção
                    $selected = ($row_2->id_paciente == $row->paciente_id_paciente) ? "selected" : "";
                    print "<option value='" . $row_2->id_paciente . "' $selected>" . $row_2->nome_paciente . "</option>";
                }
            } else {
                print "<option>Não possui pacientes cadastrados</option>";
            }
            ?>
        </select>
    </div>

    <!-- Tipo de Atendimento -->
    <div class="mb-3">
        <label for="tipo_atendimento">Tipo de Atendimento:</label>
        <select id="tipo_atendimento" name="tipo_atendimento" class="form-control" required>
            <option value="emergencia" <?php echo ($row->tipo_atendimento == 'emergencia') ? "selected" : ""; ?>>
                Emergência</option>
            <option value="consulta" <?php echo ($row->tipo_atendimento == 'consulta') ? "selected" : ""; ?>>Consulta
            </option>
            <option value="retorno" <?php echo ($row->tipo_atendimento == 'retorno') ? "selected" : ""; ?>>Retorno
            </option>
            <option value="exames" <?php echo ($row->tipo_atendimento == 'exames') ? "selected" : ""; ?>>Exames</option>
        </select>
    </div>

    <!-- Botão para salvar a edição -->
    <div class="mb-3">
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </div>
</form>