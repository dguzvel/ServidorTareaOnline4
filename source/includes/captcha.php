<?php

    require_once "../libraries/recaptchalib.php";   
    $secret = "6Lf7ticeAAAAAF83ijqinf99owagEdGAWND8NcAi";
    $response = null;
    // Verificamos la clave secreta
    $reCaptcha = new ReCaptcha($secret);
    if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
    $_SERVER["REMOTE_ADDR"],
    $_POST["g-recaptcha-response"]
    );
    }

    if ($response != null && $response->success) {
        
        header ("Location: ../views/inicio.php");

    } else {
        
        header ("Location: ../views/login.php?error=captcha");
        exit();

    }

?>