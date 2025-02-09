<h1>Triagem</h1>
<form action="?page=salvar-triagem&acao=cadastrar" method="POST">
    <input type="hidden" name="id_recepcao" value="<?php echo $id_recepcao; ?>">
    <input type="hidden" name="paciente_id_paciente" value="<?php echo $paciente_id; ?>">
    <input type="hidden" name="acao" value="cadastrar">

    <!-- Seleção do paciente -->
    <div class="mb-3">
        <label>Paciente:</label>
        <select name="paciente_id_paciente" class="form-control" required>
            <option>-= Escolha um paciente =-</option>
            <?php
            // Buscar pacientes aguardando triagem
            $sql_3 = "
                SELECT r.paciente_id_paciente, p.nome_paciente 
                FROM recepcao r
                JOIN paciente p ON r.paciente_id_paciente = p.id_paciente
                WHERE r.status = 'Aguardando Triagem'
                ";

            $res_3 = $conn->query($sql_3);

            if ($res_3 && $res_3->num_rows > 0) {
                // Exibe os pacientes aguardando triagem
                while ($row_3 = $res_3->fetch_object()) {
                    print "<option value='{$row_3->paciente_id_paciente}'>{$row_3->nome_paciente}</option>";
                }
            } else {
                print "<option disabled>Nenhum paciente aguardando triagem</option>";
            }
            ?>
        </select>
    </div>

    <!-- Pressão Arterial -->
    <div class="mb-3">
        <label>Pressão Arterial:</label>
        <input type="text" name="pressao_arterial" class="form-control" placeholder="Exemplo: 120/80 mmHg" required>
    </div>

    <!-- Frequência Cardíaca -->
    <div class="mb-3">
        <label>Frequência Cardíaca (bpm):</label>
        <input type="number" name="frequencia_cardiaca" class="form-control" placeholder="Exemplo: 72 bpm" min="30"
            max="200" required>
    </div>

    <!-- Temperatura -->
    <div class="mb-3">
        <label>Temperatura (°C):</label>
        <input type="number" step="0.1" name="temperatura" class="form-control" placeholder="Exemplo: 36.5 °C" min="30"
            max="45" required>
    </div>

    <!-- Classificação de Gravidade -->
    <div class="mb-3">
        <label>Classificação de Gravidade:</label>
        <select name="classificacao_gravidade" class="form-control" required>
            <option value="Vermelho">Vermelho: Emergência</option>
            <option value="Amarelo">Amarelo: Urgência</option>
            <option value="Verde">Verde: Não Urgente</option>
            <option value="Azul">Azul: Casos Leves</option>
        </select>
    </div>

    <!-- Queixa Principal -->
    <div class="mb-3">
        <label>Queixa Principal:</label>
        <textarea name="observacoes" class="form-control" placeholder="Descrição da queixa do paciente" rows="3"
            required></textarea>
    </div>

    <!-- Status do Paciente -->
    <div class="mb-3">
        <label>Status do Paciente:</label>
        <select name="status" class="form-control" required>
            <option value="Aguardando Consulta">Aguardando Consulta</option>
            <option value="Emergência">Emergência</option>
            <option value="Aguardando Exames">Aguardando Exames</option>
        </select>
    </div>

    <!-- Encaminhamento para Especialidade -->
    <div class="mb-3">
        <label>Encaminhamento Especialidade:</label>
        <select name="encaminhamento" id="encaminhamento" class="form-control" required>
            <option>-= Escolha a especialidade =-</option>
            <?php
            // Buscar especialidades da tabela "especialidade"
            $sql_especialidades = "SELECT id_especialidade, nome_especialidade FROM especialidade";
            $res_especialidades = $conn->query($sql_especialidades);

            if ($res_especialidades && $res_especialidades->num_rows > 0) {
                while ($row_especialidade = $res_especialidades->fetch_object()) {
                    print "<option value='{$row_especialidade->id_especialidade}'>{$row_especialidade->nome_especialidade}</option>";
                }
            } else {
                print "<option disabled>Nenhuma especialidade disponível</option>";
            }
            ?>
        </select>
    </div>

    <!-- Botão para salvar -->
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Salvar Triagem</button>
    </div>
</form>