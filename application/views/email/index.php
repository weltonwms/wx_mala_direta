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

<form method="POST" id="form_disparo" action="<?php echo base_url("email/disparar");?>" enctype="multipart/form-data">

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
                <label class="form-label">Anexos</label>
                <select class="form-select" name="tipos_anexo[]" id="tipos_anexo" multiple>

                    <option value="1">Doc Gerado Mala direta</option>
                    <option value="2">Doc Gerado PDF</option>
                    <option value="3">Upload Agora</option>
                </select>
            </div>
        </div>
        <div class="col-md-8 arquivos_upload_now" style="display:none">
            <label for="upload_now_file" class="form-label">Arquivos Upload Agora</label>
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
        <button type="submit" class="btn btn-primary" id="executar_envio">Executar Envio</button>
    </div>

</form>
<?php endif;?>

<div id="loading">
</div>




<div id="emailResults">
</div>


<script>
function Control() {
    this.contador = 0;
    this.total = 0;
    this.requestSuccess = [];
    this.requestFailed = [];
    this.metaInfo={};

    this.complete = function() {
        //console.log('complete', 'contador', this.contador);
        console.log('Ação Completa, total', this.total);
        console.log('requestSuccess', this.requestSuccess);
        console.log('requestFailed', this.requestFailed);

        saveLogSendMail(this.requestSuccess,this.requestFailed, this.metaInfo);
        clearTmpFiles(this.metaInfo); //Arquivos Upload_now_file não é mais necessário
        var text = "<div class='alert alert-info'>" + this.total + " Requisições Completadas</div>";
        var loading = document.querySelector("#loading");
        loading.innerHTML = text;
        desbloquear_executar_envio();
    }
    this.init = function(total) {
        this.total = total;
        this.contador = 0;
        this.requestSuccess = [];
        this.requestFailed = [];
        var text = '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">' +
            '<span class="visually-hidden">Loading...</span>' +
            '</div>';
        text += ' <div class="text-status">Carregando!</div>' +
            '<div class="progress" style="height: 30px;">' +
            '<div class="progress-bar progress-bar-striped progress-bar-animated" id="barra-progresso" ><span class="text-percent">0%</span></div>' +
            '</div>';
        var loading = document.querySelector("#loading");
        loading.innerHTML = text;
        //limpar também mensagens globais anteriores.
        var message_global = document.querySelector(".message_global");
        message_global.innerHTML=" ";
        bloquear_executar_envio();
    }
    this.add = function() {
        this.contador++;
        var pc = (100 / this.total) * this.contador;
        var barra_progresso = document.querySelector("#barra-progresso");
        var text_percent = document.querySelector(".text-percent");

        barra_progresso.style.width = pc + '%';
        text_percent.innerHTML = pc.toFixed(0) + '%';

        if (this.contador === this.total) {
            this.complete();
        }
    }
    this.computarResultado = function(request, status) {
        if (status) {
            this.requestSuccess.push(request);
        } else {
            this.requestFailed.push(request);
        }
    }

} //fim da Classe de Controle

var controlRequests = new Control();
var btn_execute = document.querySelector("#executar_envio");

function bloquear_executar_envio(){
    btn_execute.disabled=true;
}

function desbloquear_executar_envio(){
    btn_execute.disabled=false;
}

btn_execute.addEventListener('click', function(event) {
    event.preventDefault();
    ajaxSendForm({
        url: 'email/ajaxDisparo',
        form: '#form_disparo',
        success: function(resp) {
            var resposta = JSON.parse(resp);
            //console.log(resposta);
            executeAll(resposta);
        },
        error: function(resp) {
            var resposta = JSON.parse(resp);
            showAlert(resposta.message, '.message_global', 'danger');

        }
    });

});

