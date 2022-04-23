<h4 class="mt-2">EDIÇÃO/NOVO </h4>


<form method="POST" action="<?php echo base_url('lista/saveItem')?>">
    <input type="hidden" name="wx_id" value="<?php echo isset($item->wx_id)?$item->wx_id:''?>">
    <?php foreach($head->campos as $campo):?>
    <div class="row ">
        <label for="<?php echo $campo?>" class="col-sm-2 col-form-label"><?php echo $campo?></label>
        <div class="col-sm-10">
            <input type="text" name="<?php echo $campo?>" value="<?php echo isset($item->$campo)?$item->$campo:''?>"
                class="form-control form-control-sm" id="<?php echo $campo?>">
        </div>
    </div>

    <?php endforeach;?>

    <div class="row">
        <label for="" class="col-sm-2 col-form-label">Ativo?</label>
        <div class="col-sm-10">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="wx_ativo" id="wx_ativo1" autocomplete="off" value="1"<?php echo isset($item->wx_ativo) && $item->wx_ativo==1?'checked':''?>>
                <label class="btn btn-outline-success" for="wx_ativo1">Sim</label>

                <input type="radio" class="btn-check" name="wx_ativo" id="wx_ativo2" autocomplete="off" value="0" <?php echo isset($item->wx_ativo) && $item->wx_ativo==0?'checked':''?>>
                <label class="btn btn-outline-danger" for="wx_ativo2">Não</label>
            </div>
        </div>
    </div>



    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="<?php echo base_url('lista')?>" class="btn btn-secondary">Voltar</a>
</form>