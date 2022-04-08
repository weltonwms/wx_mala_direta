//função útil para selecionar ou deselecionar vários itens para submissão.
//No Html precisa ter um checkbox com a classe .checkAll (responsável por selecionar todos)
//Todos os checkbox dos itens precisam ter a classe .check
//Os botões que submetem algo podem ter a classe .requireChecks caso queira impedir
//submissão quando não há nenhum item selecionado.
function checkAndUncheck() {
    var checkAll = document.querySelector('.checkAll');
    var checks = document.querySelectorAll('.check');
    if (!checkAll)
    {
        return false;
    }

    checkAll.addEventListener('click', function (e) {
        var target = e.currentTarget;

        if (target.checked)
        {

            checks.forEach(function (el) {

                el.checked = true;
            })
        } else
        {
            checks.forEach(function (el) {

                el.checked = false;
            })
        }

    });

    checks.forEach(function (el) {
        el.addEventListener('click', function (e) {
            var target = e.currentTarget;
            if (!target.checked && checkAll.checked)
            {

                checkAll.checked = false;
            }

        })
    })

    function requireChecks(event) {
        var elementsChecked = document.querySelectorAll("input.check:checked");
        if (elementsChecked.length < 1)
        {
            event.preventDefault();
            alert("Nenhum Elemento Selecionado");
        }

    }
    //O buttons que submeterem algo se tiverem a classe .requireChecks Só 
    //serão submetidos se houver algum check selecinado.
    var btnsRequireChecks = document.querySelectorAll('.requireChecks');
    btnsRequireChecks.forEach(function (btn) {
        btn.addEventListener('click', requireChecks);
    });
}
//***************************************************/
//fim checkAndUncheck()

/*
 **********************************************
 *Inicio ajaxRequest 
 */
function ajaxRequest(metaRequest) {
    var meta = {
        'url': '',
        'method': 'POST',
        'data': {},
        'success': function () { },
        'error': function () { },
        'beforeSend': function () { },
        'complete': function () { }

    };

    var metax = Object.assign(meta, metaRequest);
    var xhr = new XMLHttpRequest()

    xhr.open(metax.method, metax.url, true)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.send(convertObjToFormData(metax.data));
    metax.beforeSend();
    xhr.onload = function () {
       
        if (xhr.status >= 200 && xhr.status < 400)
        {
            metax.success(JSON.parse(xhr.response));
        }
        if (xhr.status > 399)
        {
            metax.error(JSON.parse(xhr.response));
        }
        metax.complete();
    }

    function convertObjToFormData(obj) {
        if (obj)
        {
            var str = new URLSearchParams(Object.entries(obj)).toString();
            return str;
        }
        return null;
    }

}
//***************************************************/
//fim ajaxRequest()

function ajaxSendForm(metaRequest) {
    var meta = {
        'url': '',
        'method': 'POST',
        'form': 'form',
        'success': function () { },
        'error': function () { },
        'beforeSend': function () { },
        'complete': function () { }

    };
    var metax = Object.assign(meta, metaRequest);
    var xhr = new XMLHttpRequest()

    var form = document.querySelector(metax.form);
    var formData = new FormData();
    if (form instanceof HTMLFormElement)
    {
        formData = new FormData(form);
    }
    // Bind the FormData object and the form element


    // Define what happens on successful data submission
    xhr.addEventListener("load", function (event) {
       
        if (xhr.status >= 200 && xhr.status < 400)
        {
            metax.success(xhr.response);
        }
        if (xhr.status > 399)
        {
            metax.error(xhr.response);
        }
        metax.complete();
    });

    // Define what happens in case of error
    xhr.addEventListener("error", function (event) {
        metax.error("Erro ao Realizar Request");
        console.log('Oops! Something went wrong.');
    });

    // Set up our request
    xhr.open(metax.method, metax.url);
    metax.beforeSend();
    // Send our FormData object; HTTP headers are set automatically
    xhr.send(formData);
}


function showAlert(text, target, type = "success") {
    var divAlert = '<div class="alert alert-'+type+' alert-dismissible fade show" role="alert">';
    divAlert += text;
    divAlert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    divAlert += '</div>';
    
    var domTarget = document.querySelector(target);
    domTarget.innerHTML = divAlert;
   //setTimeout(function(){domTarget.innerHTML=""},4000);
}

/**
 * Função usada para suprir necessidade de $('#select').val() em casos de select multiple;
 * Uso para não usar Jquery;
 * @param {*} select 
 */
function getSelectValues(select) {
    var result = [];
    var options = select && select.options;
    var opt;
  
    for (var i=0, iLen=options.length; i<iLen; i++) {
      opt = options[i];
  
      if (opt.selected) {
        result.push(opt.value || opt.text);
      }
    }
    return result;
  }
