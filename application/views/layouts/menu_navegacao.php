<nav class="navbar navbar-expand-lg navbar-dark" style="background:linear-gradient(to right, #bd19a1 0, #712ca5 100%)">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">


                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('lista') ?>">Lista</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('modelo') ?>">Modelo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('malaDireta') ?>">Executar Mala Direta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('pdf') ?>">PDF</a>
                </li>






                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled">Disabled</a>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
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

    