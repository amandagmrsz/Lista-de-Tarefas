<h1>Editar Médico</h1>
<?php
// Buscar os dados do médico com base no ID recebido via $_REQUEST
$sql = "SELECT * FROM medico WHERE id_medico=" . $_REQUEST['id_medico'];
$res = $conn->query($sql);
$row = $res->fetch_object();
?>
<form action="?page=salvar-medico" method="POST">
    <input type="hidden" name="acao" value="editar">
    <input type="hidden" name="id_medico" value="<?php echo $row->id_medico; ?>">

    <!-- Nome do Médico -->
    <div class="mb-3">
        <label for="nome_medico">Nome</label>
        <input type="text" id="nome_medico" name="nome_medico" class="form-control"
            value="<?php echo $row->nome_medico; ?>" required>
    </div>

    <!-- CRM -->
    <div class="mb-3">
        <label for="crm_medico">CRM</label>
        <input type="text" id="crm_medico" name="crm_medico" class="form-control"
            value="<?php echo $row->crm_medico; ?>" required>
    </div>

    <!-- Especialidade -->
    <div class="mb-3">
        <label for="especialidade_medico">Especialidade</label>
        <select id="especialidade_medico" name="especialidade_medico" class="form-control" required>
            <option value="">-= Selecione uma Especialidade =-</option>
            <?php
            // Consultar especialidades disponíveis
            $sql_2 = "SELECT id_especialidade, nome_especialidade FROM especialidade";
            $res_2 = $conn->query($sql_2);

            if ($res_2->num_rows > 0) {
                while ($row_2 = $res_2->fetch_assoc()) {
                    // Marcar como selecionada a especialidade atual do médico
                    $selected = ($row_2['id_especialidade'] == $row->especialidade_id) ? "selected" : "";
                    echo "<option value='" . $row_2['id_especialidade'] . "' $selected>" . $row_2['nome_especialidade'] . "</option>";
                }
            } else {
                echo "<option value=''>Nenhuma especialidade disponível</option>";
            }
            ?>
        </select>
    </div>

    <!-- Botão para Salvar Alterações -->
    <div class="mb-3">
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </div>
</form>