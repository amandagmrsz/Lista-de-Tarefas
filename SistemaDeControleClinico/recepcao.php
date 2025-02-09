<h1>Recepção</h1>
<form action="?page=salvar-recepcao" method="POST">
    <input type="hidden" name="acao" value="cadastrar">

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
                // Listar todos os pacientes
                while ($row_2 = $res_2->fetch_object()) {
                    print "<option value='" . $row_2->id_paciente . "'>" . $row_2->nome_paciente . "</option>";
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
            <option value="emergencia">Emergência</option>
            <option value="consulta">Consulta</option>
            <option value="retorno">Retorno</option>
            <option value="exames">Exames</option>
        </select>
    </div>

    <!-- Botão para salvar a recepção -->
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Salvar Recepção</button>
    </div>
</form>