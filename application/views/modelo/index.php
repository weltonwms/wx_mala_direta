<h4 class="mt-2">MODELO <small class="text-muted">- Documento que servirá de base para criar outros documentos</small></h4>

<form action="<?php echo base_url('modelo/loadModelo') ?>" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <label for="" class="form-label text-warning"><i class="fa-solid fa-triangle-exclamation"></i></label><br>

            <button class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInstrucoes"
                aria-expanded="false" aria-controls="collapseInstrucoes">
                Instruções Para Carregamento do Modelo
            </button>


        </div>
        <div class="col-md-4">
            <label for="formFile" class="form-label">Arquivo docx ou Odt utilizado como Modelo</label>
            <input class="form-control" type="file" id="formModelo" name="formModelo" required>
        </div>
        <div class="col-md-4">

            <label for="" class="form-label text-success"><i class="fa-regular fa-circle-check"></i></label><br>
            <button class="btn btn-outline-success width-lg" type="submit">Carregar Modelo</button>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="collapse" id="collapseInstrucoes">
                <div class="card card-body">
                    <ul>
                        <li>O Arquivo deve ser um documento do Word(docx) ou documento do Libreoffice Writer(odt). </li>
                        <li>
                            Utilize marcações no documento do tipo: ${campo}, sendo campo o nome da coluna desejada.
                            <small class="text-muted">Ex: ${nome_completo}. </small>
                    
                        </li>
                       
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <br>
    <p class="mt-2 text-center bg-secondary bg-gradient text-white p-3" style="font-size:18px">
    Utilize marcações no documento do tipo: <strong>${campo}</strong>, 
        sendo campo o nome da coluna desejada.
    </p>

    <?php if($carregamentoModelo):?>
    <div class="border border-light border-3 mt-3 p-2 bg-light.bg-gradient">
        <p><strong>Último Carregamento: </strong><?php echo UtilHelper::dateBr($carregamentoModelo->updated_at); ?></p>
        <p><strong>Tipo Arquivo: </strong><?php echo $carregamentoModelo->filename; ?></p>
    </div>
    <?php endif;?>

</form>
