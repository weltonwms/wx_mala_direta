<h5 class="mt-4 mb-5">
    Email <small class="text-muted">- Disparo de Email baseado na Lista e Documentos Gerados</small>
</h5>

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
<div class="message_global">


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

<form method="POST" id="form_disparo" action="" enctype="multipart/form-data">

    <div class="row mb-2">
        <div class="col">
            <div class="">
                <label class="form-label">Email Remetente</label>
                <input type="email" class="form-control form-control-sm" value="<?php echo $config->email_from?>"
                    readonly="readonly">

            </div>
        </div>
        <div class="col">
            <div class="">
                <label for="email_password" class="form-label">Senha Email</label>
                <input type="password" name="email_password" class="form-control form-control-sm" id="email_password">
            </div>
        </div>
        <div class="col">
            <div class="">
                <label class="form-label">Nome Remetente</label>
                <input type="text" class="form-control form-control-sm" value="<?php echo $config->email_from_name?>"
                    readonly="readonly">
            </div>
        </div>
    </div>
    <div class="mb-2">
        <label for="assunto" class="form-label">Assunto</label>
        <input type="text" name="assunto" class="form-control form-control-sm" id="assunto">
    </div>

    <div class="row mb-2">
        <div class="col-md-4">
            <div class="">
                <label class="form-label" data-bs-toggle="tooltip"
                title="Segure Ctrl para selecionar mais de um">
                Anexos
                </label>
                <select class="form-select" name="tipos_anexo[]" id="tipos_anexo" multiple>

                    <option value="1">Doc Gerado Mala direta</option>
                    <option value="2">Doc Gerado PDF</option>
                    <option value="3">Upload Agora</option>
                </select>
            </div>
        </div>
        <div class="col-md-8 arquivos_upload_now" style="display:none">
            <label for="upload_now_file" data-bs-toggle="tooltip"
            title="Segure Ctrl na seleção de arquivos para escolher mais de um"
            class="form-label">
            Arquivos Upload Agora</label>
            <input name="upload_now_file[]" class="form-control form-control-sm" id="upload_now_file" type="file"
                multiple>
        </div>
    </div>




    <div class="mb-3">
        <label for="corpo" class="form-label">Corpo do Email</label>
        <textarea class="form-control form-control-sm" name="corpo" id="corpo" rows="3"></textarea>
        <div id="" class="form-text">Pode-se Usar Marcações HTML.</div>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-success" id="executar_envio">Executar Envio</button>
    </div>

</form>
<?php endif;?>

<div id="loading">
</div>


<div id="emailResults">
</div>
<!--
Toda a Lógica de envio está em send_mail.js
-->
<script src="<?php echo base_url('assets/js/send_mail.js')?>"> </script>