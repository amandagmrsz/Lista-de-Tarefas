<?php
switch ($_REQUEST['acao']) {
	case 'cadastrar':
		$nome = $_POST['nome_medico'];
		$crm = $_POST['crm_medico'];
		$especialidade = $_POST['especialidade_medico']; // Assumindo que você está recebendo o ID da especialidade

		// Inserir o médico na tabela medico
		$sql = "INSERT INTO medico (nome_medico, crm_medico) VALUES ('{$nome}', '{$crm}')";
		$res = $conn->query($sql);

		if ($res) {
			// Recuperar o ID do médico inserido
			$id_medico = $conn->insert_id;

			// Inserir o relacionamento médico-especialidade
			$sql_especialidade = "INSERT INTO medico_especialidade (medico_id_medico, especialidade_id_especialidade)
			VALUES ('{$id_medico}', '{$especialidade}')";
			$res_especialidade = $conn->query($sql_especialidade);

			if ($res_especialidade) {
				print "<script>
			alert('Cadastrou com sucesso!');
			</script>";
				print "<script>
			location.href = '?page=listar-medico';
			</script>";
			} else {
				print "<script>
			alert('Erro ao associar especialidade.');
			</script>";
				print "<script>
			location.href = '?page=listar-medico';
			</script>";
			}
		} else {
			print "<script>
			alert('Deu errado');
			</script>";
			print "<script>
			location.href = '?page=listar-medico';
			</script>";
		}
		break;

	case 'editar':
		$nome = $_POST['nome_medico'];
		$crm = $_POST['crm_medico'];
		$especialidade = $_POST['especialidade_medico']; // Assumindo que você está recebendo o ID da especialidade

		// Atualizar o médico na tabela medico
		$sql = "UPDATE medico SET nome_medico='{$nome}', crm_medico='{$crm}' WHERE id_medico={$_REQUEST["id_medico"]}";
		$res = $conn->query($sql);

		if ($res) {
			// Atualizar a especialidade do médico na tabela medico_especialidade
			$sql_especialidade = "UPDATE medico_especialidade SET especialidade_id_especialidade='{$especialidade}'
			WHERE medico_id_medico={$_REQUEST["id_medico"]}";
			$res_especialidade = $conn->query($sql_especialidade);

			if ($res_especialidade) {
				print "<script>
			alert('Editou com sucesso!');
			</script>";
				print "<script>
			location.href = '?page=listar-medico';
			</script>";
			} else {
				print "<script>
			alert('Erro ao atualizar especialidade.');
			</script>";
				print "<script>
			location.href = '?page=listar-medico';
			</script>";
			}
		} else {
			print "<script>
			alert('Deu errado');
			</script>";
			print "<script>
			location.href = '?page=listar-medico';
			</script>";
		}
		break;

	case 'excluir':
		// Excluir o relacionamento da tabela medico_especialidade
		$sql_especialidade = "DELETE FROM medico_especialidade WHERE medico_id_medico={$_REQUEST["id_medico"]}";
		$res_especialidade = $conn->query($sql_especialidade);

		// Excluir o médico
		$sql = "DELETE FROM medico WHERE id_medico={$_REQUEST["id_medico"]}";
		$res = $conn->query($sql);

		if ($res && $res_especialidade) {
			print "<script>
			alert('Excluiu com sucesso!');
			</script>";
			print "<script>
			location.href = '?page=listar-medico';
			</script>";
		} else {
			print "<script>
			alert('Deu errado');
			</script>";
			print "<script>
			location.href = '?page=listar-medico';
			</script>";
		}
		break;
}