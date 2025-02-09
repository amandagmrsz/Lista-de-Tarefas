<h1>Cadastrar Médico</h1>
<form action="?page=salvar-medico" method="POST">
    <input type="hidden" name="acao" value="cadastrar">
    <div class="mb-3">
        <label>Nome</label>
        <input type="text" name="nome_medico" class="form-control">
    </div>
    <div class="mb-3">
        <label>CRM</label>
        <input type="text" name="crm_medico" class="form-control">
    </div>
    <div class="mb-3">
        <label>Especialidade</label>
        <select name="especialidade_medico" class="form-control" required>
            <option value="">-= Selecione uma Especialidade =-</option>
            <?php
			// Consulta as especialidades disponíveis no banco de dados
			$sql = "SELECT id_especialidade, nome_especialidade FROM especialidade";
			$res = $conn->query($sql);

			if ($res->num_rows > 0) {
				// Exibe as especialidades no select
				while ($row = $res->fetch_assoc()) {
					echo "<option value='" . $row['id_especialidade'] . "'>" . $row['nome_especialidade'] . "</option>";
				}
			} else {
				echo "<option>Nenhuma especialidade disponível</option>";
			}
			?>
        </select>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-success">Salvar</button>
    </div>
</form>