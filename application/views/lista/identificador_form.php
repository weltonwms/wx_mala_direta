<h4 class="alert-heading text-center">Lista Carregada Com Sucesso</h4>
<hr>
<p class="text-muted">
    Escolha dentre as Colunas o Identificador e Email.
    Sugere-se como identificador algo único como o cpf,
    mas pode ser qualquer código de matrícula, número de placa, etc. O 
    Objetivo é identificar um registro como único.
    Se na Lista não houver coluna contendo email não será possível Disparar
    email posteriormente.
</p>
<p class="text-muted">
    Caso queira essas informações podem ser configuradas posteriormente nas Configurações.
</p>
<form action="<?php echo base_url('lista/setIdentificador') ?>" method="post" class="col-md-6 offset-md-3">
    <div class="row">
        <label for="" class="col-sm-4 col-form-label">Identificador Ùnico</label>
        <div class="col-sm-8">
            <select name="campo_identificador" id="" class="form-control form-control-sm" required>
                <option value="">--Selecione--</option>
                <?php foreach($headCampos as $campo):?>
                <option value="<?php echo $campo?>"><?php echo $campo?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <div class="row">
        <label for="" class="col-sm-4 col-form-label">Coluna do Email</label>
        <div class="col-sm-8">
            <select name="campo_email" id="" class="form-control form-control-sm">
            <option value="">--Selecione--</option>
                <?php foreach($headCampos as $campo):?>
                <option value="<?php echo $campo?>"><?php echo $campo?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <div class="row">
        <button type="submit" class="btn btn-primary">Salvar Configuração</button>
    </div>




</form>