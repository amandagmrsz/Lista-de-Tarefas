<?php
switch ($_REQUEST['acao']) {
    case 'cadastrar':
        $paciente_id = $_POST['paciente_id_paciente'];
        $tipo_atendimento = $_POST['tipo_atendimento'];

        // Inserir dados na tabela recepcao com status 'aguardando_triagem'
        $sql = "INSERT INTO recepcao (paciente_id_paciente, tipo_atendimento, status) 
                VALUES ('{$paciente_id}', '{$tipo_atendimento}', 'Aguardando Triagem')";
        $res = $conn->query($sql);

        if ($res) {
            print "<script>
            alert('Recepção cadastrada com sucesso!');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        } else {
            print "<script>
            alert('Erro ao cadastrar recepção.');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        }
        break;

    case 'editar':
        $paciente_id = $_POST['paciente_id_paciente'];
        $tipo_atendimento = $_POST['tipo_atendimento'];

        // Atualizar dados na tabela recepcao
        $sql = "UPDATE recepcao SET 
                    paciente_id_paciente='{$paciente_id}', 
                    tipo_atendimento='{$tipo_atendimento}' 
                WHERE id_recepcao={$_REQUEST["id_recepcao"]}";
        $res = $conn->query($sql);

        if ($res) {
            print "<script>
            alert('Recepção editada com sucesso!');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        } else {
            print "<script>
            alert('Erro ao editar recepção.');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        }
        break;

    case 'excluir':
        // Excluir dados da tabela recepcao
        $sql = "DELETE FROM recepcao WHERE id_recepcao={$_REQUEST["id_recepcao"]}";
        $res = $conn->query($sql);

        if ($res) {
            print "<script>
            alert('Recepção excluída com sucesso!');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        } else {
            print "<script>
            alert('Erro ao excluir recepção.');
            </script>";
            print "<script>
            location.href = '?page=listar-recepcao';
            </script>";
        }
        break;
}
?>