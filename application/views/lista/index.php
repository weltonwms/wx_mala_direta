<h4 class="mt-2">LISTA <small class="text-muted">- Tabela com Dados Utilizados pelo Sistema</small></h4>

<form action="<?php echo base_url('lista/loadList') ?>" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <label for="" class="form-label text-warning"><i class="fa-solid fa-triangle-exclamation"></i></label><br>

            <button class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInstrucoes"
                aria-expanded="false" aria-controls="collapseInstrucoes">
                Instruções Para Carregamento da Lista
            </button>


        </div>
        <div class="col-md-4">
            <label for="formFile" class="form-label">Arquivo CSV utilizado pela Lista</label>
            <input class="form-control" type="file" id="formLista" name="formLista" required>
        </div>
        <div class="col-md-4">

            <label for="" class="form-label text-success"><i class="fa-regular fa-circle-check"></i></label><br>
            <button class="btn btn-outline-success width-lg" type="submit">Carregar Lista</button>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="collapse" id="collapseInstrucoes">
                <div class="card card-body">
                    <ul>
                        <li>O Arquivo deve ser uma planilha no Formato CSV. <small class="text-muted">Use o Excel ou
                                Libreoffice para Converter</small></li>
                        <li>A planilha deve conter um Cabeçalho na primeira linha denominando o nome de todas as colunas
                        </li>
                        <li>Não use Acentos, Espaços ou Caracteres Especiais no Cabeçaho. <small
                                class="text-muted">Sugestão: Use underline "_" no lugar de espaços</small> </li>
                        <li>Na linha do cabeçalho não pode haver células em branco. <small class="text-muted">Todas as
                                Colunas devem ser nomeadas</small></li>
                        <li>Pode-se usar caracteres Maiúsculos ou Minúsculos na linha do cabeçalho, porém sugere-se usar
                            tudo Minúsculo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>




</form>


<?php if ($carregamentoLista): ?>
<form action="" method="POST">
    <div class="border border-light border-2 mt-3 p-2">
        <strong>Último Carregamento: </strong><?php echo UtilHelper::dateBr($carregamentoLista['updated_at']); ?>
    </div>

    <br>


    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 text-info d-block d-sm-none">Barra de Ferramentas</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarBarraFerramentas" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarBarraFerramentas">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item ">
                        <a class="btn btn-outline-success me-2 btn-sm " href="<?php echo base_url('Lista/create')?>">
                            <i class="fa fa-plus-circle"></i>
                            Novo</a>


                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-secondary me-2 btn-sm requireChecks" type="button"
                            data-url="<?php echo base_url('Lista/editItem/wx_id')?>" onclick="editItemLista(this)">
                            <i class="fa fa-pencil"></i>
                            Editar</button>
                    </li>



                    <li class="nav-item">
                        <button class="btn btn-outline-secondary me-2 btn-sm requireChecks" type="submit"
                            formaction="<?php echo base_url('Lista/ativar')?>">
                            <i class="fa fa-check text-success"></i>
                            Ativar</button>
                    </li>

                    <li class="nav-item">
                        <button class="btn btn-outline-secondary me-2 btn-sm requireChecks" type="submit"
                            formaction="<?php echo base_url('Lista/inativar')?>">
                            <i class="fa-solid fa-circle-xmark text-danger"></i>
                            Inativar</button>
                    </li>

                    <li class="nav-item">
                        <button class="btn btn-outline-danger me-2 btn-sm requireChecks" type="submit"
                            formaction="<?php echo base_url('Lista/excluir')?>">
                            <i class="fa fa-trash"></i>
                            Excluir</button>
                    </li>



                </ul>

            </div>
        </div>
    </nav>









    <div class="table-responsive">
        <table class="table table-sm font-sm table-bordered table-hover">
            <thead>
                <th><input type="checkbox" class='checkAll'></th>

                <?php foreach ($carregamentoLista['campos'] as $campo): ?>
                <th><?php echo $campo ?></th>
                <?php endforeach;?>
                <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carregamentoLista['dados'] as $dado): ?>
                <tr>
                    <td><input type="checkbox" class="check" name="wx_id[]" value="<?php echo $dado->wx_id?>"></td>

                    <?php foreach ($carregamentoLista['campos'] as $i=>$campo): ?>
                    <?php if($i<2):?>
                    <td><a
                            href="<?php echo base_url("lista/editItem/{$dado->wx_id}") ?>"><?php echo $dado->$campo ?></a>
                    </td>
                    <?php else:?>
                    <td><?php echo $dado->$campo ?></td>
                    <?php endif;?>
                    <?php endforeach;?>
                    <td>
                        <?php echo $dado->wx_ativo?
                        "<button type='button' class='btn btn-light'><i class='fa-solid fa-check text-success'></i></button>":
                        "<button type='button' class='btn btn-light'><i class='fa-solid fa-circle-xmark text-danger'></i></button>"?>

                    </td>
                </tr>

                <?php endforeach;?>

            </tbody>
        </table>
    </div>


</form>

<?php endif;?>

<script>

checkAndUncheck(); //função para selecionar todos os registros e deselecionar

function editItemLista(alvo) {
    var baseUrl = alvo.dataset.url;
    var elementChecked = document.querySelector('input.check:checked'); //pegar 1º elemento checado
    if (elementChecked) {
        var wx_id = elementChecked.value;
        var url = baseUrl.replace('wx_id', wx_id);

        location.href = url;
    }

}
</script>