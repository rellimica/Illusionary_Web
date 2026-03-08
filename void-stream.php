<?php
// void-stream.php - Standalone helper for the Null broadcast experience
$url = $_GET['v'] ?? '';
if (!$url) {
    die("NO_SIGNAL_FOUND");
}

// Extract Video ID for looping
$videoId = '';
if (preg_match('/embed\/([^\/\?]+)/', $url, $matches)) {
    $videoId = $matches[1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NULL_STREAM</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background: #000;
            overflow: hidden;
            font-family: 'Outfit', sans-serif;
        }
        .null-video-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: #000;
        }
        .null-video-blocker {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100;
            cursor: default;
        }
        .null-video-title {
            position: fixed;
            top: 20px;
            left: 20px;
            color: #00ffae; /* Null's accent color */
            font-size: 1rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            z-index: 20;
            pointer-events: none;
            opacity: 0.7;
            text-shadow: 0 0 10px #000;
        }
        @keyframes scanline {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent, rgba(0, 255, 174, 0.05), transparent);
            opacity: 0.1;
            z-index: 15;
            pointer-events: none;
            animation: scanline 4s linear infinite;
        }
    </style>
</head>
<body>
    <div class="null-video-title" id="statusTitle">NULL_STREAM: CONNECTING...</div>
    <div class="scanline"></div>
    <div class="null-video-container">
        <div class="null-video-blocker"></div>
        <iframe src="<?php echo htmlspecialchars($url); ?>?autoplay=1&controls=0&modestbranding=1&rel=0&iv_load_policy=3<?php echo $videoId ? "&loop=1&playlist=$videoId" : ""; ?>" 
                allow="autoplay; encrypted-media" 
                allowfullscreen></iframe>
    </div>

    <script>
        const titles = [
            "NULL_STREAM: VISCOSITY_NORMAL",
            "VOID_PIPE: 99.8% STABLE",
            "ZEKE_CAM_01: ACTIVE",
            "MEMORY_LEAK_FEED: BROADCASTING",
            "CORE_DUMP_VISUALIZER: RUNNING",
            "LATTICE_SYNC: ESTABLISHED",
            "DECRYPTING_VOID: 55%",
            "SIGNAL_DAMPENING: OFF"
        ];
        document.getElementById('statusTitle').innerText = titles[Math.floor(Math.random() * titles.length)];
    </script>
</body>
</html>
