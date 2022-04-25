
</section>
<footer class="container-fluid p-3" style="background-color:#dedede">
    <div class="container" >
        <div class="text-center text-muted">
        Copyright 2022 - WX MALA DIRETA
        </div>
    </div>
   
</footer>
<script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js')?>"></script>
<script>
    //disparando todos os tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

</body>
</html>