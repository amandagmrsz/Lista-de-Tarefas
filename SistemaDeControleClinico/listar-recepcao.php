<h1>Listar Recepção</h1>
<?php
    $sql = "SELECT r.id_recepcao, p.nome_paciente, r.tipo_atendimento, r.status
            FROM recepcao r
            JOIN paciente p ON r.paciente_id_paciente = p.id_paciente";

    $res = $conn->query($sql);

    $qtd = $res->num_rows;

    if ($qtd > 0) {
        print "<p>Encontrou <b>$qtd</b> resultado(s)</p>";
        print "<table class='table table-bordered table-striped table-hover'>";
        print "<tr>";
        print "<th>#</th>";
        print "<th>Paciente</th>";
        print "<th>Tipo de Atendimento</th>";
        print "<th>Status</th>"; 
        print "<th>Ações</th>";
        print "</tr>";
        
        while ($row = $res->fetch_object()) {
            print "<tr>";
            print "<td>".$row->id_recepcao."</td>";
            print "<td>".$row->nome_paciente."</td>";
            print "<td>".ucwords($row->tipo_atendimento)."</td>"; 
            print "<td>".ucwords($row->status)."</td>"; 
            print "<td>
                        <button class='btn btn-success' onclick=\"location.href='?page=editar-recepcao&id_recepcao=".$row->id_recepcao."';\">Editar</button>

                        <button class='btn btn-danger' onclick=\"if(confirm('Tem certeza que deseja excluir?')){location.href='?page=salvar-recepcao&acao=excluir&id_recepcao=".$row->id_recepcao."';}else{false;}\">Excluir</button>
                   </td>";
            print "</tr>";
        }
        print "</table>";
    } else {
        print "<p>Não encontrou resultado</p>";
    }
?>