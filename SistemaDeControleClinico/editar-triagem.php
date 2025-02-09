<?php
// Verificar se foi passado o id da triagem para editar
if (isset($_GET['id_triagem'])) {
    $id_triagem = $_GET['id_triagem'];

    // Buscar os dados da triagem para o id especificado
    $sql = "
    SELECT t.*, p.nome_paciente, e.nome_especialidade, r.status
    FROM triagem t
    JOIN paciente p ON t.paciente_id_paciente = p.id_paciente
    JOIN especialidade e ON t.especialidade_id_especialidade = e.id_especialidade
    JOIN recepcao r ON r.paciente_id_paciente = p.id_paciente
    WHERE t.id_triagem = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_triagem);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se a triagem foi encontrada
    if ($result->num_rows > 0) {
        $triagem = $result->fetch_object();
    } else {
        echo "Triagem não encontrada.";
        exit;
    }
} else {
    echo "ID da triagem não especificado.";
    exit;
}
?>

<h1>Editar Triagem</h1>
<form action="?page=salvar-triagem&id_triagem=<?php echo $triagem->id_triagem; ?>" method="POST">
    <input type="hidden" name="acao" value="editar">
    <input type="hidden" name="id_triagem" value="<?php echo $triagem->id_triagem; ?>">

    <!-- Paciente (não editável, pois já está associado) -->
    <div class="mb-3">
        <label>Paciente:</label>
        <input type="text" class="form-control" value="<?php echo $triagem->nome_paciente; ?>" disabled>
    </div>

    <!-- Pressão Arterial -->
    <div class="mb-3">
        <label>Pressão Arterial:</label>
        <input type="text" name="pressao_arterial" class="form-control" value="<?php echo $triagem->pressao; ?>"
            required>
    </div>

    <!-- Frequência Cardíaca -->
    <div class="mb-3">
        <label>Frequência Cardíaca (bpm):</label>
        <input type="number" name="frequencia_cardiaca" class="form-control"
            value="<?php echo $triagem->frequencia_cardiaca; ?>" min="30" max="200" required>
    </div>

    <!-- Temperatura -->
    <div class="mb-3">
        <label>Temperatura (°C):</label>
        <input type="number" step="0.1" name="temperatura" class="form-control"
            value="<?php echo $triagem->temperatura; ?>" min="30" max="45" required>
    </div>

    <!-- Classificação de Gravidade -->
    <div class="mb-3">
        <label>Classificação de Gravidade:</label>
        <select name="classificacao_gravidade" class="form-control" required>
            <option value="Vermelho" <?php echo $triagem->prioridade == 'Vermelho' ? 'selected' : ''; ?>>Vermelho:
                Emergência</option>
            <option value="Amarelo" <?php echo $triagem->prioridade == 'Amarelo' ? 'selected' : ''; ?>>Amarelo: Urgência
            </option>
            <option value="Verde" <?php echo $triagem->prioridade == 'Verde' ? 'selected' : ''; ?>>Verde: Não Urgente
            </option>
            <option value="Azul" <?php echo $triagem->prioridade == 'Azul' ? 'selected' : ''; ?>>Azul: Casos Leves
            </option>
        </select>
    </div>

    <!-- Queixa Principal -->
    <div class="mb-3">
        <label>Queixa Principal:</label>
        <textarea name="queixa_principal" class="form-control" rows="3"
            required><?php echo $triagem->observacoes; ?></textarea>
    </div>
    <!-- Status do Paciente -->
    <div class="mb-3">
        <input type="hidden" name="id_recepcao" value="<?php echo $triagem->id_recepcao; ?>">
        <label>Status do Paciente:</label>
        <select name="status" class="form-control" required>
            <option value="Aguardando Consulta"
                <?php echo $triagem->status == 'Aguardando Consulta' ? 'selected' : ''; ?>>Aguardando Consulta</option>
            <option value="Emergência" <?php echo $triagem->status == 'Emergência' ? 'selected' : ''; ?>>Emergência
            </option>
            <option value="Aguardando Exames" <?php echo $triagem->status == 'Aguardando Exames' ? 'selected' : ''; ?>>
                Aguardando Exames</option>
        </select>
    </div>

    <!-- Encaminhamento para Especialidade -->
    <div class="mb-3">
        <label>Encaminhamento (Especialidade):</label>
        <select name="encaminhamento" class="form-control" required>
            <option value="">-= Escolha a especialidade =-</option>
            <?php
            // Buscar especialidades
            $sql_especialidades = "SELECT id_especialidade, nome_especialidade FROM especialidade";
            $res_especialidades = $conn->query($sql_especialidades);

            if ($res_especialidades && $res_especialidades->num_rows > 0) {
                while ($row_especialidade = $res_especialidades->fetch_object()) {
                    $selected = $triagem->especialidade_id_especialidade == $row_especialidade->id_especialidade ? 'selected' : '';
                    print "<option value='{$row_especialidade->id_especialidade}' $selected>{$row_especialidade->nome_especialidade}</option>";
                }
            } else {
                print "<option disabled>Nenhuma especialidade disponível</option>";
            }
            ?>
        </select>
    </div>

    <!-- Botão para salvar -->
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </div>
</form>