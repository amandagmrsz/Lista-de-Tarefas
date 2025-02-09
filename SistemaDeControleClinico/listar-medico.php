<h1>Listar Médicos</h1>
<?php
    // Consulta para trazer médicos com suas especialidades
    $sql = "SELECT m.id_medico, m.nome_medico, m.crm_medico, e.nome_especialidade 
            FROM medico m
            LEFT JOIN medico_especialidade me ON m.id_medico = me.medico_id_medico
            LEFT JOIN especialidade e ON me.especialidade_id_especialidade = e.id_especialidade";

    $res = $conn->query($sql);

    $qtd = $res->num_rows;

    if ($qtd > 0) {
        print "<p>Encontrou <b>$qtd</b> resultado(s)</p>";
        print "<table class='table table-bordered table-striped table-hover'>";
        print "<tr>";
        print "<th>#</th>";
        print "<th>Nome</th>";
        print "<th>CRM</th>";
        print "<th>Especialidade</th>";
        print "<th>Ações</th>";
        print "</tr>";
        
        while ($row = $res->fetch_object()) {
            print "<tr>";
            print "<td>".$row->id_medico."</td>";
            print "<td>".$row->nome_medico."</td>";
            print "<td>".$row->crm_medico."</td>";
            print "<td>".$row->nome_especialidade."</td>";
            print "<td>
                        <button class='btn btn-success' onclick=\"location.href='?page=editar-medico&id_medico=".$row->id_medico."';\">Editar</button>

                        <button class='btn btn-danger' onclick=\"if(confirm('Tem certeza que deseja excluir?')){location.href='?page=salvar-medico&acao=excluir&id_medico=".$row->id_medico."';}else{false;}\">Excluir</button>
                   </td>";
            print "</tr>";
        }
        print "</table>";
    } else {
        print "<p>Não encontrou resultado</p>";
    }
?>