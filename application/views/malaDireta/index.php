<h4 class="mt-2">Mala Direta <small class="text-muted">- Criação de Documentos Baseados na Lista e no Modelo</small>
</h4>


<div class="row">

    <div class="col-md-6">

    </div>
    <div class="col-md-2"></div>
    <div class="col-md-4">


        <a class="btn btn-outline-success width-lg" href="<?php echo base_url('malaDireta/execute')?>">
            <i class="fa-regular fa-circle-check"></i>
            Executar Mala Direta
        </a>

    </div>
</div>

<br><br>
<?php if($files):?>
<form action="" method="POST">
    <div class="card">
        <div class="card-header text-end">
            <a class="btn btn-success me-2 btn-sm "  href="<?php echo base_url('malaDireta/downloadAll')?>">
                <i class="fa-solid fa-download"></i>
                Download Todos Arquivos
            </a>

            <button class="btn btn-outline-danger me-2 btn-sm requireChecks" type="submit"
                formaction="<?php echo base_url('malaDireta/excluir')?>">
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

                <?php foreach($files as $file):?>

                <div class="row_file row">

                    <div class="col-md-12">
                        <input type="checkbox" class="check" name="files[]" value="<?php echo $file?>" style="margin-right:10px">
                        <a href="<?php echo base_url("malaDireta/download/$file")?>" style="text-decoration: none">
                            <i class="fa-regular fa-file text-secondary"></i>
                            &nbsp; &nbsp;<?php  echo   $file?>
                        </a>
                    </div>


                </div>

                <?php endforeach;?>
            </div>
        </div>
    </div>
</form>
<?php endif;?>


<script>
checkAndUncheck(); //função para selecionar todos os registros e deselecionar
</script>