<h5 class="mt-4 mb-4">Usuários <small class="text-muted">- Edição/Novo</small></h5>

<div class="row ">
    <div class="col-md-6">
        <form method="POST" action="<?php echo base_url('user/save')?>">
            <input type="hidden" name="id" value="<?php echo isset($user->id)?$user->id:''?>">

            <div class="row mb-2">
                <label for="username" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" name="username" value="<?php echo isset($user->username)?$user->username:''?>"
                        class="form-control form-control-sm" id="username" required>
                </div>
            </div>

            <div class="row mb-2">
                <label for="name" class="col-sm-2 col-form-label">Nome</label>
                <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($user->name)?$user->name:''?>"
                        class="form-control form-control-sm" id="name" required>
                </div>
            </div>

            <div class="row mb-2">
                <label for="name" class="col-sm-2 col-form-label">Senha</label>
                <div class="col-sm-10">
                    <input type="password" name="password" class="form-control form-control-sm" id="password">
                </div>
            </div>

            <div class="row mb-2">
                <label for="perfil" class="col-sm-2 col-form-label">Perfil</label>
                <div class="col-sm-10">
                    <select name="perfil" id="perfil" class="form-control form-control-sm" required>
                        <option value="">--Selecione--</option>
                        <option value="1" <?php echo (isset($user)&& $user->perfil==1)?'selected':''?>>Administrador
                        </option>
                        <option value="2" <?php echo (isset($user)&& $user->perfil==2)?'selected':''?>>Usuário Comum
                        </option>
                    </select>

                </div>
            </div>

            <div class="row mt-4 ">
                <div>
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <a href="<?php echo base_url('user')?>" class="btn btn-secondary">Voltar</a>
                </div>

            </div>



        </form>
    </div>
</div>