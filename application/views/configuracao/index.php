<h4 class="mt-2">
    Configurações <small class="text-muted">- Informações Gerais Utilizadas pelo Sistema</small>
</h4>
<?php
$headCampos=isset($head_lista->campos)?$head_lista->campos:[];
$campo_identificador=isset($head_lista->campo_identificador)?$head_lista->campo_identificador:'';
$campo_email=isset($head_lista->campo_email)?$head_lista->campo_email:'';
?>
<form action="<?php echo base_url('configuracao/save')?>" method="post" class="formConfig">
    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-success">Salvar Configuração</button>
        </div>
    </div>


    <br>
    <div class="row">
        <div class="col-md-6">
            <fieldset>
                <legend class="legend_destaque text-muted">Lista</legend>



                <div class="row mb-3">
                    <label for="" class="col-sm-3 col-form-label">Identificador Ùnico</label>
                    <div class="col-sm-9">
                        <select name="campo_identificador" id="campo_identificador" class="form-control form-control-sm"
                            required>
                            <option value="">--Selecione--</option>
                            <?php foreach($headCampos as $campo):?>
                            <option value="<?php echo $campo?>"
                                <?php echo $campo==$campo_identificador?'selected':'' ?>>
                                <?php echo $campo?>
                            </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm-3 col-form-label">Coluna do Email</label>
                    <div class="col-sm-9">
                        <select name="campo_email" id="campo_email" class="form-control form-control-sm">
                            <option value="">--Selecione--</option>
                            <?php foreach($headCampos as $campo):?>
                            <option value="<?php echo $campo?>" <?php echo $campo==$campo_email?'selected':'' ?>>
                                <?php echo $campo?>
                            </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>



            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset>
                <legend class="legend_destaque text-muted">Email</legend>
                <div class="row mb-2">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">Email Remetente</label>
                    <div class="col-sm-9">
                        <input type="email" name="email_from"
                            value="<?php echo isset($config->email_from)?$config->email_from:''?>"
                            class="form-control form-control-sm" id="email_from">
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">Nome Remetente</label>
                    <div class="col-sm-9">
                        <input type="text" name="email_from_name"
                            value="<?php echo isset($config->email_from_name)?$config->email_from_name:''?>"
                            class="form-control form-control-sm" id="email_from_name">
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">SMTP Host</label>
                    <div class="col-sm-9">
                        <input type="text" name="smtp_host"
                            value="<?php echo isset($config->smtp_host)?$config->smtp_host:''?>"
                            class="form-control form-control-sm" id="smtp_host">
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">SMTP Porta</label>
                    <div class="col-sm-9">
                        <input type="number" name="smtp_port"
                            value="<?php echo isset($config->smtp_port)?$config->smtp_port:''?>"
                            class="form-control form-control-sm" id="smtp_port">
                    </div>
                </div>

                <div class="row mb-2 blocoPassword" style="display:none">
                    <label for="" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" name="email_password" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row mb-2">
                            <div class="msg_mail_teste"></div>
                </div>

                <div class="row mb-2 mt-4">
                    <div class="">
                        <button type="button" id='btn_send_mail_test' class="btn btn-outline-secondary btn-sm">
                            Enviar Email Teste
                        </button>
                    </div>

                </div>


            </fieldset>
        </div>
    </div>

</form>
<script>
var btn_send_mail_test = document.querySelector('#btn_send_mail_test');
btn_send_mail_test.addEventListener('click', function() {
    var blocoPassword = document.querySelector(".blocoPassword");
    blocoPassword.style.display = "flex";
    var fieldsNotAssign=fieldsNotAssignToSendMail();
    console.log(fieldsNotAssign)
    if(fieldsNotAssign.length > 0){
        var msg="<p>Preencha os Campos</p>";
        msg+='<ul>';
        fieldsNotAssign.forEach(function(field){
            msg+="<li>"+field+'</li>';
        });
        msg+='</ul>';
        var div_msg=document.querySelector('.msg_mail_teste');
        div_msg.innerHTML=msg;
        return false;
    }
    var fieldsToSendMail = getFieldsToSendMail();
    ajaxRequest(
        {
            url:"configuracao/ajaxTesteMail",
            data:fieldsToSendMail,
            method:'GET',
            success:function(resp){
                console.log(resp);
            },
            error:function(xhr){
                console.log('deu ruim');
            }
        }
    )

   
});

function fieldsNotAssignToSendMail() {
    var fieldsToSendMail = getFieldsToSendMail();
    var fieldsNotAssign = [];
    for (let key in fieldsToSendMail) {
        let value = fieldsToSendMail[key];
        if (!value) {
            fieldsNotAssign.push(key);
        }
        // Use `key` and `value`
    }
    return fieldsNotAssign;

}

function getFieldsToSendMail() {
    var obj = {};
    var fields = ['smtp_host', 'smtp_port', 'email_from', 'email_from_name', 'email_password'];
    fields.forEach(function(field) {
        //console.log(field);
        obj[field] = document.querySelector('[name=' + field + ']').value;
    });
    return obj;
}
</script>