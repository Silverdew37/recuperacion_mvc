<?php include_once(VIEWS . 'header.php') ?>
<div class="card" id="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Iniciar sesión</a></li>
            <li class="breadcrumb-item">Datos de envío</li>
            <li class="breadcrumb-item"><a href="#">Forma de pago</a></li>
            <li class="breadcrumb-item"><a href="#">Verifica los datos</a></li>
        </ol>
    </nav>
    <div class="card-header">
        <h1>Dirección del usuario</h1>
        <p>Por favor, confirme su dirección o introduzca una nueva</p>
    </div>
    <div class="card-body">
        DATOS DIRECCIÓN
        Nombre: <?= $data['data']->first_name ?>
        Apellidos: <?= $data['data']->last_name_1 . " " . $data['data']->last_name_2 ?>
        Dirección: <?= $data['data']->address ?>
        Ciudad: <?= $data['data']->city ?>
        Provincia: <?= $data['data']->state ?>
        Código Postal: <?= $data['data']->zipcode ?>
        País: <?= $data['data']->country ?>
        <div >
            <a href="<?= ROOT ?>cart/paymentmode" class="btn btn-success">Confirmar</a>
            <a href="<?= ROOT ?>cart/address" class="btn btn-primary">Modificar</a>
        </div>

        BOTONES CONFIRMAR / MODIFICAR (address)
    </div>

</div>

<?php include_once(VIEWS . 'footer.php') ?>
