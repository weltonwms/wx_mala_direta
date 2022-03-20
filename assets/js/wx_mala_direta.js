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

function ajaxRequest(metaRequest) {
    var meta = {
        'url': '',
        'method': 'POST',
        'data': {'um':'asd'},
        'success': function () { },
        'error': function () { }

    };

    var metax = Object.assign(meta, metaRequest);
    var post = JSON.stringify(metax.data)

    var url = metax.url
    var xhr = new XMLHttpRequest()

    xhr.open(metax.method, url, true)
    xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
    console.log(' metax ', metax)
    //xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //xhr.send("fname=Henry&lname=Ford");
    xhr.send(post);

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400)
        {
            //console.log(xhr);
            metax.success(JSON.parse(xhr.response));
        }
        if (xhr.status > 399)
        {
            metax.error(xhr);
        }
    }

}