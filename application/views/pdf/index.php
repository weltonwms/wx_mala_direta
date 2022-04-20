<h5 class="mt-4 mb-4">PDF <small class="text-muted">- Conversão de Documentos da Mala Direta para PDF</small>
</h5>


<div class="row">

    <div class="col-md-6">

    </div>
    <div class="col-md-2"></div>
    <div class="col-md-4">


        <button class="btn btn-outline-success width-lg" id="executar_conversao">
            <i class="fa-regular fa-circle-check"></i>
            Converter Documentos
        </button>

    </div>
</div>


<div class="message_global mt-3"></div>

<input type="hidden" id="files_manager_pdf" value='<?php echo json_encode($files_pdf) ?>'>

<form action="" method="POST" class="manager_files">
    <div class="card">
        <div class="card-header text-end">
            <a class="btn btn-success me-2 btn-sm " href="<?php echo base_url('pdf/downloadAll')?>">
                <i class="fa-solid fa-download"></i>
                Download Todos Arquivos
            </a>

            <button class="btn btn-outline-danger me-2 btn-sm requireChecks" type="submit"
                formaction="<?php echo base_url('pdf/excluir')?>">
                <i class="fa fa-trash"></i>
                Excluir
            </button>


        </div>
        <div class="card-body">
            <div class="row" style="padding:5px">
                <div class="col-md-12">
                    <input type="checkbox" class="checkAll" name="" value="" style="margin-right:10px;">
                    <label for="" class="form-label">Selecionar Todos</label>
                </div>
            </div>
            <div class="box_files">
                <!-- conteúdo colocado via javascript -->

            </div>
        </div>
    </div>
</form>






<br>
<div id="loading">
</div>


<div id="pdfResults">
</div>
<br>

<script>
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
        getFilesNotConverted(alertNotConvert);
        getFilesPdf(taskOnFinishConvert);
        var text = "<div class='alert alert-info'>" + this.total + " Requisições Completadas</div>";
        var loading = document.querySelector("#loading");
        loading.innerHTML = text;
        desbloquear_executar_envio();
        mostrar_file_manager();
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
        esconder_file_manager();
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
var btn_execute = document.querySelector("#executar_conversao");
var manager_files = document.querySelector(".manager_files");

function bloquear_executar_envio() {
    btn_execute.disabled = true;
}

function desbloquear_executar_envio() {
    btn_execute.disabled = false;
}

function esconder_file_manager() {
    manager_files.style.display = "none";
}

function mostrar_file_manager() {
    manager_files.style.display = "block";
}

btn_execute.addEventListener('click', function(event) {
    event.preventDefault();
    ajaxRequest({
        url: 'pdf/ajaxDisparo',
        success: function(resp) {
            var resposta = resp;
            console.log(resposta);
            executeAll(resposta);
        },
        error: function(resp) {
            var resposta = resp;
            showAlert(resposta.message, '.message_global', 'danger');

        }
    });

});

function executeAll(serverResposta) {
    var lista = serverResposta.lista;
    var countLista = lista.length;

    console.log("Total De Requests a fazer", countLista)

    var pdfResults = document.querySelector("#pdfResults");
    pdfResults.innerHTML = " ";
    dadosToServer = {};

    controlRequests.init(countLista);

    lista.forEach(function(itemList) {
        dadosToServer.file = itemList;
        ajaxRequest({
            url: "pdf/ajaxConvertFile",
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
                    erro: ""
                };
                controlRequests.computarResultado(obj, false);
                console.log('erro ', resp);
                //motivoErro = resp.error;
                apppendResult(resp.message, false, itemList, {});
            },
            complete: function() {
                controlRequests.add();
            }
        });



    }); //fim forEach
} //FIM executeAll()

function apppendResult(result, status, itemObj = {}, motivoErro = null) {
    var pdfResults = document.querySelector("#pdfResults");
    var div = document.createElement('div');

    if (status) {
        div.className = "text-success";
        var link = '  <a href="#" style="color:inherit"   ' +
            '" aria-expanded="false">';
        link += "<i class='fa-solid fa-eye'></i></a>";
        div.innerHTML = "<i class='fa-solid fa-check'></i> " + result;


    } else {
        div.className = "text-danger";
        var link = '  <a href="#" style="color:inherit"   ' +
            '" aria-expanded="false">';
        link += "<i class='fa-solid fa-eye'></i></a>";
        div.innerHTML = "<i class='fa-regular fa-circle-xmark'></i> " + result;

    }

    pdfResults.appendChild(div);

}

function getFilesNotConverted(callbackSuccess) {
    ajaxRequest({
        url: "pdf/ajaxFilesNotConverted",
        method: "GET",
        success: callbackSuccess,
        error: console.log
    });
}

function getFilesPdf(callbackSuccess) {
    ajaxRequest({
        url: "pdf/ajaxGetFilesPdf",
        method: "GET",
        success: callbackSuccess,
        error: console.log
    });
}

function alertNotConvert(lista) {
    if (Array.isArray(lista) && lista.length) {
        var string = "<strong>Os seguintes Arquivos não foram Convertidos:</strong><br>";
        string += lista.join("<br>");
        showAlert(string, '.message_global', 'danger');
    }
}

function taskOnFinishConvert(lista) {
    var input = document.querySelector("#files_manager_pdf");
    input.value = JSON.stringify(lista);
    mountFilesManager();
}

function mountFilesManager() {
    var input = document.querySelector("#files_manager_pdf");
    var box = document.querySelector(".box_files");
    var files = JSON.parse(input.value);
    if (!files.length) {

    }

    var filesMap = files.map(function(file) {
        var urlDownlod = "pdf/download/" + file;
        var string = '<div class="row_file row">' +
            '<div class="col-md-12">' +
            '<input type="checkbox" class="check" name="files[]" ' +
            'value="' + file + '" style="margin-right:10px">' +
            '<a href="' + urlDownlod + '" style="text-decoration: none">' +
            '<i class="fa-solid fa-file-pdf text-danger"></i>' +
            '&nbsp; &nbsp;' + file +
            '</a>' +
            '</div>' +
            '</div>';
        return string;
    });

    box.innerHTML = filesMap.join(' ');
    checkAndUncheck(); //função para selecionar todos os registros e deselecionar
}

mountFilesManager();
</script>