<?php
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp-relay.gmail.com';
    $phpmailer->SMTPAuth   = false;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From       = 'info@abarentacar.com.ar';
    $phpmailer->FromName   = 'ABA Rent a Car';
});
