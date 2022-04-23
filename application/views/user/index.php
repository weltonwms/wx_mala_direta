<h5 class="mt-4 mb-4">Usuários <small class="text-muted">- Usuários do Sistema</small></h5>

<?php
function nomePerfil($cod_perfil){
    $nomes=["",'Administrador',"Usuário Comum"];
   return isset($nomes[$cod_perfil])?$nomes[$cod_perfil]:"";
}

?>



<form action="" method="POST">


    <br>


    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 text-success d-block d-sm-none">Barra de Ferramentas</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarBarraFerramentas" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarBarraFerramentas">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item ">
                        <a class="btn btn-outline-success me-2 mb-2 btn-sm width-lg-sm"
                            href="<?php echo base_url('User/create') ?>">
                            <i class="fa fa-plus-circle"></i>
                            Novo</a>


                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-secondary me-2 mb-2 btn-sm width-lg-sm requireChecks"
                            type="button" data-url="<?php echo base_url('User/edit/id') ?>" onclick="editUser(this)">
                            <i class="fa fa-pencil"></i>
                            Editar</button>
                    </li>





                    <li class="nav-item">
                        <button class="btn btn-outline-danger me-2 mb-2 btn-sm width-lg-sm requireChecks" type="submit"
                            formaction="<?php echo base_url('User/excluir') ?>" onclick="return confirm('Tem certeza que deseja Excluir?')">
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
                <tr>
                    <th><input type="checkbox" class='checkAll'></th>
                    <th>Username</th>
                    <th>Nome</th>
                    <th>Perfil</th>


                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><input type="checkbox" class="check" name="ids[]" value="<?php echo $user->id ?>"></td>


                    <td>
                        <a href="<?php echo base_url("user/edit/{$user->id}") ?>">
                            <?php echo $user->username ?>
                        </a>
                    </td>
                    <td><?php echo $user->name ?></td>
                    <td><?php echo nomePerfil($user->perfil) ?></td>

                </tr>

                <?php endforeach;?>

            </tbody>
        </table>
    </div>


</form>



<script>
checkAndUncheck(); //função para selecionar todos os registros e deselecionar

function editUser(alvo) {
    var baseUrl = alvo.dataset.url;
    var elementChecked = document.querySelector('input.check:checked'); //pegar 1º elemento checado
    if (elementChecked) {
        var id = elementChecked.value;
        var url = baseUrl.replace('id', id);

        location.href = url;
    }



}
</script>