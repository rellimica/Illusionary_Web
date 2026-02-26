<?php
session_name('ILLUSIONARY_SID');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | Access Denied</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Outfit:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0c0a15;
            margin: 0;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 3rem;
            border-radius: 24px;
            text-align: center;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .error-code {
            font-family: 'Outfit', sans-serif;
            font-size: 5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ff4e4e, #ff00ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .error-text {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .btn {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ff4e4e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    <div class="error-card">
        <div class="error-code">403</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-text">You don't have the required clearances to access this sector of the Illusionary network.</p>
        <a href="/" class="btn">Back to Home</a>
    </div>
</body>
</html>
