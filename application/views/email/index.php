<h4 class="mt-2">
    Email <small class="text-muted">- Disparo de Email baseado na Lista e Documentos Gerados</small>
</h4>

<button class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInstrucoes"
    aria-expanded="false" aria-controls="collapseInstrucoes">
    Instruções Para Envio de Email
</button>
<div class="row">
    <div class="col-md-12">
        <div class="collapse" id="collapseInstrucoes">
            <div class="card card-body">
                <ul>
                    <li>Deve haver uma lista carregada. </li>
                    <li>O campo identificador de email deve estar configurado. </li>
                    <li>Se escolher enviar Anexo com doc enviado da Mala direta,
                        então deve haver documentos na mala direta.
                    </li>
                    <li>Se escolher enviar Anexo com doc PDF,
                        então deve haver documentos gerados em PDF.
                    </li>
                    <li>
                        Por segurança a senha do remetente não será salva no sistema,
                        sendo necessário preecher senha a cada execução em massa.
                    </li>


                </ul>
            </div>
        </div>
    </div>
</div>
<br><br>
<?php if($configsAusentes):?>
<div class="alert alert-danger">
    <h4>
        Existem Configurações Pendentes
        <small class="">(Indispensáveis para Enviar Email)</small>
    </h4>
    <p>Cheque no menu
        <a href="<?php echo base_url('configuracao')?>">
            Configurações</a> as pendências abaixo:
    </p>
    <ul>
        <?php foreach($configsAusentes as $configsAusente):?>
        <li><?php echo $configsAusente?></li>
        <?php endforeach;?>
    </ul>
</div>

<?php else:?>

<form>

    <div class="row mb-2">
        <div class="col">
            <div class="">
                <label for="" class="form-label">Email Remetente</label>
                <input type="email" class="form-control form-control-sm" id=""
                value="<?php echo $config->email_from?>"
                   readonly="readonly">

            </div>
        </div>
        <div class="col">
            <div class="">
                <label for="" class="form-label">Senha Email</label>
                <input type="password" class="form-control form-control-sm" id="exampleInputPassword1">
            </div>
        </div>
        <div class="col">
            <div class="">
                <label for="" class="form-label">Nome Remetente</label>
                <input type="text" class="form-control form-control-sm" id=""
                value="<?php echo $config->email_from_name?>"
                readonly="readonly">
            </div>
        </div>
    </div>
    <div class="mb-2">
        <label for="" class="form-label">Assunto</label>
        <input type="text" class="form-control form-control-sm" id="">
    </div>

    <div class="row mb-2">
        <div class="col-md-4">
            <div class="">
                <label for="" class="form-label">Anexos</label>
                <select class="form-select" multiple aria-label="multiple select example">

                    <option value="1">Doc Gerado Mala direta</option>
                    <option value="2">Doc Gerado PDF</option>
                    <option value="3">Upload Agora</option>
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <label for="formFileSm" class="form-label">Small file input example</label>
            <input class="form-control form-control-sm" id="formFileSm" type="file" multiple>
        </div>
    </div>




    <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Corpo do Email</label>
        <textarea class="form-control form-control-sm" id="exampleFormControlTextarea1" rows="3"></textarea>
        <div id="" class="form-text">Pode-se Usar Marcações HTML.</div>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Executar Envio</button>
    </div>

</form>
<?php endif;?>