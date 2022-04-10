function Control() {
    this.contador = 0;
    this.total = 0;
    this.requestSuccess = [];
    this.requestFailed = [];
    this.metaInfo = {};

    this.complete = function() {
        //console.log('complete', 'contador', this.contador);
        console.log('Ação Completa, total', this.total);
        console.log('requestSuccess', this.requestSuccess);
        console.log('requestFailed', this.requestFailed);

        saveLogSendMail(this.requestSuccess, this.requestFailed, this.metaInfo);
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
        message_global.innerHTML = " ";
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

function bloquear_executar_envio() {
    btn_execute.disabled = true;
}

function desbloquear_executar_envio() {
    btn_execute.disabled = false;
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
    controlRequests.metaInfo = dadosToServer;
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


function saveLogSendMail(requestSuccess = [], requestFailed = [], metaInfo = {}) {
    var dadosToServer = {
        registros_enviados: JSON.stringify(requestSuccess),
        registros_nao_enviados: JSON.stringify(requestFailed),
        metaInfo: JSON.stringify(metaInfo)
    };
    ajaxRequest({
        url: "email/ajaxSaveLogSendMail",
        data: dadosToServer,
        success: console.log,
        error: console.log
    });
}

function clearTmpFiles(metaInfo) {
    var upload_now_file = (metaInfo && metaInfo.upload_now_file) ? metaInfo.upload_now_file : [];
    if (typeof upload_now_file !== "string") {
        //garantir de mandar string ao server
        upload_now_file = JSON.stringify(upload_now_file);
    }
    console.log('upload_now_file: ', upload_now_file);
    var dadosToServer = {
        upload_now_file: upload_now_file,

    };
    ajaxRequest({
        url: "email/clearTmpFiles",
        data: dadosToServer,
        success: console.log,
        error: console.log
    });
}

var arquivos_upload_now = document.querySelector(".arquivos_upload_now");
var tipos_anexo = document.querySelector("#tipos_anexo");

tipos_anexo.addEventListener("change", function(e) {
    var alvo = e.currentTarget; //select tipos_anexo
    var values = getSelectValues(alvo);
    if (values.indexOf("3") !== -1) {
        arquivos_upload_now.style.display = "block";
    } else {
        arquivos_upload_now.style.display = "none";
    }

})