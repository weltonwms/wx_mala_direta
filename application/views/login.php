<div class="container-fluid" style="background-color:#009688">
    <img src="<?php echo base_url("assets/img/logo2.png")?>" class="mx-auto d-block" alt="logo HeyMan">
</div>
<section class="container" style="min-height:650px; background-color:inherit">

    <div class="row justify-content-center" style="margin-top:-30px">
        <div class="col-md-4">
            <div class="card p-5">
                <h5 class="card-header text-center">
                    <i class="fa-solid fa-user"></i>
                    LOG IN
                </h5>
                <div class="card-body">
                <?php if ($this->session->flashdata('msg_error')): ?>
                    <div class="alert alert-danger">
                       <?php echo $this->session->flashdata('msg_error')?>
                    </div>
                <?php endif;?>
                    
                    <form method="POST" action="<?php echo base_url("login/postLogin")?>"> 
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuário</label>
                            <input type="text" class="form-control"
                             id="username" name="username" >
                           
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" 
                            id="password" name="password">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Lembre-me</label>
                        </div>
                        <button type="submit" class="btn btn-success width-lg">
                        <i class="fa-solid fa-right-to-bracket"></i>
                            Entrar
                        </button>
                    </form>



                </div>
            </div>
        </div>

    </div>