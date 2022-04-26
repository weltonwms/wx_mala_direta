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

<input type="hidden" id="files_manager_pdf" value='<?php echo json_encode($files_pdf,JSON_HEX_APOS) ?>'>

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

<!--
Toda a Lógica de conversão PDF está em convert_pdf.js
-->
<script src="<?php echo base_url('assets/js/convert_pdf.js')?>"> </script>

