<html>
    <head></head>
    <body>
        <a target="_blank" href="<?php echo get_site_url(); ?>" alt="" title="<?php echo get_bloginfo('name'); ?>" style="display: inline-block;margin: 25px auto;">
            <img src="<?php echo get_bloginfo('template_url'); ?>/images/logo.png" width="156">
        </a>
        <br>
        <strong>Información de contacto</strong>
        <br><br>
        <strong>Nombre: </strong> <?php echo $input['name']; ?><br>
        <strong>Email: </strong> <?php echo $input['email']; ?><br>
        <strong>Teléfono: </strong> <?php echo $input['phone_number']; ?><br>
        <strong>Mensaje: </strong> <?php echo $input['message']; ?><br>
        <strong>Donar/Contribuir: </strong> <?php echo implode(' ', $input['intention']); ?><br>
        <br><br>
        Saludos, <br>
        <?php echo get_bloginfo('name'); ?><br>
        <br><br>
        © <?php echo date('Y'); ?> <a target="_blank" href="<?php echo get_site_url(); ?>"><?php echo get_bloginfo('name'); ?></a> Todos los derechos reservados<br>
        <br><br>
    </body>
</html>
