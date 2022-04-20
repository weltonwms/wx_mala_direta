<?php
function activeClass($str){
    $uri_string=uri_string();
    if(strpos($uri_string, $str)!== false){
        return 'active';
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar_wx">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="<?php echo base_url("assets/img/logo.png")?>" alt="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">


                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('home') ?>" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('lista') ?>"
                        href="<?php echo base_url('lista') ?>">Lista</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('modelo') ?>"
                        href="<?php echo base_url('modelo') ?>">Modelo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('malaDireta') ?>"
                        href="<?php echo base_url('malaDireta') ?>">Mala Direta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('pdf') ?>" href="<?php echo base_url('pdf') ?>">PDF</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('email') ?>"
                        href="<?php echo base_url('email') ?>">Email</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo activeClass('configuracao') ?>"
                        href="<?php echo base_url('configuracao') ?>">Configurações</a>
                </li>


            </ul>


            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Usuário X
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item " href="#">Logout</a></li>
                       
                    </ul>
                </li>
            </ul>




        </div>

    </div>
</nav>
<section class="container">

    <?php if ($this->session->flashdata('msg_confirm') != null): ?>

    <div class="alert alert-<?php echo $this->session->flashdata('status') ?> alert-dismissible fade show" role="alert">
        <?php $icone = $this->session->flashdata('status') == 'danger' ? 'fa-triangle-exclamation' : 'fa-circle-check'?>
        <i class="fa-solid <?php echo $icone?>"></i>
        <?php echo $this->session->flashdata('msg_confirm') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?php endif;?>