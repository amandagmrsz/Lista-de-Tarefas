<h1>Listar Triagens</h1>
<?php
    $sql = "SELECT t.id_triagem, p.nome_paciente, t.prioridade, t.temperatura, t.pressao, t.frequencia_cardiaca, 
        t.observacoes, e.nome_especialidade, r.status
        FROM triagem t
        LEFT JOIN paciente p ON t.paciente_id_paciente = p.id_paciente
        LEFT JOIN especialidade e ON t.especialidade_id_especialidade = e.id_especialidade
        LEFT JOIN recepcao r ON r.paciente_id_paciente = p.id_paciente"; 

        $res = $conn->query($sql);
        $qtd = $res->num_rows;

        if ($qtd > 0) {
            print "<p>Encontrou <b>$qtd</b> resultado(s)</p>";
            print "<table class='table table-bordered table-striped table-hover'>";
            print "<tr>";
            print "<th>#</th>";
            print "<th>Nome</th>";
            print "<th>Prioridade</th>";
            print "<th>Temperatura</th>";
            print "<th>Pressão</th>";
            print "<th>Frequência Cardíaca</th>";
            print "<th>Observações</th>";
            print "<th>Especialidade</th>";
            print "<th>Status</th>";
            print "<th>Ações</th>";
            print "</tr>";

            while ($row = $res->fetch_object()) {
                print "<tr>";
                print "<td>".$row->id_triagem."</td>";
                print "<td>".$row->nome_paciente."</td>";
                print "<td>".$row->prioridade."</td>";
                print "<td>".$row->temperatura."</td>";
                print "<td>".$row->pressao."</td>";
                print "<td>".$row->frequencia_cardiaca."</td>";
                print "<td>".$row->observacoes."</td>";
                print "<td>".$row->nome_especialidade."</td>";
                print "<td>".$row->status."</td>"; 
                print "<td>
                            <button class='btn btn-success' onclick=\"location.href='?page=editar-triagem&id_triagem=".$row->id_triagem."';\">Editar</button>
                            <button class='btn btn-danger' onclick=\"if(confirm('Tem certeza que deseja excluir?')){location.href='?page=salvar-triagem&acao=excluir&id_triagem=".$row->id_triagem."';}else{false;}\">Excluir</button>
                    </td>";
                print "</tr>";
            }
            print "</table>";
        } else {
            print "<p>Não encontrou resultado</p>";
        }

?>