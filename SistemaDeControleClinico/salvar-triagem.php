<?php
switch ($_REQUEST['acao']) {
    case 'cadastrar':
        // Captura os dados enviados pelo formulário com validação básica
        $paciente_id = $_POST['paciente_id_paciente'] ?? null;
        $especialidade_id = $_POST['encaminhamento'] ?? null;
        $classificacao = $_POST['classificacao_gravidade'] ?? null;
        $temperatura = $_POST['temperatura'] ?? null;
        $pressao = $_POST['pressao_arterial'] ?? null;
        $frequencia = $_POST['frequencia_cardiaca'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;
        $status = $_POST['status'] ?? null;
        $id_recepcao = $_POST['id_recepcao'] ?? null;

        // Verificação obrigatória
        if (!$paciente_id || !$especialidade_id || !$classificacao || !$id_recepcao) {
            die("Erro: Campos obrigatórios não preenchidos.");
        }

        // Insere os dados na tabela triagem
        $sql_triagem = "INSERT INTO triagem (
                            paciente_id_paciente, 
                            especialidade_id_especialidade, 
                            prioridade,  
                            temperatura, 
                            pressao, 
                            frequencia_cardiaca, 
                            observacoes, 
                            classificacao_gravidade
                        ) VALUES (
                            '{$paciente_id}', 
                            '{$especialidade_id}',  
                            '{$classificacao}', 
                            '{$temperatura}', 
                            '{$pressao}', 
                            '{$frequencia}', 
                            '{$observacoes}', 
                            '{$classificacao}'
                        )";

        $res_triagem = $conn->query($sql_triagem);

        if ($res_triagem) {
            // Atualiza o status na tabela recepção
            if ($status) {
                $sql_recepcao = "UPDATE recepcao 
                                 SET status = '{$status}' 
                                 WHERE id_recepcao = '{$id_recepcao}'";
                $res_recepcao = $conn->query($sql_recepcao);

                if (!$res_recepcao) {
                    die("Erro ao atualizar status na recepção: " . $conn->error);
                }
            }

            echo "<script>
                    alert('Triagem cadastrada e status atualizado com sucesso!');
                    location.href = '?page=listar-triagem';
                  </script>";
        } else {
            die("Erro ao cadastrar triagem: " . $conn->error);
        }
        break; 

    case 'editar':
            $id_triagem = $_POST['id_triagem'];
            $pressao = $_POST['pressao_arterial'];
            $frequencia = $_POST['frequencia_cardiaca'];
            $temperatura = $_POST['temperatura'];
            $gravidade = $_POST['classificacao_gravidade'];
            $queixa = $_POST['queixa_principal'];
            $especialidade = $_POST['encaminhamento'];
            $status = $_POST['status']; 
        
            // Atualizar os dados na tabela triagem
            $sql = "UPDATE triagem 
                    SET 
                        pressao = '{$pressao}', 
                        frequencia_cardiaca = '{$frequencia}', 
                        temperatura = '{$temperatura}', 
                        prioridade = '{$gravidade}', 
                        observacoes = '{$queixa}', 
                        especialidade_id_especialidade = '{$especialidade}'
                    WHERE id_triagem = '{$id_triagem}'";
            
            // Executar a consulta na triagem
            $res = $conn->query($sql);
        
            // Agora, atualizar o status na tabela recepcao
            $sql_status = "UPDATE recepcao 
                           SET status = '{$status}' 
                           WHERE paciente_id_paciente = (SELECT paciente_id_paciente FROM triagem WHERE id_triagem = '{$id_triagem}')";
        
            // Executar a consulta de status
            $res_status = $conn->query($sql_status);
        
            if ($res) {
                echo "<script>alert('Triagem atualizada com sucesso!');</script>";
                echo "<script>location.href='?page=listar-triagem';</script>";
            } else {
                echo "<script>alert('Erro ao atualizar triagem!');</script>";
                echo "<script>location.href='?page=editar-triagem&id_triagem={$id_triagem}';</script>";
            }
            break;  
        
                                   

    case 'excluir':
        $triagem_id = $_REQUEST['id_triagem'];

        // Excluir o registro da tabela triagem
        $sql = "DELETE FROM triagem WHERE id_triagem='{$triagem_id}'";
        $res = $conn->query($sql);

        if ($res) {
            print "<script>
            alert('Triagem excluída com sucesso!');
            </script>";
            print "<script>
            location.href = '?page=listar-triagem';
            </script>";
        } else {
            print "<script>
            alert('Erro ao excluir triagem.');
            </script>";
            print "<script>
            location.href = '?page=listar-triagem';
            </script>";
        }
        break;
}
?>