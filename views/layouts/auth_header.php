<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SICAP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-image: url('<?php echo URLROOT; ?>/img/fondologin.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh; margin: 0; display: flex; justify-content: center; align-items: center; 
        }
        .auth-card { background: rgba(255, 255, 255, 0.9); padding: 2rem; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); width: 350px; text-align: center; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 1rem; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-success { background-color: #28a745; color: white; width: 100%; }
        .btn-danger { background-color: #dc3545; color: white; width: 100%; }
        .btn-secondary { background-color: #6c757d; color: white; width: 100%; margin-top: 0.5rem; }
    </style>
</head>
<body>
