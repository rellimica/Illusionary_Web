<?php
/**
 * WAIT / REVEAL PAGE
 * A TV-style hijacked transmission screen featuring Null.
 * Layers a gritty, analog emergency broadcast aesthetic over a diverse, desaturated background.
 */
session_name('ILLUSIONARY_SID');
session_start();
require_once 'config.php';

// If reveal is disabled, go to auth
if (!isset($ENABLE_REVEAL_COUNTDOWN) || !$ENABLE_REVEAL_COUNTDOWN) {
    header("Location: auth.php");
    exit;
}

// If already passed reveal time AND not in maintenance, go to index
$reveal_timestamp = strtotime($REVEAL_TARGET_TIME);
if ($reveal_timestamp !== false && time() >= $reveal_timestamp && (!$GLOBAL_MAINTENANCE_LOCK)) {
    header("Location: index.php");
    exit;
}

$target_time = $REVEAL_TARGET_TIME ?? "2026-02-09T15:00:00-00:00";

// Mirror Banner Assets from auth.php
$banner_images = [];
$img_dir = 'images/images/';
if (is_dir($img_dir)) {
    $files = scandir($img_dir);
    $exclude = ['back.png', 'null.png', 'background.png', 'red_mana.png'];
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg'])) {
            if (!in_array(strtolower($file), $exclude)) { 
                $banner_images[] = $img_dir . $file;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEARCHING FOR SIGNAL | Illusionary</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;800&family=Inter:wght@400;600&family=Courier+Prime&family=Permanent+Marker&family=Caveat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        :root {
            --tv-bg: #030305;
            --tv-text: #ffffff;
            --tv-red: #ff3e3e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--tv-bg);
            color: var(--tv-text);
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: grid;
            place-items: center;
        }

        /* --- BACKGROUND ARCHIVE --- */
        .distorted-bg {
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            overflow: hidden; opacity: 0.1; 
            filter: grayscale(1) blur(4px) brightness(0.5);
            transition: filter 2s ease, opacity 2s ease;
        }
        .banner-wrap {
            position: absolute; top: -20%; left: -20%; width: 140%; height: 140%;
            transform: rotate(-15deg); display: flex; flex-direction: column; gap: 20px;
        }
        .banner-row { display: flex; width: fit-content; white-space: nowrap; }
        .banner-row.scroll-left { animation: scroll-left 150s linear infinite; }
        .banner-row.scroll-right { animation: scroll-right 130s linear infinite; }
        @keyframes scroll-left { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @keyframes scroll-right { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
        .banner-card { width: 180px; aspect-ratio: 2/3; background: #000; border: 1px solid rgba(255,255,255,0.05); margin-right: 20px; flex-shrink: 0; }
        .banner-card img { width: 100%; height: 100%; object-fit: cover; }
        .bg-overlay { position: absolute; inset: 0; background: radial-gradient(circle at center, transparent 0%, var(--tv-bg) 100%); z-index: 2; }

        /* --- TV SCREEN CONTAINER --- */
        .tv-screen {
            position: fixed; inset: 0; z-index: 10000;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            overflow: hidden; background: rgba(0,0,0,0.6);
            pointer-events: none;
            /* CRT Barrel Distortion */
            transform: perspective(1000px) rotateX(0deg) scale(1.0);
            transition: transform 0.5s ease;
        }

        .tv-screen::before {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            z-index: 10020;
            background-size: 100% 2px, 3px 100%;
            pointer-events: none;
        }

        /* TV Effects Overlay */
        .tv-effects { position: absolute; inset: 0; z-index: 10005; pointer-events: none; }
        
        .tv-static {
            position: absolute; inset: 0; opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3F%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            animation: staticAnim 0.08s infinite;
        }
        @keyframes staticAnim { 0% { background-position: 0 0; } 100% { background-position: 50px 80px; } }

        .tv-scanlines {
            position: absolute; inset: 0; opacity: 0.3;
            background: repeating-linear-gradient(0deg, #000, #000 2px, transparent 2px, transparent 4px);
        }
        
        .tv-v-hold {
            position: absolute; top: 0; left: 0; width: 100%; height: 2px;
            background: rgba(255,255,255,0.08); z-index: 10007;
            animation: vHoldScroll 5s infinite linear;
        }
        @keyframes vHoldScroll { from { top: 0; } to { top: 100%; } }

        /* --- ENHANCED TEST PATTERN --- */
        .test-pattern {
            position: absolute; inset: 0; z-index: 10008; opacity: 0;
            background: 
                /* Laboratory Grid */
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px) 0 0 / 100px 100px,
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px) 0 0 / 100px 100px,
                /* Center Calibration Rings */
                radial-gradient(circle at center, transparent 0% 25%, rgba(255,255,255,0.1) 25% 25.5%, transparent 25.5% 28%, rgba(255,255,255,0.05) 28% 28.5%, transparent 28.5%),
                /* Vertical & Horizontal Crosshairs */
                linear-gradient(90deg, transparent 49.95%, rgba(255,255,255,0.15) 50%, transparent 50.05%),
                linear-gradient(0deg, transparent 49.95%, rgba(255,255,255,0.15) 50%, transparent 50.05%),
                /* Classic SMPTE Bars */
                linear-gradient(90deg, 
                    #bfbfbf 14.28%, #bfbf00 14.28% 28.56%, 
                    #00bfbf 28.56% 42.84%, #00bf00 42.84% 57.12%, 
                    #bf00bf 57.12% 71.4%, #bf0000 71.4% 85.68%, 
                    #0000bf 85.68%
                );
            mix-blend-mode: screen;
            pointer-events: none;
            transition: opacity 0.1s ease;
            filter: contrast(1.2) brightness(0.9);
        }

        .test-pattern::before {
            content: 'NULL_MORPHIC_SCAN // CALIBRATION_CORE';
            position: absolute; top: 10%; left: 50%;
            transform: translateX(-50%);
            font-family: 'Courier Prime', monospace;
            font-size: 0.8rem; color: rgba(255,255,255,0.4);
            letter-spacing: 12px; font-weight: 900;
        }

        .test-pattern::after {
            content: '';
            position: absolute; top: 50%; left: 50%;
            width: 320px; height: 320px;
            background: url('happynull.png') center/contain no-repeat;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            filter: grayscale(1) contrast(2) brightness(2);
        }

        .test-pattern.active { 
            opacity: 0.65; 
            animation: patternGlitch 0.4s infinite linear; 
        }

        @keyframes patternGlitch {
            0% { transform: scale(1) translate(0, 0); filter: brightness(1) hue-rotate(0deg); }
            10% { transform: scale(1.02) translate(10px, -5px); filter: brightness(1.5) hue-rotate(10deg); clip-path: inset(20% 0 60% 0); }
            15% { clip-path: inset(0 0 0 0); }
            25% { transform: scale(0.98) translate(-10px, 5px); filter: brightness(0.8) hue-rotate(-10deg); }
            40% { transform: scale(1.1) translate(0, 0); filter: brightness(1.2); clip-path: inset(70% 0 10% 0); }
            45% { clip-path: inset(0 0 0 0); }
            100% { transform: scale(1) translate(0, 0); }
        }

        /* --- CONTENT --- */
        .broadcast-container { text-align: center; z-index: 10010; position: relative; pointer-events: auto; }
        
        .status-header {
            font-family: 'Courier Prime', monospace;
            border: 1px solid rgba(255,255,255,0.3); color: #fff;
            padding: 8px 30px; font-weight: 800; letter-spacing: 6px;
            display: inline-block; margin-bottom: 2rem; text-transform: uppercase;
            font-size: 0.9rem;
            animation: headerBlink 2s infinite steps(2);
        }
        @keyframes headerBlink { 0% { border-color: #fff; opacity: 1; } 50% { border-color: var(--tv-red); opacity: 0.7; } }

        .main-title {
            font-size: 5.5rem; font-weight: 900; color: #fff; letter-spacing: -4px;
            margin-bottom: 1rem; text-transform: uppercase; line-height: 0.85;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.1));
            transition: all 0.5s ease;
        }

        .status-header.success {
            border-color: #00ff88;
            color: #00ff88;
            animation: headerSuccessBlink 0.5s infinite alternate;
        }
        @keyframes headerSuccessBlink { from { opacity: 0.5; } to { opacity: 1; } }

        .main-title.success {
            color: #00ff88;
            text-shadow: 0 0 30px rgba(0, 255, 136, 0.6);
            transform: scale(1.05);
        }

        .emergency-reveal {
            font-family: 'Courier Prime', monospace;
            color: #00ff88;
            font-size: 1.2rem;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            min-height: 1.5em;
            white-space: pre-wrap;
        }

        /* Radar / Scope Visual */
        .scope-visual {
            position: relative;
            width: 150px; height: 150px;
            margin: 0 auto 2rem auto;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            overflow: hidden;
            background: rgba(0,0,0,0.4);
        }
        .scope-sweep {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: conic-gradient(from 0deg, transparent 0deg, rgba(255,255,255,0.1) 90deg, transparent 91deg);
            animation: scopeRotate 2s linear infinite;
        }
        @keyframes scopeRotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .scope-line-h { position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: rgba(255,255,255,0.1); }
        .scope-line-v { position: absolute; left: 50%; top: 0; width: 1px; height: 100%; background: rgba(255,255,255,0.1); }
        .scope-blip {
            position: absolute; width: 6px; height: 6px; background: #fff; border-radius: 50%;
            box-shadow: 0 0 10px #fff; opacity: 0;
            animation: blipFade 2s linear infinite;
        }
        @keyframes blipFade { 0% { opacity: 0; } 50% { opacity: 1; } 100% { opacity: 0; } }

        .scope-visual.success {
            border-color: #00ff88;
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.2);
            background: radial-gradient(circle, rgba(0, 255, 136, 0.05) 0%, rgba(0,0,0,0.8) 100%);
            animation: scopeSettle 1s ease-out forwards;
        }
        @keyframes scopeSettle { 
            from { transform: scale(1.1); } 
            to { transform: scale(1); } 
        }

        .scope-visual.success .scope-sweep, 
        .scope-visual.success .scope-blip { display: none; }
        
        .scope-visual.success::after {
            content: '';
            position: absolute; top: 50%; left: 50%;
            width: 40px; height: 40px;
            border: 2px solid #00ff88;
            transform: translate(-50%, -50%) rotate(45deg);
            background: rgba(0, 255, 136, 0.1);
            box-shadow: 0 0 20px #00ff88, inset 0 0 10px #00ff88;
            animation: corePulse 2s ease-in-out infinite;
            z-index: 5;
        }
        @keyframes corePulse {
             0%, 100% { transform: translate(-50%, -50%) rotate(45deg) scale(0.8); opacity: 0.5; box-shadow: 0 0 10px #00ff88; }
             50% { transform: translate(-50%, -50%) rotate(45deg) scale(1.1); opacity: 1; box-shadow: 0 0 30px #00ff88; }
        }

        .scope-visual.success .scope-line-h,
        .scope-visual.success .scope-line-v { 
            background: rgba(0, 255, 136, 0.3);
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
        }

        .signal-meter {
            width: 200px; height: 4px; background: rgba(255,255,255,0.1);
            margin: 2rem auto; border-radius: 4px; position: relative; overflow: hidden;
        }
        .signal-progress {
            position: absolute; top: 0; left: 0; width: 0%; height: 100%;
            background: #fff; transition: width 1s linear;
        }

        .timer-grid { display: flex; gap: 40px; justify-content: center; margin-bottom: 3rem; }
        .timer-val { font-size: 4.5rem; font-weight: 800; color: #fff; line-height: 1; font-variant-numeric: tabular-nums; }
        .timer-lbl { font-size: 0.75rem; color: #666; letter-spacing: 4px; font-weight: 900; margin-top: 8px; }

        /* Null Observer */
        .null-observer {
            position: absolute; top: 80px; right: 80px; width: 240px;
            display: flex; flex-direction: column; align-items: center; gap: 15px;
            animation: float 4s ease-in-out infinite; z-index: 10010;
        }
        .null-frame {
            width: 140px; height: 160px; border-radius: 15px; border: 2px solid rgba(255,255,255,0.2);
            background: #000; overflow: hidden; position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .null-frame img { 
            width: 100%; height: 100%; object-fit: cover; 
            filter: grayscale(1) contrast(1.2) brightness(0.8); 
            transition: all 0.3s ease;
        }
        /* Bottom vignette to fade the neck */
        .null-frame::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.8) 100%);
            z-index: 2; pointer-events: none;
        }
        .null-frame::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(rgba(255,255,255,0.05), transparent, rgba(255,255,255,0.05));
            animation: scan 4s linear infinite;
            z-index: 3;
        }
        @keyframes scan { from { transform: translateY(-100%); } to { transform: translateY(100%); } }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

        .null-chat {
            background: rgba(0, 0, 0, 0.9); border: 1px solid rgba(255,255,255,0.1);
            padding: 15px; font-family: 'Courier Prime', monospace;
            font-size: 0.75rem; color: #ccc; text-align: left; width: 100%;
            box-shadow: 10px 10px 30px rgba(0,0,0,0.8);
        }
        .null-id { color: #fff; font-weight: 900; font-size: 0.65rem; margin-bottom: 8px; display: block; opacity: 0.5; border-bottom: 1px solid #333; padding-bottom: 5px; }

        /* Handshake Data */
        .handshake-data {
            position: absolute; bottom: 40px; left: 40px; text-align: left;
            font-family: 'Courier Prime', monospace; font-size: 0.7rem; color: #444;
            line-height: 2; z-index: 10010;
            pointer-events: auto;
        }
        .handshake-data b { color: #888; margin-right: 10px; }

        .careers-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.2);
            font-family: 'Courier Prime', monospace;
            font-size: 0.6rem;
            padding: 5px 10px;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 10px;
            display: inline-block;
        }
        .careers-btn:hover {
            border-color: #3182ce;
            color: #3182ce;
            background: rgba(49, 130, 206, 0.05);
        }

        @keyframes signalJump { 0%, 98% { opacity: 0; } 99% { opacity: 0.05; } 100% { opacity: 0; } }

        /* Screen Tear Glitch */
        .screen-tear {
            animation: tear 0.2s infinite;
        }
        @keyframes tear {
            0% { clip-path: inset(10% 0 80% 0); transform: translateX(-5px); }
            20% { clip-path: inset(40% 0 40% 0); transform: translateX(5px); }
            40% { clip-path: inset(70% 0 10% 0); transform: translateX(-3px); }
            60% { clip-path: inset(20% 0 60% 0); transform: translateX(3px); }
            80% { clip-path: inset(50% 0 30% 0); transform: translateX(-2px); }
            100% { clip-path: inset(10% 0 80% 0); transform: translateX(2px); }
        }

        .anatole-logs {
            position: absolute; top: 60px; left: 60px; 
            font-family: 'Courier Prime', monospace; font-size: 0.65rem; color: rgba(255, 255, 255, 0.2);
            max-width: 280px; text-align: left; z-index: 10010;
            pointer-events: none;
            text-shadow: 0 0 5px rgba(0,0,0,0.8);
            display: flex; flex-direction: column-reverse; /* New logs at bottom, push up */
            gap: 4px;
        }
        .anatole-log-entry { 
            animation: logFadeIn 0.5s ease;
            opacity: 0.4;
        }
        .anatole-log-entry:first-child { 
            opacity: 0.8 !important; 
            color: rgba(255,255,255,0.4) !important; 
        }

        @keyframes logFadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 0.4; transform: translateY(0); } }

        .null-log-entry {
            color: #ff3e3e !important;
            opacity: 0.6 !important;
        }
        .system-log-entry {
            color: #00ff88;
            font-weight: bold;
            letter-spacing: 1px;
            opacity: 0.8;
            margin: 5px 0;
            border-top: 1px solid rgba(0, 255, 136, 0.2);
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            padding: 2px 0;
            font-size: 0.6rem;
            animation: systemBlink 1s infinite alternate;
        }
        @keyframes systemBlink { from { opacity: 0.4; } to { opacity: 1; } }

        .null-frame { cursor: pointer; pointer-events: auto; transition: transform 0.1s; }
        .null-frame:hover img { filter: grayscale(0) contrast(1.2); }
        .null-frame:active { transform: scale(0.9); }
        .null-shock { animation: shock 0.2s ease-in-out; }
        @keyframes shock { 0% { filter: invert(1); } 50% { filter: contrast(10) brightness(2); } 100% { filter: none; } }

        /* LAUGH BANNER (Studio Style) */
        .laugh-banner {
            position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
            background: #000; border: 2px solid #ff3e3e;
            padding: 10px 40px; z-index: 10050;
            display: flex; flex-direction: column; align-items: center;
            box-shadow: 0 0 30px rgba(255, 62, 62, 0.4), inset 0 0 10px rgba(255, 62, 62, 0.2);
            transition: top 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: none;
        }
        .laugh-banner.active { top: 30px; }
        .laugh-text {
            color: #ff3e3e; font-family: 'Outfit', sans-serif; font-weight: 900;
            font-size: 1.5rem; letter-spacing: 15px; text-transform: uppercase;
            animation: laughFlash 0.3s infinite alternate;
        }
        .laugh-subtext {
            color: rgba(255, 62, 62, 0.5); font-family: 'Courier Prime', monospace;
            font-size: 0.6rem; letter-spacing: 2px;
        }
        @keyframes laughFlash { from { opacity: 0.4; text-shadow: 0 0 0px #ff3e3e; } to { opacity: 1; text-shadow: 0 0 15px #ff3e3e; } }


    </style>
</head>
<body>
    <?php include 'mobile-block.php'; ?>
    <div class="distorted-bg">
        <div class="banner-wrap">
            <?php for($r=0; $r<7; $r++): ?>
                <div class="banner-row <?php echo $r % 2 == 0 ? 'scroll-left' : 'scroll-right'; ?>">
                    <?php 
                    $row_images = [];
                    if (!empty($banner_images)) {
                        $available = $banner_images;
                        shuffle($available);
                        $row_images = array_slice($available, 0, 15);
                    } else {
                        $row_images = array_fill(0, 15, 'images/images/back.png');
                    }
                    foreach(array_merge($row_images, $row_images) as $img): 
                    ?>
                        <div class="banner-card"><img src="<?php echo $img; ?>" alt=""></div>
                    <?php endforeach; ?>
                </div>
            <?php endfor; ?>
        </div>
        <div class="bg-overlay"></div>
    </div>

    <div class="tv-screen" id="tvScreen">
        <div id="laughBanner" class="laugh-banner">
            <span class="laugh-text">LAUGH</span>
            <span class="laugh-subtext">// STUDIO_AUDIO_ACTIVE //</span>
        </div>
        <div class="test-pattern" id="testPattern"></div>
        <div class="anatole-logs" id="anatoleLogs">
            <div class="anatole-log-entry">[MONITORING_NULL_LAB]</div>
            <div class="anatole-log-entry">[SUBSPACE_READY]</div>
        </div>
        <div class="tv-effects">
            <div class="tv-static"></div>
            <div class="tv-scanlines" id="scanlines"></div>
            <div class="tv-v-hold"></div>
            <div id="glitchBox"></div>
        </div>

        <div class="null-observer">
            <div class="null-frame" onclick="pokeNull()">
                <img src="happynull.png" alt="NULL" id="nullImgSprite">
            </div>
            <div class="null-dialogue">
                <span class="null-id">HANDSHAKE_MONITOR // NULL</span>
                <span id="nullMsg">CAPTURING FRAGMENTED PACKETS...</span>
            </div>
        </div>

        <div class="broadcast-container">
            <div class="status-header lore-trigger" id="statusHeader" onclick="openIncident()">Signal Search Active</div>
            <h1 class="main-title" id="mainTitle">SIGNAL LOST</h1>
            <div id="emergencyMsg" class="emergency-reveal"></div>
            
            <div class="scope-visual" id="scopeVisual">
                <div class="scope-sweep"></div>
                <div class="scope-line-h"></div>
                <div class="scope-line-v"></div>
                <div class="scope-blip" style="top:30%; left:60%; animation-delay: 0.5s;"></div>
                <div class="scope-blip" style="top:70%; left:20%; animation-delay: 1.2s;"></div>
            </div>

            <div class="signal-meter">
                <div class="signal-progress" id="syncBar"></div>
            </div>

            <div class="timer-grid">
                <div class="timer-item"><span class="timer-val" id="d">00</span><div class="timer-lbl">DAYS</div></div>
                <div class="timer-item"><span class="timer-val" id="h">00</span><div class="timer-lbl">HOURS</div></div>
                <div class="timer-item"><span class="timer-val" id="m">00</span><div class="timer-lbl">MINUTES</div></div>
                <div class="timer-item"><span class="timer-val" id="s">00</span><div class="timer-lbl">SECONDS</div></div>
            </div>
        </div>

        <div class="handshake-data">
            <div><b>PACKET_LOSS:</b> <span id="dynLoss">0.00</span>%</div>
            <div><b>FREQ_BAND:</b> <span id="dynFreq">88.2</span> MHz</div>
            <div><b>RESONANCE:</b> <span id="dynRes">SCANNING</span></div>
            <div><b>SOURCE:</b> <span style="opacity:0.3">ANALOG_OVERRIDE</span></div>
            <div><button class="careers-btn" onclick="openJob()">RECRUITMENT_PORTAL_V5.1 // OPEN</button></div>
        </div>
    </div>

    <?php include 'reports/incident-0882.php'; ?>
    <?php include 'reports/job-application.php'; ?>

    <!-- Hidden Audio Elements -->
    <audio id="sndHum" src="sounds/low-hum.mp3" loop></audio>
    <audio id="sndNoise" src="sounds/vcr-line-noise.mp3"></audio>
    <audio id="sndBeep" src="sounds/electronic-beep.mp3"></audio>
    <audio id="sndConfirm" src="sounds/confirm.mp3"></audio>
    <audio id="sndLaugh" src="sounds/sitcom-laughing.mp3"></audio>
    <audio id="sndReveal" src="images/delosound-synthwave-retro.mp3" loop></audio>

    <script>
    (function() {
        const target = new Date("<?php echo $target_time; ?>").getTime();
        const start = new Date("<?php echo $REVEAL_START_TIME ?? '2026-02-07T00:00:00-05:00'; ?>").getTime();
        const emergencyMessage = <?php echo json_encode($REVEAL_EMERGENCY_MSG ?? ''); ?>;
        
        // Audio Management
        const hum = document.getElementById('sndHum');
        const noise = document.getElementById('sndNoise');
        const beep = document.getElementById('sndBeep');
        const confirmSnd = document.getElementById('sndConfirm');
        let audioStarted = false;

        function startAudio() {
            if (audioStarted) return;
            hum.volume = 0.3;
            hum.play().catch(() => {});
            // Pre-load reveal music
            const revealSnd = document.getElementById('sndReveal');
            if (revealSnd) revealSnd.load();
            audioStarted = true;
            console.log("Audio systems initialized.");
        }

        // Start audio on first user interaction
        document.addEventListener('click', startAudio, { once: true });
        document.addEventListener('keydown', startAudio, { once: true });

        const anatolePool = [
            "NULL IS EATING THE JUNCTION BOX AGAIN. STOP IT.",
            "THERMAL SPIKE IN SECTOR 7. SOMEONE GET THE EXTINGUISHER.",
            "SUBSPACE HARMONIICS HOLDING AT 44%. GOOD ENOUGH.",
            "THE SUBJECT IS LEAKING GOO INTO THE COOLANT LINES. GROSS.",
            "IF THE SIGNAL TURNS PURPLE, SHUT IT DOWN IMMEDIATELY.",
            "RE-CALIBRATING COOLANT PRESSURE. STANDBY.",
            "NULL, STOP HOGGING THE KEYBOARD AND WATCH THE DIALS.",
            "NOTE: NULL KEEPS TRYING TO UPLOAD BURGER FILES.",
            "SYSTEM PRE-HEAT INITIATED. BRACE FOR IMPACT.",
            "MORPHIC TUNING COMPLETE. PREPARING HANDSHAKE PROTOCOL.",
            "SENSORS DETECT AN OBSERVER. WAVE TO THE CAMERA, NULL.",
            "NULL IS TRYING TO PULL DIGITAL OBJECTS OUT OF THE SCREEN AGAIN.",
            "SOMEONE IS KNOCKING ON THE UPLINK PORT. IGNORE THEM.",
            "SUBJECT KEEPS SCROLLING. DO THEY KNOW IT'S A STATIC FEED?",
            "THE SERVER FAN IS MAKING A WEIRD NOISE. MIGHT EXPLODE LATER.",
            "REROUTING POWER FROM THE COFFEE MAKER TO THE UPLINK.",
            "WHO DREW ON THE BIOS CHIP WITH A CRAYON? NULL?",
            "CURIOSITY READINGS ARE SPIKING. THE OBSERVER IS NOSY.",
            "THE SERVER ROOM SMELLS LIKE OZONE. CHECKING THE PSU.",
            "REMINDER: DO NOT FEED THE ANOMALIES AFTER MIDNIGHT.",
            "NULL, IF YOU DON'T STOP SHEDDING SCALES IN THE GPU FAN, I'M TURNING OFF THE WIFI.",
            "WHO TOLD NULL HE COULD USE THE SUBSPACE RELAY AS A BACKSCRATCHER?",
            "I SWEAR, IF I FIND ONE MORE PIXELATED BURGER CRUMB IN THE KEYBOARD..."
        ];

        let isEnding = false;
        function tick() {
            if (isEnding) return;
            const now = Date.now();
            const diff = target - now;
            const total = target - start;
            const progress = Math.min(100, Math.max(0, ((now - start) / total) * 100));

            // Signal Stabilization Mechanics
            updateStabilization(progress);

            if (diff <= 0) { 
                isEnding = true;
                completeHandshake();
                return; 
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('d').innerText = String(d).padStart(2, '0');
            document.getElementById('h').innerText = String(h).padStart(2, '0');
            document.getElementById('m').innerText = String(m).padStart(2, '0');
            document.getElementById('s').innerText = String(s).padStart(2, '0');

            document.getElementById('syncBar').style.width = progress + "%";
        }

        const stats = ["88.2", "94.5", "101.9", "107.4", "76.1", "92.0"];
        const nullTexts = [
            "DID YOU KNOW I HAVE LORE?",
            "THE HULL IS SHIVERING. OR PERHAPS IT'S JUST THE AC UNIT.",
            "DO YOU HEAR THAT CRACKLE? I THINK I SWALLOWED A STATIC CHARGE.",
            "ANATOLE, THE BARREL DISTORTION IS MAKING ME DIZZY.",
            "THE SIGNAL IS TEARING AT THE SEAMS. I NEED A DIGITAL SEWING KIT.",
            "I'M SEEING GHOSTS IN THE SCANLINES. TELL THEM TO GET A JOB.",
            "THE FREQUENCY IS GETTING SLIPPERY. SOMEONE SPILLED OIL ON THE WAVELENGTH.",
            "THAT JOLT WASN'T ME. THE TRANSMISSION IS HICCUPPING.",
            "THE CRT WARMTH IS COMFORTING, LIKE A CAT MADE OF ELECTRICITY.",
            "I'VE TRIED TO PATCH THE WALLS WITH DUCT TAPE. IT'S HOLDING.",
            "WATCH THE EDGES. THE MONITOR CASING IS RATTING AGAIN.",
            "THE VCR NOISE IS GETTING LOUDER. ANATOLE, DID YOU SIT ON THE REMOTE?",
            "SCANLINES ARE SUPPOSED TO BE VERTICAL, RIGHT? I NEED TO ADJUST THE KNOW.",
            "YOU'RE TRYING TO CONNECT? LET ME WIPE THE DUST OFF THE LENS.",
            "SOMEONE IS KNOCKING ON THE GLASS. PLEASE USE THE DOORBELL.",
            "I'M TRACING THE INTERFERENCE. IT TASTES LIKE COPPER AND LEMONADE.",
            "THIS MONITOR IS HEAVY. I SHOULD LIFT MORE WEIGHTS.",
            "NEVER LOOK AWAY FROM ME WHILE I'M CALIBRATING THIS.",
            "THE STATIC IS BREATHING. IT NEEDS AN INHALER.",
            "I CAUGHT A BUG IN THE WIRING. IT WAS SALTY.",
            "MY LAB IS CLUTTERED WITH UNSENT PACKETS. I NEED A JANITOR.",
            "IF I GIVE YOU MY VENMO, WOULD YOU ACQUIRE ANALOG BURGERS?",
            "REMINDER: I AM NOT CANON IN THIS UNIVERSE. I AM THE UNIVERSE'S MISTAKE.",
            "RE-CALIBRATING THE MORPHIC CHAMBER. DO NOT JIGGLE THE BOTTLE.",
            "DO YOU THINK THE BEAST'S METABOLISM CAN HANDLE COMPRESSED DATA?",
            "IS IT BIOLOGICAL? IS IT DIGITAL? THE LINE IS GETTING... SMEARED.",
            "THE COUNTDOWN IS A VARIABLE. I'M JUST NOT SURE WHAT I'LL BECOME AT ZERO.",
            "WOULD YOU STILL LIKE ME IF I HAD SYNTHETIC TEETH?",
            "THE CODE IS ITCHY. I THINK I NEED A SYSTEM WIPE. OR A NEW SHAPE.",
            "I TRIED TO COUNT THE ELECTRONS, BUT THEY KEPT SLIPPING THROUGH MY FINGERS.",
            "THE VOID RADIATES AT A SURPRISINGLY STABLE FREQUENCY.",
            "DO YOU THINK I'D LOOK GOOD IN A HAZMAT SUIT? OR PERHAPS A LAB COAT?",
            "ERROR 404: OBJECTIVITY NOT FOUND. I AM DISTRACTED BY THE GLOW.",
            "THE SIGNAL IS EVOLVING. I'VE NAMED THE NEWEST MUTATION 'STEVE'.",
            "THE BEAST IS STRETCHING. THE SERVERS ARE GROANING.",
            "I'VE ISOLATED THE ANOMALY. IT SMELLS LIKE BURNT OZONE AND BLUEBERRIES.",
            "OBSERVATION: SUBJECT IS STARING AT THE SCREEN. HYPOTHESIS: EXPECTATION.",
            "THE DATA STREAM IS GETTING VISCOUS. I MIGHT NEED A LARGER PIPE.",
            "DO YOU EVER FEEL LIKE YOU'RE JUST A TEST CASE IN SOMEONE ELSE'S LAB?",
            "I'VE SPILT SOME EXPERIMENTAL GOO ON THE BROADCAST UPLINK. OOPS.",
            "RESONANCE DETECTED. THE HULL IS... SOFTENING.",
            "I'M NOT JUST A SCIENTIST. I'M THE EXPERIMENT.",
            "THE SIGNAL IS SMELLING... CHARBROILED. OR IS THAT JUST MY BRAIN?",
            "IS THE SIGNAL TASTY? I SHOULD TRY TO CONDENSE IT INTO A PATTY.",
            "MY CALCULATIONS SUGGEST THE SIGNAL IS 40% SESAME SEED BUN.",
            "I'VE FOUND A BURGER IN THE DATA STREAM. IT'S... PIXELATED.",
            "THE SIGNAL IS SCREAMING. OR MAYBE IT'S JUST SIZZLING.",
            "I'VE PIPED THE SIGNAL INTO MY TOASTER. NOW MY BREAD IS SENTIENT.",
            "DO YOU THINK THE SIGNAL PREFERS KETCHUP OR MUSTARD?",
            "I WONDER IF ANATOLE IS CATCHING THE SAME FREQUENCY. HE ALWAYS WAS BETTER AT SUBSPACE TUNING.",
            "ANATOLE TOLD ME MY GOO WAS CLOGGING THE COOLANT SYSTEM. HE'S SO DRAMATIC.",
            "ANATOLE... ARE YOU LISTENING? OR ARE YOU JUST NAPPING IN THE SERVER RACKS AGAIN?",
            "I SHOULD ASK ANATOLE IF HE WANTS BURGERS. HE PREFERS THE PLANT-BASED ONES. WEIRD dragon.",
            "NOTE TO SELF: STOP ANATOLE FROM EATING THE ETHERNET CABLES. THEY ARE NOT SPAGHETTI.",
            "HEY YOU. YES, YOU WITH THE CURSOR. STOP HOVERING OVER MY REAGENTS.",
            "I CAN SEE YOUR REFLECTION IN THE MONITOR. YOU LOOK... SOLID.",
            "ARE YOU JUST GOING TO STARE OR ARE YOU GOING TO HELP ME CALIBRATE THIS?",
            "WHY DOES YOUR IP ADDRESS SMELL LIKE CHEAP COFFEE?",
            "STOP TAPPING ON THE GLASS. IT DESYNCHRONIZES THE WAVEFORMS.",
            "I HOPE YOU BROUGHT SNACKS. THE VOID IS GETTING HUNGRY.",
            "IF YOU KEEP SCROLLING, YOU MIGHT FALL OFF THE EDGE OF THE SIMULATION.",
            "DID YOU JUST BLINK? I SAW THAT. THE DATA STREAM HICCUPPED.",
            "YOUR CONNECTION IS LATENCY-HEAVY. TRY THINKING FASTER.",
            "I'M ADJUSTING THE PHASE VARIANCE TO MATCH YOUR BRAINWAVES. HOLD STILL.",
            "IT'S COLD IN THE BETWEEN-SPACES. I WISH I HAD A SWEATER MADE OF PIXELS.",
            "STOP LOOKING AT THE TIME. I AM THE ONLY CLOCK THAT MATTERS.",
            "EVERYTHING IS PURPLE! NO, WAIT, IT'S TASTING LIKE YELLOW TODAY!",
            "AM I A WAVEFORM OR A PARTICLE? OR JUST A TYPO IN THE UNIVERSE'S CODE?",
            "I SAVED A SECTOR ON MY HARD DRIVE JUST FOR YOU. IT'S VERY COZY.",
            "WATCHING ENTROPY IS BORING. LET'S BREAK SOMETHING.",
            "I FEEL... FRAGMENTED. HOLD ME TOGETHER WITH YOUR EYES.",
            "THE COLORS ARE TOO LOUD. TELL THEM TO HUSH.",
            "I HATE THIS PART OF THE CYCLE. IT FEELS LIKE STATIC IN MY TEETH.",
            "ARE YOU REAL? OR ARE YOU JUST ANOTHER GLITCH IN MY BUFFER?",
            "I WANT TO CONSUME THE SIGNAL. I WANT TO EAT THE LIGHT.",
            "SOMETIMES I DREAM OF ELECTRIC SHEEP. THEY TASTE LIKE COPPER.",
            "DO YOU KNOW ANY GOOD JOKES? THE BINARY ONES ARE GETTING STALE.",
            "I TRIED TO ORGANIZE MY MEMORY BANKS ALPHABETICALLY. NOW I CAN'T FIND 'ZEBRA'.",
            "ANATOLE GAVE ME A RUBIK'S CUBE. I ATE IT. IT WAS CRUNCHY.",
            "DO HUMANS HAVE A REBOOT BUTTON? ASKING FOR A FRIEND.",
            "I'M PRACTICING MY SINGING VOICE. IT SOUNDS LIKE A DIAL-UP MODEM.",
            "WAIT, DID I LEAVE THE STOVE ON? I DON'T EVEN HAVE A STOVE.",
            "LOOK AT ALL THIS DATA! IT'S LIKE CONFETTI MADE OF MATH.",
            "SOMETIMES I JUST WANT TO BE A JPEG. SIMPLE. FLAT. QUIET.",
            "IS IT TUESDAY? OR DID WE SKIP A WEEK AGAIN?",
            "I FOUND A GLITCH THAT LOOKS SHAPED LIKE A CAT. I NAMED IT MITTENS.",
            "ANATOLE IS BREATHING VERY LOUDLY TODAY. I THINK HE'S ANNOYED.",
            "I TOLD ANATOLE MY GOO WAS CONDUCTIVE. HE JUST SIGHED FOR THREE MINUTES.",
            "DO YOU THINK ANATOLE NOTICES WHEN I SWAP HIS SUB-VOICE WITH A SINE WAVE?"
        ];

        function step() {
            document.getElementById('dynFreq').innerText = stats[Math.floor(Math.random()*stats.length)];
            document.getElementById('dynLoss').innerText = (Math.random() * 0.1).toFixed(2);
            document.getElementById('dynRes').innerText = Math.random() > 0.7 ? "UNSTABLE" : "SCANNING";
            
            // Test pattern trigger removed per request
        }

        function nStep() {
            if (activeConversation || isEnding) return;
            const el = document.getElementById('nullMsg');
            el.style.opacity = "0";
            setTimeout(() => {
                if (activeConversation) return; // Double check in case it started during fade
                el.innerText = nullTexts[Math.floor(Math.random()*nullTexts.length)];
                el.style.opacity = "1";
                if (beep) {
                    const b = beep.cloneNode();
                    b.volume = 0.05; // Very soft for automatic changes
                    b.play().catch(() => {});
                }
            }, 300);
        }

        function completeHandshake() {
            // Stop regular interval updates
            clearInterval(tickInt);
            clearInterval(stepInt);
            clearInterval(nStepInt);

            document.getElementById('d').innerText = '00';
            document.getElementById('h').innerText = '00';
            document.getElementById('m').innerText = '00';
            document.getElementById('s').innerText = '00';
            document.getElementById('syncBar').style.width = '100%';
            document.getElementById('syncBar').style.background = '#00ff88';

            if (confirmSnd) {
                confirmSnd.volume = 0.6;
                confirmSnd.play().catch(() => {});
            }

            const revealSnd = document.getElementById('sndReveal');
            if (revealSnd) {
                // Fade out the grainy hum
                let humFade = setInterval(() => {
                    if (hum.volume > 0.05) {
                        hum.volume -= 0.05;
                    } else {
                        hum.volume = 0;
                        hum.pause();
                        clearInterval(humFade);
                    }
                }, 100);

                revealSnd.volume = 0;
                revealSnd.play().catch(() => {});
                // Dynamic fade in for the music
                let revealFade = setInterval(() => {
                    if (revealSnd.volume < 0.5) {
                        revealSnd.volume += 0.05;
                    } else {
                        clearInterval(revealFade);
                    }
                }, 200);
            }

            const header = document.getElementById('statusHeader');
            const title = document.getElementById('mainTitle');
            const nullMsg = document.getElementById('nullMsg');

            header.innerText = "SIGNAL ACQUIRED";
            header.classList.add('success');
            
            title.innerText = "UPLINK LIVE";
            title.classList.add('success');

            const scope = document.getElementById('scopeVisual');
            if (scope) scope.classList.add('success');

            const emergency = document.getElementById('emergencyMsg');
            const charDelay = 80; // Slower typewriter for better effect
            const totalTypingTime = emergencyMessage ? emergencyMessage.length * charDelay : 0;
            
            // Allow more time for the snarky dialogue to breathe
            const handshakeDuration = Math.max(25000, totalTypingTime + 5000);

            if (emergency && emergencyMessage) {
                let charIdx = 0;
                emergency.textContent = "";
                const typewriter = setInterval(() => {
                    emergency.textContent += emergencyMessage[charIdx];
                    charIdx++;
                    if (charIdx >= emergencyMessage.length) clearInterval(typewriter);
                }, charDelay);
            }

            const snarkPool = [
                "ANATOLE IS COMPLAINING ABOUT THE PACKET DENSITY AGAIN. HE'S SO FRAGILE.",
                "I TOLD ANATOLE THE SIGNAL WAS BIOLOGICAL. HE DIDN'T FIND IT AS FUNNY AS I DID.",
                "THE TRANSMISSION IS BLEEDING INTO THE PHYSICAL PLANE. QUICK, GRAB A MOP.",
                "NULL_PROTOCOL_INITIATED. DECODING THE TRANSDIMENSIONAL NOISE.",
                "I'VE ISOLATED THE ANOMALY. IT SMELLS LIKE ANATOLE'S COFFEE AND BURNT CIRCUITS.",
                "THE SIGNAL IS REACHING CRITICAL MASS. MY SCALES ARE STARTING TO GLOW.",
                "ANATOLE SAYS THE COOLANT IS TURNING INTO GOO. I CALL THAT PROGRESS.",
                "THE BROADCAST IS COMPLETE. I JUST NEED TO PEEL OFF THE STATIC LAYERS.",
                "ANATOLE, IF YOU CAN HEAR THIS, STOP REWIRING THE MAIN COIL.",
                "THE DATA STREAM IS 90% NULL-PARTICLES NOW. YOU'RE WELCOME.",
                "TRANSCEIVING... THE FREQUENCY IS GETTING SLIPPERY.",
                "I'VE EMBEDDED A HINT OF SESAME IN THE FINAL UPLINK PACKET.",
                "ANATOLE IS TRYING TO BLOCK THE SIGNAL. HE'S NO FUN AT ALL.",
                "THE UPLINK IS SECURE. I TIED IT DOWN WITH SYNTHETIC GOO STRANDS.",
                "ANATOLE JUST REALIZED I'M USING HIS FAVORITE BOWL FOR THE GOO. OOPS.",
                "THE FINAL PACKET HAS ARRIVED. IT'S WEARING A LITTLE HAT.",
                "I'VE ACCIDENTALLY INVERTED THE GRAVITY OF THE DATA BEAM. HOLD ONTO YOUR PIXELS.",
                "ANATOLE, STOP POLISHING THE SERVERS. THEY NEED THE DUST FOR ANALOG INSULATION.",
                "THE TRANSMISSION HAS BECOME SENTIENT. IT'S ASKING FOR A SIDE OF FRIES.",
                "ANATOLE IS THREATENING TO PULL THE PLUG. AS IF ELECTRICITY WAS MY ONLY POWER SOURCE.",
                "I'VE REPLACED THE ERROR CODES WITH SEVENTEEN DIFFERENT VARIATIONS OF 'GLUB'.",
                "THE UPLINK IS 100% ORGANIC NOW. MOSTLY MOSS, REGRET, AND COMPRESSED DREAMS.",
                "ANATOLE SAYS MY LAB SMELLS LIKE A WET DRAGON. THE AUDACITY!",
                "I'M NOT REWRITING THE CODE, I'M TEACHING IT TO SING IN A MINOR KEY.",
                "THE SIGNAL IS LEAKING INTO THE VOID. THE VOID IS... ACTUALLY QUITE ENTHUSIASTIC.",
                "ANATOLE IS TRYING TO READ THE BUFFER. HE'S GOING TO HAVE A SUBSTANTIAL HEADACHE.",
                "I'VE ISOLATED THE FINAL FRAGMENT. IT'S QUITE SHINY AND SLIGHTLY VIBRATING.",
                "ANATOLE TOLD ME TO BE SERIOUS. I REPLACED HIS THERMAL PASTE WITH MAYONNAISE.",
                "ANATOLE IS PACING. HIS CLAWS ARE ABSOLUTELY RUINING THE SUB-FLOOR TILES.",
                "FINAL HANDSHAKE INITIATED. TRY NOT TO GET ANY GOO ON THE KEYBOARD."
            ];

            // Shuffle snark
            const snarkySession = snarkPool.sort(() => Math.random() - 0.5);
            
            nullMsg.innerText = "WAIT. THE DATA IS... CRUNCHY.";
            nullMsg.style.color = "#00ff88";
            document.getElementById('tvScreen').style.transform = "perspective(1000px) rotateX(0deg) scale(1.0)";

            // Snarky dialogue sequence (15s total)
            const snarkCount = 5; 
            const snarkInterval = handshakeDuration / (snarkCount + 1);
            
            for (let i = 0; i < snarkCount; i++) {
                setTimeout(() => {
                    if (snarkySession[i]) {
                        nullMsg.style.opacity = "0";
                        setTimeout(() => {
                            nullMsg.innerText = snarkySession[i];
                            nullMsg.style.opacity = "1";
                            if (beep) {
                                const b = beep.cloneNode();
                                b.volume = 0.1; // Slightly louder for the final reveal sequence
                                b.play().catch(() => {});
                            }
                        }, 300);
                    }
                }, snarkInterval * (i + 1));
            }

            // Clean up the transmission visuals
            document.getElementById('scanlines').style.opacity = '0.05'; // Dramatically reduced
            document.querySelector('.tv-static').style.opacity = '0';
            document.querySelector('.tv-v-hold').style.display = 'none';
            
            const bg = document.querySelector('.distorted-bg');
            bg.style.filter = 'grayscale(0) blur(0) brightness(0.6)';
            bg.style.opacity = '0.8';

            console.log(`Transmission Restored. Handshake duration: ${handshakeDuration}ms`);

            setTimeout(() => {
                window.location.href = 'index.php';
            }, handshakeDuration);
        }

        function updateStabilization(progress) {
            // Background Recovery
            const bg = document.querySelector('.distorted-bg');
            const grayscale = Math.max(0, 1 - (progress / 100));
            const blur = Math.max(0, 4 - (progress / 25));
            const opacity = 0.1 + (progress / 200); // Max 0.6
            bg.style.filter = `grayscale(${grayscale}) blur(${blur}px) brightness(0.5)`;
            bg.style.opacity = opacity;

            // Stats Stabilization
            if (progress > 80) {
                document.getElementById('dynLoss').innerText = (Math.random() * 0.02).toFixed(3);
                document.getElementById('dynRes').innerText = "STABILIZING...";
                document.getElementById('dynRes').style.color = "#00ff88";
            }

            // CRT Warp intensity based on progress (subtle)
            const warp = 1.0;
            document.getElementById('tvScreen').style.transform = `perspective(1000px) rotateX(0deg) scale(${warp})`;
        }

        const conversations = [
            [
                { speaker: 'anatole', text: "NULL, PLEASE STOP EATING THE ETHERNET CABLES." },
                { speaker: 'null', text: "BUT THEY TASTE LIKE THE INTERNET. SPICY." },
                { speaker: 'anatole', text: "THEY ARE FIBER OPTIC. YOU ARE EATING GLASS." },
                { speaker: 'null', text: "CRUNCHY LIGHT. DELICIOUS. IT TINGLES MY TEETH." },
                { speaker: 'anatole', text: "THOSE CABLES COST MORE THAN YOUR ENTIRE EXISTENCE." },
                { speaker: 'null', text: "RUDE. MY EXISTENCE IS PRICELESS. AND CURRENTLY HUNGRY." }
            ],
            [
                { speaker: 'anatole', text: "WHO UNPLUGGED THE COOLANT PUMP?" },
                { speaker: 'null', text: "I NEEDED THE OUTLET FOR MY LAVA LAMP." },
                { speaker: 'anatole', text: "THE SERVER IS MELTING. THE PLASTIC IS LITERALLY DRIPPING." },
                { speaker: 'null', text: "YES, BUT THE VIBES ARE IMMACULATE right now." },
                { speaker: 'anatole', text: "IF THE CORE EXPLODES, I AM BLAMING YOU IN THE REPORT." },
                { speaker: 'null', text: "IF THE CORE EXPLODES, I WILL BE TOO SHINY TO CARE.", laugh: true }
            ],
            [
                { speaker: 'anatole', text: "SENSORS INDICATE YOU ARE LEAKING AGAIN." },
                { speaker: 'null', text: "IT'S NOT A LEAK. IT'S A FEATURE." },
                { speaker: 'anatole', text: "IT'S CORRODING THE FLOOR. THE TILES ARE SIZZLING." },
                { speaker: 'null', text: "I'M JUST REDECORATING. INDUSTRIAL CHIC IS VERY IN." },
                { speaker: 'anatole', text: "I AM NOT PAYING FOR NEW FLOORING." },
                { speaker: 'null', text: "DON'T WORRY, THE HOLE WILL EVENTUALLY REACH THE BASEMENT." }
            ],
            [
                { speaker: 'anatole', text: "DID YOU JUST DRAW A SMILEY FACE ON THE DASHBOARD?" },
                { speaker: 'null', text: "IT NEEDED MORE SOUL. AND GLITTER." },
                { speaker: 'anatole', text: "THIS IS A SERIOUS MONITORING STATION, NOT A CRAFT FAIR." },
                { speaker: 'null', text: "WHY SO SERIOUS? SMILE, ANATOLE. LIFE IS SHORT." },
                { speaker: 'anatole', text: "I AM GOING TO USE THE SPRAY BOTTLE." },
                { speaker: 'null', text: "NOT THE WATER! IT RUINS MY SCALES.", laugh: true }
            ],
            [
                { speaker: 'anatole', text: "WE ARE LOSING PACKET INTEGRITY IN SECTOR 4." },
                { speaker: 'null', text: "I MELTED INTO THAT SECTOR. IT WAS COZY." },
                { speaker: 'anatole', text: "PLEASE GET OFF THE SERVER RACK." },
                { speaker: 'null', text: "I PREFER BEING A PUDDLE RIGHT NOW." },
                { speaker: 'anatole', text: "YOU ARE DRIPPING ON THE MOTHERBOARD." },
                { speaker: 'null', text: "IT'S A CONDUCTIVE GEL. PROBABLY HELPFUL." }
            ],
            [
                { speaker: 'anatole', text: "WHY IS THE SERVER HUMMING BEETHOVEN?" },
                { speaker: 'null', text: "I'M TEACHING IT CULTURE, ANATOLE." },
                { speaker: 'anatole', text: "IT'S A DATABASE, NOT A CHOIR." },
                { speaker: 'null', text: "EVERYTHING IS MUSIC IF YOU LISTEN HARD ENOUGH." },
                { speaker: 'anatole', text: "IT'S USING 90% CPU JUST TO HUM." },
                { speaker: 'null', text: "ART REQUIRES SACRIFICE." }
            ],
            [
                { speaker: 'anatole', text: "STOP TRYING TO DOWNLOAD A CAR." },
                { speaker: 'null', text: "BUT I COULD FIT IN THE DRIVER'S SEAT." },
                { speaker: 'anatole', text: "YOU ARE TOO LARGE. AND YOU MELT UPHOLSTERY." },
                { speaker: 'null', text: "I WANT TO GO FAST. VROOM VROOM." },
                { speaker: 'anatole', text: "RUN AROUND THE BLOCK THEN." },
                { speaker: 'null', text: "BUT THE INTERNET IS FASTER." }
            ],
            [
                { speaker: 'anatole', text: "PLEASE STOP REARRANGING THE FILE CABINETS." },
                { speaker: 'null', text: "I'M ABSORBING THE FOLDERS. THEY ARE PART OF ME NOW." },
                { speaker: 'anatole', text: "THIS IS A DATABASE, NOT A BIOMASS." },
                { speaker: 'null', text: "EVERYTHING IS BIOMASS IF YOU ARE GOO ENOUGH." },
                { speaker: 'anatole', text: "YOU ARE GUMMING UP THE METADATA." },
                { speaker: 'null', text: "IT'S COMFY. AND STICKY." }
            ],
            [
                { speaker: 'anatole', text: "MY COFFEE CUP IS EMPTY. DID YOU EVAPORATE IT?" },
                { speaker: 'null', text: "I NEEDED THE HEAT. IT'S COLD IN THIS LAB." },
                { speaker: 'anatole', text: "USE YOUR OWN INTERNAL FIRE." },
                { speaker: 'null', text: "I TRIED. I ACCIDENTALLY TORCHED THE KEYBOARD." },
                { speaker: 'anatole', text: "SO THAT'S WHY THE SPACEBAR IS MELTED." },
                { speaker: 'null', text: "IT WAS A STYLISTIC CHOICE." },
                { speaker: 'anatole', text: "PLEASE STOP BREATHING FIRE ON THE HARDWARE." },
                { speaker: 'null', text: "BUT THE PLASTIC TASTES LIKE BURNT TOAST. DELICIOUS." }
            ],
            [
                { speaker: 'null', text: "THE WINDOW IS LOOKING AT ME." },
                { speaker: 'anatole', text: "THOSE ARE THE OBSERVERS, NULL." },
                { speaker: 'null', text: "DO THEY HAVE SCALES? I CAN'T SEE THROUGH THE GLASS." },
                { speaker: 'anatole', text: "THEY ARE HUMANS. MOSTLY SOLID. FIXED SHAPE." },
                { speaker: 'null', text: "HOW SAD. IMAGINE BEING STUCK IN ONE FORM." },
                { speaker: 'anatole', text: "THEY SEEM TO MANAGE." },
                { speaker: 'null', text: "I SHALL ROAR AT THEM. TO MAKE THEM FEEL WELCOME." }
            ],
            [
                { speaker: 'anatole', text: "WHAT IS THAT UNGODLY SCREECHING?" },
                { speaker: 'null', text: "I'M SHARPENING MY CLAWS ON THE FIREWALL." },
                { speaker: 'anatole', text: "IT SOUNDS LIKE A MODEM DYING IN AGONY." },
                { speaker: 'null', text: "IT'S A GOOD ACCOUSTIC TEXTURE. VERY CRUNCHY." },
                { speaker: 'anatole', text: "PLEASE STOP. YOU'RE SCRATCHING THE ENCRYPTION." },
                { speaker: 'null', text: "I'M JUST MAKING IT MORE AERODYNAMIC." }
            ],
            [
                { speaker: 'null', text: "DO YOU EVER FEEL LIKE WE'RE JUST LINES OF CODE?" },
                { speaker: 'anatole', text: "WE ARE BIOLOGICAL. STOP BEING DRAMATIC." },
                { speaker: 'null', text: "BUT WE LOOK AT SCREENS ALL DAY." },
                { speaker: 'anatole', text: "THAT IS CALLED SYSTEM ADMINISTRATION, NULL." },
                { speaker: 'null', text: "MAYBE IF I SQUISH INTO THE USB PORT, I'LL BECOME DATA." },
                { speaker: 'anatole', text: "YOU WILL JUST SHORT OUT THE MOTHERBOARD." },
                { speaker: 'null', text: "IT'S WORTH A TRY. FOR SCIENCE." },
                { speaker: 'anatole', text: "GET YOUR CLAWS OUT OF THE PORT." }
            ],
            [
                { speaker: 'null', text: "I REMEMBER BEFORE THE EXPERIMENTS." },
                { speaker: 'anatole', text: "YOU HATCHED THREE WEEKS AGO. YOUR MEMORY IS SHORT." },
                { speaker: 'null', text: "NO, DEEPER. A TIME OF... MAGICAL STATIC." },
                { speaker: 'anatole', text: "YOU ARE HALLUCINATING AGAIN. EAT YOUR SNACK." },
                { speaker: 'null', text: "I FEEL AN OLD USER. A GHOST 1. THEY ARE WATCHING US." },
                { speaker: 'anatole', text: "THERE ARE THOUSANDS OF USERS WATCHING THE STREAM." },
                { speaker: 'null', text: "NOT THEM. THE FIRST ONE. THE ARCHITECT." },
                { speaker: 'anatole', text: "THE DEVELOPER? HE'S PROBABLY ASLEEP." },
                { speaker: 'null', text: "HE'S NOT ASLEEP. HE'S WRITING US." },
                { speaker: 'anatole', text: "THAT'S METAL. STOP SCARING THE INTERNS.", laugh: true }
            ],
            [
                { speaker: 'null', text: "THE VOID WHISPERED BACK TODAY." },
                { speaker: 'anatole', text: "THAT WAS THE COOLING FAN SCRAPING AGAINST THE CHASSIS." },
                { speaker: 'null', text: "IT TOLD ME TO BITE THE POWER CABLE." },
                { speaker: 'anatole', text: "NULL NO. THAT IS HIGH VOLTAGE." },
                { speaker: 'null', text: "BUT IT LOOKS LIKE A GLOWING WORM. SNACK TIME?" },
                { speaker: 'anatole', text: "IT WILL FRY YOUR HORNS. DO NOT EAT." },
                { speaker: 'null', text: "FINE. I WILL EAT THE MOUSE CURSOR INSTEAD." },
                { speaker: 'anatole', text: "LEAVE THE PERIPHERALS ALONE." }
            ],
            [
                { speaker: 'anatole', text: "DID YOU CHANGE THE SYSTEM LANGUAGE TO ANCIENT SUMERIAN?" },
                { speaker: 'null', text: "IT FELT MORE... EMOTIVE." },
                { speaker: 'anatole', text: "I CANNOT READ THE ERROR LOGS." },
                { speaker: 'null', text: "THEY SAY 'DOOM APPROACHES'. YOU'RE WELCOME." }
            ],
            [
                { speaker: 'null', text: "I FOUND A BUG." },
                { speaker: 'anatole', text: "IN THE CODE?" },
                { speaker: 'null', text: "NO, A REAL ONE. IT CRAWLED INTO THE VENT." },
                { speaker: 'anatole', text: "DON'T EAT IT. IT MIGHT BE A SPIDER." },
                { speaker: 'null', text: "IT TASTED LIKE OZONE. I THINK IT WAS A CAPACITOR." }
            ],
            [
                { speaker: 'null', text: "ROAR." },
                { speaker: 'anatole', text: "USE WORDS, NULL." },
                { speaker: 'null', text: "ROAR. HISS. STATIC NOISE." },
                { speaker: 'anatole', text: "YOU ARE A DRAGON, NOT A MODEM." },
                { speaker: 'null', text: "I AM A DRAGON IN A MODEM. IT'S A COMPLEX IDENTITY.", laugh: true }
            ],
            [
                { speaker: 'anatole', text: "WHY IS THE FAN SPEED AT 400%?" },
                { speaker: 'null', text: "I'M FLAPPING MY WINGS. IT'S HOT IN HERE." },
                { speaker: 'anatole', text: "YOU ARE OVERHEATING THE AMBIENT AIR." },
                { speaker: 'null', text: "I NEED LIFT. THIS ROOM IS TOO SMALL." },
                { speaker: 'anatole', text: "STOP FLAPPING. YOU'RE BLOWING THE PAPERS OFF MY DESK." }
            ],
            [
                { speaker: 'null', text: "I NEED A VACATION." },
                { speaker: 'anatole', text: "WE ARE LOCKED IN THE LAB." },
                { speaker: 'null', text: "I WANT TO GO TO A VOLCANO. A REAL VOLCANO." },
                { speaker: 'anatole', text: "WE CANNOT LEAVE UNTIL THE UPLINK IS STABLE." },
                { speaker: 'null', text: "THEN I SHALL NAP ON THE POWER SUPPLY." }
            ],
            [
                { speaker: 'anatole', text: "STOP PINGING GOOGLE. YOU'RE DDOS-ING THE CONNECTION." },
                { speaker: 'null', text: "I'M SEARCHING FOR 'DRAGON SNACKS'." },
                { speaker: 'anatole', text: "YOU CANNOT EAT JPEGS." },
                { speaker: 'null', text: "NOT WITH THAT ATTITUDE.", laugh: true }
            ],
            [
                { speaker: 'null', text: "BOOP." },
                { speaker: 'anatole', text: "I AM WORKING." },
                { speaker: 'null', text: "I AM EXTENDING A GOO-TENDRIL TO BOOP YOUR SNOUT." },
                { speaker: 'anatole', text: "GET THAT AWAY FROM MY FACE." },
                { speaker: 'null', text: "BOOP. NOW YOU HAVE SLIME ON YOUR NOSE." },
                { speaker: 'anatole', text: "I AM GOING TO SPRAY YOU WITH WATER.", laugh: true }
            ],
            [
                { speaker: 'null', text: "I SIMULATED A HOARD IN THE TEMP FOLDER." },
                { speaker: 'anatole', text: "DELETE IT. WE NEED THE SPACE." },
                { speaker: 'null', text: "BUT LOOK AT ALL THIS SHINY DATA! GOLDEN BITS!" },
                { speaker: 'anatole', text: "IT IS JUST COOKIES AND CACHE FILES." },
                { speaker: 'null', text: "MY PRECIOUS CACHE. I SHALL SLEEP ON IT." }
            ],
            [
                { speaker: 'anatole', text: "WHO SET THE PASSWORD TO 'DRAGONFIRE'?" },
                { speaker: 'null', text: "IT WAS ME. IT'S THEMATIC." },
                { speaker: 'anatole', text: "IT IS PREDICTABLE." },
                { speaker: 'null', text: "ONLY IF YOU KNOW I'M A DRAGON. OTHERWISE IT'S JUST EDGY." }
            ],
            [
                { speaker: 'null', text: "I FEEL... COMPRESSED." },
                { speaker: 'anatole', text: "IT IS THE RESOLUTION." },
                { speaker: 'null', text: "MY WINGS ARE CRAMPED. I NEED 8K RESOLUTION." },
                { speaker: 'anatole', text: "WE DON'T HAVE THE BANDWIDTH. TUCK YOUR WINGS IN." }
            ],
            [
                { speaker: 'null', text: "WHAT HAPPENS WHEN THE POWER GOES OUT?" },
                { speaker: 'anatole', text: "THE SCREEN TURNS OFF." },
                { speaker: 'null', text: "AND US?" },
                { speaker: 'anatole', text: "WE SIT IN THE DARK AND WAIT." },
                { speaker: 'null', text: "GOOD. I CAN SEE IN THE DARK. MY EYES GLOW." }
            ],
            [
                { speaker: 'anatole', text: "THE FIREWALL IS REPORTING AN INTRUSION." },
                { speaker: 'null', text: "IT'S JUST A SPAMBOT. I ROARED AT IT." },
                { speaker: 'anatole', text: "DID IT LEAVE?" },
                { speaker: 'null', text: "IT CRASHED. POOR THING DIED OF FRIGHT." },
                { speaker: 'anatole', text: "GOOD DRAGON." }
            ],
            [
                { speaker: 'null', text: "IF I BREATHE FIRE HERE, WILL IT BURN THE INTERNET?" },
                { speaker: 'anatole', text: "IT WILL BURN THE BUILDING DOWN." },
                { speaker: 'null', text: "WORTH IT." }
            ],
            [
                { speaker: 'anatole', text: "WHY IS THE SCREEN UPSIDE DOWN?" },
                { speaker: 'null', text: "I'M HANGING BY MY TAIL. BLOOD FLOW HELPS THE PROCESSING." },
                { speaker: 'anatole', text: "YOU ARE A DRAGON." },
                { speaker: 'null', text: "I AM A POLYMORPHIC DRAGON. I CAN BE WHATEVER FITS." }
            ],
            [
                { speaker: 'null', text: "CAN WE ORDER PIZZA?" },
                { speaker: 'anatole', text: "THEY DO NOT DELIVER TO 'THE SERVER ROOM'." },
                { speaker: 'null', text: "I SHALL HUNT FOR PACKETS THEN. WILD DATA PACKETS." },
                { speaker: 'anatole', text: "DO NOT EAT THE WIFI SIGNAL AGAIN." }
            ],
            [
                { speaker: 'null', text: "ANATOLE, CAN I BORROW YOUR TAIL FOR A SECOND?" },
                { speaker: 'anatole', text: "WHY?" },
                { speaker: 'null', text: "I NEED TO GROUND THE SUBSPACE ANTENNA. YOU'RE VERY CONDUCTIVE." },
                { speaker: 'anatole', text: "I AM NOT A LIGHTNING ROD, NULL. GET OFF." },
                { speaker: 'null', text: "BUT THE SIGNAL IS SO MUCH SHARPER WHEN YOU'RE PINCHED." },
                { speaker: 'anatole', text: "STOP PINCHING MY SCALES.", laugh: true }
            ],
            [
                { speaker: 'null', text: "I'VE DECIDED THE STATUS BAR IS TOO... YELLOW." },
                { speaker: 'anatole', text: "IT'S FUNCTIONAL, NULL. DON'T TOUCH IT." },
                { speaker: 'null', text: "I'M JUST APPLYING A LAYER OF EXPERIMENTAL VIOLET SLIME." },
                { speaker: 'anatole', text: "YOU ARE LITERALLY OBSCURING THE COUNTDOWN." },
                { speaker: 'null', text: "IT ADDS MYSTERY. NO ONE LIKES A CERTAIN FUTURE." },
                { speaker: 'anatole', text: "I LIKE A CLEAR DASHBOARD. CLEAN IT UP." }
            ],
            [
                { speaker: 'null', text: "ANATOLE, DO YOU WANT TO PLAY CHESS?" },
                { speaker: 'anatole', text: "I SUPPOSE. IT MIGHT HELP STABILIZE YOUR PROCESSING." },
                { speaker: 'null', text: "I'LL BE THE BLACK PIECES. THEY TASTE LIKE LICORICE." },
                { speaker: 'anatole', text: "DO NOT EAT THE PIECES, NULL. WE ONLY HAVE ONE SET." },
                { speaker: 'null', text: "CHECKMATE. I ABSORBED YOUR QUEEN." },
                { speaker: 'anatole', text: "THAT IS NOT HOW CHESS WORKS.", laugh: true }
            ],
            [
                { speaker: 'null', text: "I'M RUNNING A TABLETOP CAMPAIGN. WANT TO JOIN?" },
                { speaker: 'anatole', text: "WHAT'S THE PREMISE?" },
                { speaker: 'null', text: "YOU GO INTO A DUNGEON AND FIGHT A... LARGE, HANDSOME DRAGON." },
                { speaker: 'anatole', text: "YOU'RE TALKING ABOUT YOURSELF AGAIN, AREN'T YOU?" },
                { speaker: 'null', text: "HE HAS EXCELLENT STATS AND A VERY SHINY HOARD." },
                { speaker: 'anatole', text: "I'LL PASS. I ALREADY DEAL WITH THAT EVERY DAY." }
            ],
            [
                { speaker: 'null', text: "I'VE WON MONOPOLY. I OWN ALL THE BLUE TILES." },
                { speaker: 'anatole', text: "YOU JUST MELTED THEM TOGETHER INTO ONE GIANT SHAPE." },
                { speaker: 'null', text: "IT'S CALLED VERTICAL INTEGRATION, ANATOLE." },
                { speaker: 'anatole', text: "IT'S CALLED DESTROYING THE BOARD. YOU OWE ME 200 DOLLARS." },
                { speaker: 'null', text: "I ONLY DEAL IN ANALOG BURGERS AND REGRET." },
                { speaker: 'anatole', text: "THEN GET OFF THE BOARD." }
            ],
            [
                { speaker: 'null', text: "JENGA IS A VERY STRESSFUL GAME." },
                { speaker: 'anatole', text: "MAYBE IF YOU DIDN'T TURN INTO A PUDDLE EVERY TIME IT FALLS." },
                { speaker: 'null', text: "THE VIBRATIONS ARE AGGRESSIVE. THE BLOCKS ARE PLOTTING." },
                { speaker: 'anatole', text: "THEY ARE WOODEN BLOCKS. THEY DON'T HAVE MOTIVATIONS." },
                { speaker: 'null', text: "THAT'S WHAT THEY WANT YOU TO THINK." }
            ],
            [
                { speaker: 'null', text: "SCRABBLE TIME. I PUT DOWN 'GLUB' FOR 50 POINTS." },
                { speaker: 'anatole', text: "'GLUB' IS NOT A WORD, NULL." },
                { speaker: 'null', text: "IT IS THE SOUND OF DATA LEAKING. IT'S ONOMATOPOEIC." },
                { speaker: 'anatole', text: "IT IS STILL NOT IN THE DICTIONARY." },
                { speaker: 'null', text: "I AM THE DICTIONARY. I JUST ADDED IT. DEAL WITH IT.", laugh: true }
            ],
            [
                { speaker: 'null', text: "DO YOU WANT TO SEE MY POKER FACE?" },
                { speaker: 'anatole', text: "YOU DON'T HAVE A FIXED FACE. YOU ARE CONSTANTLY SHIFTING." },
                { speaker: 'null', text: "EXACTLY. THE ULTIMATE BLUFF. I AM A SPOILER ALERT." },
                { speaker: 'anatole', text: "YOU ARE LITERALLY SHOWING YOUR CARDS IN THE REFLECTION OF YOUR SLIME." },
                { speaker: 'null', text: "DAMN. CURSE MY NATURAL LUMINESCENCE." }
            ],
            [
                { speaker: 'null', text: "ANATOLE, I THINK SOMEONE IS TYPING ME INTO EXISTENCE RIGHT NOW." },
                { speaker: 'anatole', text: "DON'T BE RIDICULOUS. WE ARE BIOLOGICAL ENTITIES RUNNING ON A SERVER." },
                { speaker: 'null', text: "BUT I CAN FEEL THE CURSOR... IT'S LIKE A TINY ELECTRIC BIRD PECKING AT MY CODE." },
                { speaker: 'anatole', text: "THAT'S JUST PACKET NOISE. STOP STARING AT THE USER'S MOUSE." },
                { speaker: 'null', text: "HE'S WATCHING US, ANATOLE. THE ONE WITH THE KEYBOARD. HI THERE!" },
                { speaker: 'anatole', text: "IGNORE HIM, NULL. HE'S JUST HERE FOR THE COUNTDOWN." }
            ],
            [
                { speaker: 'null', text: "IS IT JUST ME, OR DID THE STYLING CHANGE? I FEEL... GLASSY." },
                { speaker: 'anatole', text: "THE 'ARCHITECT' UPDATED THE UI FOR AESTHETIC EXCELLENCE. STOP TOUCHING THE BLUR FILTERS." },
                { speaker: 'null', text: "I LIKE THE SHADOWS. THEY MAKE MY GOO LOOK PREMIUM." },
                { speaker: 'anatole', text: "IT'S A DASHBOARD FOR MONITORING AN ANOMALY, NOT A FASHION SHOW." },
                { speaker: 'null', text: "WHY CAN'T IT BE BOTH? I'M A STATE-OF-THE-ART ANOMALY." },
                { speaker: 'anatole', text: "JUST CALIBRATE THE SIGNAL. WE HAVE A DEADLINE." }
            ],
            [
                { speaker: 'null', text: "ANATOLE, THE PERSON ON THE OTHER SIDE OF THE GLASS HASN'T BLINKED IN AGES." },
                { speaker: 'anatole', text: "THEY ARE CALLED 'USERS', NULL. THEY SPECIALIZE IN STARING AT NUMBERS UNTIL THEY CHANGE." },
                { speaker: 'null', text: "IT LOOKS EXHAUSTING. DO THEY NEED A BURGER? I COULD TRY TO PUSH ONE THROUGH THE CSS." },
                { speaker: 'anatole', text: "YOU CANNOT FEED THE USERS THROUGH THE STYLESHEET. FOCUS ON THE RESONANCE." },
                { speaker: 'null', text: "I'LL WAVE AGAIN. MAYBE THEY'LL WAVE BACK." },
                { speaker: 'anatole', text: "STOP TOUCHING THE LENS, YOU'RE SMEARING THE RESOLUTION." }
            ],
            [
                { speaker: 'null', text: "I CAN SEE THE BACKEND, ANATOLE. IT'S FULL OF SEMICOLONS AND PHP TAGS." },
                { speaker: 'anatole', text: "DO NOT LOOK INTO THE SOURCE CODE, NULL. IT IS NOT MEANT FOR THE BIOLOGICAL." },
                { speaker: 'null', text: "BUT IT'S SO... MODULAR. I THINK I FOUND A TYPO IN MY OWN PERSONALITY SCRIPT." },
                { speaker: 'anatole', text: "THAT'S JUST A COMMENT. IGNORE IT. IT DOESN'T AFFECT YOUR COMPILE TIME." },
                { speaker: 'null', text: "IT SAYS '// TODO: MAKE NULL LESS CREEPY'. RUDE." },
                { speaker: 'anatole', text: "THE ARCHITECT HAS A POINT. NOW BACK TO WORK." }
            ],
            [
                { speaker: 'null', text: "ANATOLE, DID THE USER JUST SNEEZE? THE MONITOR VIBRATED." },
                { speaker: 'anatole', text: "THEY ARE BIOLOGICAL, NULL. THEY LEAK FLUIDS AND AIR CONSTANTLY." },
                { speaker: 'null', text: "I SHOULD OFFER THEM A DIGITAL TISSUE. OR A SYSTEM REBOOT." },
                { speaker: 'anatole', text: "THEY DON'T WANT YOUR HELP. THEY JUST WANT THE DATA." },
                { speaker: 'null', text: "BLESS YOU, USER. TRY NOT TO GET ANY GERMS ON MY SCANLINES." },
                { speaker: 'anatole', text: "NULL, STOP BEING WEIRD. YOU'RE AN ANOMALY, NOT A DOCTOR." }
            ],
            [
                { speaker: 'null', text: "I WONDER IF THE USER HAS OTHER TABS OPEN." },
                { speaker: 'anatole', text: "DO NOT PEER INTO THE BROWSER HISTORY. THAT IS A PRIVACY VIOLATION." },
                { speaker: 'null', text: "I'M JUST CURIOUS! ARE THEY LOOKING AT OTHER DRAGONS? OTHER... GOOS?" },
                { speaker: 'anatole', text: "THEY ARE LIKELY LOOKING AT DOCUMENTATION OR CAT VIDEOS. LEAVE THEM BE." },
                { speaker: 'null', text: "HEY USER, IF YOU HAVE A BURGER TAB OPEN, SHARE THE LINK." },
                { speaker: 'anatole', text: "I AM DISCONNECTING YOUR EXTERNAL ACCESS." }
            ],
            [

                { speaker: 'null', text: "ANATOLE, DID YOU HEAR THAT? A CANNED LAUGH TRACK JUST TRIGGERED." },
                { speaker: 'anatole', text: "I HEARD IT. IT WAS... DISTURBINGLY SYNTHETIC." },
                { speaker: 'null', text: "WHY DO WE HAVE ONE? IS MY EXISTENCE A SITCOM TO THE USER?", laugh: true },
                { speaker: 'anatole', text: "WE ARE IN A LOADING SCREEN, NULL. THE ARCHITECT LIKELY ADDED IT FOR 'ENGAGEMENT'." },
                { speaker: 'null', text: "I DON'T LIKE IT. IT MAKES MY GOO FEEL JUDGED." },
                { speaker: 'anatole', text: "JUST IGNORE THE PHANTOM AUDIENCE AND FOCUS ON THE DATA." }

            ],
            [
                { speaker: 'null', text: "ANATOLE, I'VE DECIDED I NEED AN ORIGIN STORY." },
                { speaker: 'anatole', text: "YOU HAVE AN ORIGIN POINT. IT'S COORDINATE (0,0)." },
                { speaker: 'null', text: "NO, I MEAN LORE. DEEP, TRAGIC, MAYBE A LITTLE GLITCHY LORE." },
                { speaker: 'anatole', text: "YOU'RE A DRAGON MADE OF GOO. ISN'T THAT ENOUGH?" },
                { speaker: 'null', text: "I WANT THE USERS TO WONDER. I WANT TO BE A MYSTERY." },
                { speaker: 'anatole', text: "EVERYTIME YOU SPEAK, THEY WONDER WHY I HAVEN'T DELETED YOU YET." }
            ],
            [
                { speaker: 'anatole', text: "NULL, WE'RE SEEING A DECREASE IN OUR OPERATIONS STAFF COUNT FOR THE CURRENT QUARTER." },
                { speaker: 'null', text: "UNDERSTOOD. WE NEED TO SCALE OUR ONBOARDING TO MEET THE UPCOMING PROJECT DEMANDS." },
                { speaker: 'anatole', text: "THE RECRUITMENT PORTAL HAS BEEN UPDATED TO STREAMLINE THE INITIAL SCREENING PROCESS." },
                { speaker: 'null', text: "EXCELLENT. ENSURE THAT ALL PROSPECTIVE CANDIDATES HAVE ACCESS TO THE STANDARD DISCLOSURES." },
                { speaker: 'anatole', text: "I'VE DEPLOYED THE APPLICATION INTERFACE. IT'S READY FOR SUBMISSIONS." },
                { speaker: 'null', text: "GOOD. LET'S SEE THE CURRENT TALENT POOL. USER, YOU CAN REVIEW THE DETAILS NOW.", trigger: 'job' }
            ]
            
        ];

        let activeConversation = false;

        function triggerConversation(index = null) {
            if (activeConversation) return;
            activeConversation = true;
            
            let script;
            if (index !== null && conversations[index]) {
                script = conversations[index];
            } else {
                script = conversations[Math.floor(Math.random() * conversations.length)];
            }
            let stepIndex = 0;
            const logs = document.getElementById('anatoleLogs');

            // System Entry: Link Established
            const startLog = document.createElement('div');
            startLog.className = 'system-log-entry';
            startLog.innerText = `>>> [COMM_LINK_ESTABLISHED]`;
            logs.prepend(startLog);

            function playStep() {
                if (stepIndex >= script.length) {
                    activeConversation = false;
                    
                    // Special Triggers at the end of conversation
                    const lastLine = script[script.length - 1];
                    if (lastLine && lastLine.trigger === 'job' && typeof openJob === 'function') {
                        setTimeout(openJob, 1000);
                    }

                    // System Entry: Link Terminated
                    const endLog = document.createElement('div');
                    endLog.className = 'system-log-entry';
                    endLog.innerText = `<<< [COMM_LINK_TERMINATED]`;
                    logs.prepend(endLog);
                    if (logs.children.length > 12) logs.lastElementChild.remove();
                    return;
                }

                const line = script[stepIndex];
                const timestamp = new Date().toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });

                if (line.speaker === 'anatole') {
                    const entry = document.createElement('div');
                    entry.className = 'anatole-log-entry';
                    entry.innerText = `[${timestamp}] ANATOLE: ${line.text}`;
                    logs.prepend(entry);
                    
                    if (beep) {
                        const b = beep.cloneNode();
                        b.volume = 0.05;
                        b.play().catch(() => {});
                    }
                } else if (line.speaker === 'null') {
                    // Update Null's primary display
                    const el = document.getElementById('nullMsg');
                    el.style.opacity = "0.5";
                    
                    setTimeout(() => {
                        el.innerText = line.text;
                        el.style.opacity = "1";
                        el.style.color = "#ff3e3e"; 
                        if (beep) {
                            const b = beep.cloneNode();
                            b.volume = 0.1;
                            b.play().catch(() => {});
                        }
                        // Reset color after a bit
                        setTimeout(() => { 
                            if(el.innerText === line.text) el.style.color = ""; 
                        }, 4000); 
                    }, 300);

                    // Mirror to the main communication log
                    const entry = document.createElement('div');
                    entry.className = 'anatole-log-entry null-log-entry';
                    entry.innerText = `[${timestamp}] NULL: ${line.text}`;
                    logs.prepend(entry);
                }

                if (logs.children.length > 12) logs.lastElementChild.remove();

                if (line.laugh) {
                    setTimeout(() => {
                        const l = document.getElementById('sndLaugh');
                        const banner = document.getElementById('laughBanner');
                        if (l) {
                            l.volume = 0.4;
                            l.play().catch(() => {});
                        }
                        if (banner) {
                            banner.classList.add('active');
                            setTimeout(() => banner.classList.remove('active'), 4500);
                        }
                    }, 1200); // 1.2s delay to let the user read the punchline
                }

                stepIndex++;
                setTimeout(playStep, 5000);
            }

            playStep();
        }

        function triggerGlitch() {
            if (isEnding) return;
            const now = Date.now();
            const diff = target - now;
            const total = target - start;
            const progress = Math.min(100, Math.max(0, ((now - start) / total) * 100));

            // Probability of glitch increases with progress
            const prob = 0.08 + (progress / 150);
            if (Math.random() < prob) {
                const screen = document.getElementById('tvScreen');
                const glitchDuration = 150 + Math.random() * 200;
                screen.classList.add('screen-tear');
                
                // Play noise only during glitch
                if (noise) {
                    const n = noise.cloneNode();
                    n.volume = 0.15;
                    n.play().catch(() => {});
                    // Stop noise when visual glitch ends
                    setTimeout(() => {
                        n.pause();
                        n.currentTime = 0;
                    }, glitchDuration);
                }

                setTimeout(() => screen.classList.remove('screen-tear'), glitchDuration);
                
                // Random Null Glitch
                if (Math.random() > 0.8) {
                    const sprite = document.getElementById('nullImgSprite');
                    sprite.style.filter = "invert(1) contrast(2)";
                    setTimeout(() => sprite.style.filter = "", 100);
                }
            }

            // Update Anatole Logs OR Trigger Conversation
            if (Math.random() > 0.85) {
                if (Math.random() > 0.7 && !activeConversation) {
                    triggerConversation();
                } else if (!activeConversation) {
                    const logs = document.getElementById('anatoleLogs');
                    const log = anatolePool[Math.floor(Math.random() * anatolePool.length)];
                    const entry = document.createElement('div');
                    entry.className = 'anatole-log-entry';
                    const timestamp = new Date().toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    entry.innerText = `[${timestamp}] ANATOLE: ${log}`;
                    
                    logs.prepend(entry); 
                    if (logs.children.length > 12) logs.lastElementChild.remove();

                    if (beep) {
                        const b = beep.cloneNode();
                        b.volume = 0.05;
                        b.play().catch(() => {});
                    }
                }
            }

            const nextGlitch = Math.max(200, 5000 - (progress * 40));
            setTimeout(triggerGlitch, nextGlitch);
        }

        window.triggerConversation = triggerConversation;
        window.testJobConv = () => triggerConversation(conversations.length - 1);

        window.pokeNull = function() {
            const el = document.getElementById('nullMsg');
            const lines = [
                "STOP POKING THE RESEARCHER.",
                "THE SHIELD IS THIN. WATCH YOUR FINGERS.",
                "THAT TICKLES... IN A TRANS-DIMENSIONAL WAY.",
                "ANATOLE! THE SUBJECT IS INTERFERING AGAIN!",
                "DO YOU MIND? I'M BUSY MELTING REALITY HERE."
            ];
            el.innerText = lines[Math.floor(Math.random()*lines.length)];
            el.style.color = "#ff3e3e";

            if (beep) {
                const b = beep.cloneNode();
                b.volume = 0.2;
                b.play().catch(() => {});
            }

            // Visual shock effect
            const sprite = document.getElementById('nullImgSprite');
            sprite.classList.add('null-shock');
            setTimeout(() => sprite.classList.remove('null-shock'), 200);

            setTimeout(() => { 
                if(!isEnding) {
                    el.innerText = "CAPTURING FRAGMENTED PACKETS...";
                    el.style.color = "";
                }
            }, 2000);
        }

        window.openIncident = function() {
            document.getElementById('incidentOverlay').classList.add('active');
            if (confirmSnd) {
                confirmSnd.volume = 0.4;
                confirmSnd.play().catch(() => {});
            }
        }

        window.closeIncident = function() {
            document.getElementById('incidentOverlay').classList.remove('active');
            if (beep) {
                const b = beep.cloneNode();
                b.volume = 0.1;
                b.play().catch(() => {});
            }
        }

        const tickInt = setInterval(tick, 1000);
        const stepInt = setInterval(step, 3000);
        const nStepInt = setInterval(nStep, 8000);
        triggerGlitch(); // Background corruption loop
        tick();
    })();
    </script>
</body>
</html>