function executeAll(serverResposta) {
    var lista = serverResposta.lista;
    var countLista = lista.length;
    var dadosToServer = serverResposta;
    var email_password = document.querySelector('input[name=email_password]');
    dadosToServer.email_password = email_password.value; //mandar para o server a senha do email
    delete dadosToServer.lista; //desnecessário mandar a lista para o server
    dadosToServer.tipos_anexo = JSON.stringify(dadosToServer
    .tipos_anexo); //todos dados enviados precisam estar em String
    dadosToServer.upload_now_file = JSON.stringify(dadosToServer
    .upload_now_file); //todos dados enviados precisam estar em String
    console.log("Total De Requests a fazer", countLista)
    //console.log('vai ir ao ajaxSendMail', dadosToServer);
    var emailResults = document.querySelector("#emailResults");
    emailResults.innerHTML = " ";

    controlRequests.init(countLista);
    controlRequests.metaInfo=dadosToServer;
    lista.forEach(function(itemList) {
        dadosToServer.item = JSON.stringify(itemList); //server precisa do item corrente da lista
        ajaxRequest({
            url: "email/ajaxSendMail",
            data: dadosToServer,
            success: function(resp) {
                var obj = {
                    item: itemList
                };
                controlRequests.computarResultado(obj, true);
                console.log(resp);
                apppendResult(resp.message, true, itemList);

            },
            error: function(resp) {
                var obj = {
                    item: itemList,
                    erro: resp
                };
                controlRequests.computarResultado(obj, false);
                console.log('erro ', resp);
                motivoErro = resp.error;
                apppendResult(resp.message, false, itemList, motivoErro);
            },
            complete: function() {
                controlRequests.add();
            }
        });



    }); //fim forEach
} //FIM executeAll()

function apppendResult(result, status, itemObj = {}, motivoErro = null) {
    var emailResults = document.querySelector("#emailResults");
    var idCollapse = "colapse_" + Math.floor(Date.now() * Math.random())
    var divCollapse = document.createElement('div');
    divCollapse.id = idCollapse;
    divCollapse.className = "collapse";
    divCollapse.style = "color:#bbb;padding:5px;margin-bottom:10px";
    var textoInfo = "";
    if (motivoErro) {
        textoInfo += "<div><strong>Motivo Erro: " + motivoErro + "</strong></div>";
    }
    textoInfo += (typeof itemObj === 'object' && itemObj !== null) ? JSON.stringify(itemObj) : itemObj;
    divCollapse.innerHTML = textoInfo;
    var div = document.createElement('div');
    if (status) {
        div.className = "text-success";
        var link = '  <a href="#" style="color:inherit"  data-bs-toggle="collapse" data-bs-target="#' + idCollapse +
            '" aria-expanded="false">';
        link += "<i class='fa-solid fa-eye'></i></a>";
        div.innerHTML = "<i class='fa-solid fa-check'></i> " + result + link;


    } else {
        div.className = "text-danger";
        var link = '  <a href="#" style="color:inherit"  data-bs-toggle="collapse" data-bs-target="#' + idCollapse +
            '" aria-expanded="false">';
        link += "<i class='fa-solid fa-eye'></i></a>";
        div.innerHTML = "<i class='fa-regular fa-circle-xmark'></i> " + result + link;

    }
    div.appendChild(divCollapse);
    emailResults.appendChild(div);

}


function saveLogSendMail(requestSuccess = [],requestFailed = [], metaInfo={}){
    var dadosToServer={
        registros_enviados:JSON.stringify(requestSuccess),
        registros_nao_enviados:JSON.stringify(requestFailed),
        metaInfo:JSON.stringify(metaInfo)
    };
    ajaxRequest({
            url: "email/ajaxSaveLogSendMail",
            data: dadosToServer,
            success: console.log,
            error: console.log
        });
}

function clearTmpFiles(metaInfo){
    var upload_now_file= (metaInfo && metaInfo.upload_now_file)?metaInfo.upload_now_file:[];
    if(typeof upload_now_file !== "string"){
        //garantir de mandar string ao server
        upload_now_file=JSON.stringify(upload_now_file); 
    }
    console.log('upload_now_file: ', upload_now_file);
    var dadosToServer={
        upload_now_file:upload_now_file,
       
    };
    ajaxRequest({
            url: "email/clearTmpFiles",
            data: dadosToServer,
            success: console.log,
            error: console.log
        });
}

var arquivos_upload_now=document.querySelector(".arquivos_upload_now");
var tipos_anexo=document.querySelector("#tipos_anexo");

tipos_anexo.addEventListener("change",function(e){
    var alvo=e.currentTarget; //select tipos_anexo
    var values= getSelectValues(alvo);
    if(values.indexOf("3") !==-1){ 
        arquivos_upload_now.style.display="block";
    }
    else{
        arquivos_upload_now.style.display="none";
    }
    
})

</script>