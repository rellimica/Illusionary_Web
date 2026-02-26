<?php
/**
 * SYSTEM MAINTENANCE PAGE
 * Industrial Construction Theme feat. Null
 */
session_name('ILLUSIONARY_SID');
session_start();
require_once 'config.php';

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
    <title>MAINTENANCE IN PROGRESS | Illusionary</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;800&family=Inter:wght@400;600;900&family=Courier+Prime&family=Permanent+Marker&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        :root {
            --blueprint-blue: #003366;
            --blueprint-line: rgba(255, 255, 255, 0.15);
            --blueprint-accent: rgba(255, 255, 255, 0.4);
            --safety-orange: #ff9d00;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--blueprint-blue);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- BLUEPRINT BACKGROUND --- */
        .construction-bg {
            position: fixed; inset: 0; z-index: 1;
            background-color: var(--blueprint-blue);
            background-image: 
                /* Technical Grid */
                linear-gradient(var(--blueprint-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--blueprint-line) 1px, transparent 1px),
                /* Large Accent Grid */
                linear-gradient(rgba(255,255,255,0.05) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 2px, transparent 2px);
            background-size: 20px 20px, 20px 20px, 100px 100px, 100px 100px;
        }

        /* Blueprint Design Marks */
        .construction-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 10% 10%, transparent 0, transparent 200px, var(--blueprint-accent) 201px, transparent 202px),
                radial-gradient(circle at 90% 85%, transparent 0, transparent 300px, var(--blueprint-accent) 301px, transparent 302px);
            opacity: 0.3;
            pointer-events: none;
        }

        /* Paper Texture */
        .construction-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3F%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.04;
            mix-blend-mode: overlay;
            pointer-events: none;
        }

        /* Coordinate Grid Borders */
        .blueprint-border {
            position: fixed;
            inset: 20px;
            border: 2px solid var(--blueprint-line);
            pointer-events: none;
            z-index: 100;
        }
        .coord {
            position: absolute;
            font-family: 'Courier Prime', monospace;
            font-size: 0.7rem;
            color: var(--blueprint-accent);
            font-weight: 900;
        }
        .coord-v { left: -15px; width: 10px; text-align: center; }
        .coord-h { top: -15px; height: 10px; }

        /* Title Block / Stamp */
        .blueprint-stamp {
            position: fixed;
            bottom: 40px;
            right: 40px;
            border: 2px solid var(--blueprint-accent);
            background: rgba(0, 51, 102, 0.8);
            padding: 15px;
            width: 280px;
            z-index: 110;
            font-family: 'Courier Prime', monospace;
            text-transform: uppercase;
        }
        .stamp-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid var(--blueprint-line);
            padding: 5px 0;
            font-size: 0.65rem;
            color: var(--blueprint-accent);
        }
        .stamp-row b { color: #fff; }
        .stamp-title {
            font-size: 0.9rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        /* Hand-penned Note */
        /* Ball Pit Technical Plot */
        .ball-pit-blueprint {
            position: fixed;
            bottom: 120px;
            left: 10%;
            width: 280px;
            height: 280px;
            border: 1px dashed #00ffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
            text-align: center;
            padding: 20px;
        }

        .ball-pit-blueprint span {
            font-family: 'Courier Prime', monospace;
            font-size: 0.8rem;
            color: #00ffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Snack Bar Technical Plot */
        .snack-bar-blueprint {
            position: fixed;
            top: 150px;
            right: 15%;
            width: 200px;
            height: 90px;
            border: 1px dashed #00ff99;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
            text-align: center;
        }

        .snack-bar-blueprint span {
            font-family: 'Courier Prime', monospace;
            font-size: 0.8rem;
            color: #00ff99;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Gourmet Burger Kitchen */
        .burger-kitchen-blueprint {
            position: fixed;
            top: 40%;
            left: 18%;
            width: 150px;
            height: 150px;
            border: 1px dashed #ff9d00;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
            text-align: center;
        }
        .burger-kitchen-blueprint span {
            font-family: 'Courier Prime', monospace;
            font-size: 0.65rem;
            color: #ff9d00;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Licorice Silo */
        .licorice-silo-blueprint {
            position: fixed;
            bottom: 150px;
            right: 380px;
            width: 100px;
            height: 100px;
            border: 1px dashed #ff4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
            text-align: center;
        }
        .licorice-silo-blueprint span {
            font-family: 'Courier Prime', monospace;
            font-size: 0.65rem;
            color: #ff4444;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Anatole Bunker */
        .anatole-bunker-blueprint {
            position: fixed;
            bottom: 120px;
            left: 32%;
            width: 120px;
            height: 80px;
            border: 2px solid #a855f7;
            border-style: double;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
            text-align: center;
        }
        .anatole-bunker-blueprint span {
            font-family: 'Courier Prime', monospace;
            font-size: 0.6rem;
            color: #a855f7;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Hand-penned Note Style */
        .hand-note {
            position: fixed;
            font-family: 'Caveat', cursive;
            color: #fff;
            font-size: 1.4rem;
            opacity: 0.6;
            z-index: 50;
            max-width: 200px;
            line-height: 1;
            pointer-events: none;
        }
        .hand-note::before {
            content: '←';
            position: absolute;
            left: -35px;
            top: 0;
            font-size: 2rem;
        }

        .note-marker-fix { top: 120px; left: 15%; transform: rotate(3deg); }
        .note-burger-priority { top: 35%; right: 10%; transform: rotate(-8deg); color: #ff9d00; }
        .note-licorice-order { bottom: 250px; left: 28%; transform: rotate(-4deg); }
        .note-anatole-snark { bottom: 350px; left: 40%; transform: rotate(2deg); opacity: 0.4; }
        
        .note-no-arrow::before { display: none; }

        .note-no-arrow::before { display: none; }

        /* Technical Drafting Additions */
        .blueprint-ruler {
            position: fixed;
            bottom: 150px;
            left: 50%;
            transform: translateX(-50%);
            height: 15px;
            width: 400px;
            border-bottom: 1px solid var(--blueprint-accent);
            background-image: repeating-linear-gradient(90deg, var(--blueprint-accent), var(--blueprint-accent) 1px, transparent 1px, transparent 10px);
            z-index: 101;
        }
        .blueprint-ruler::after {
            content: 'SCALE 1:5000 [LOGIC_UNITS]';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.5rem;
            font-family: 'Courier Prime', monospace;
            color: var(--blueprint-accent);
        }

        .measurement-arc {
            position: fixed;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border: 1px dashed var(--blueprint-accent);
            border-radius: 50%;
            opacity: 0.2;
            z-index: 2;
        }
        .measurement-arc::before {
            content: '45.00° [SWING_RADIUS]';
            position: absolute;
            bottom: 80px;
            left: 40px;
            font-size: 0.6rem;
            color: var(--blueprint-accent);
            transform: rotate(-45deg);
        }

        .logic-circuit {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 2;
            opacity: 0.15;
        }
        .node {
            position: absolute;
            width: 6px;
            height: 6px;
            border: 1px solid #fff;
            border-radius: 50%;
            background: var(--blueprint-blue);
        }
        .wire {
            position: absolute;
            background: #fff;
            height: 1px;
        }

        /* Drafting Tape */
        .masking-tape {
            position: fixed;
            width: 80px;
            height: 25px;
            background: rgba(245, 245, 220, 0.4);
            z-index: 105;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        .tape-tl { top: 15px; left: 15px; transform: rotate(-40deg); }
        .tape-tr { top: 15px; right: 15px; transform: rotate(40deg); }
        .tape-bl { bottom: 15px; left: 15px; transform: rotate(45deg); }
        .tape-br { bottom: 15px; right: 15px; transform: rotate(-45deg); }

        /* Progress Checklist */
        .site-checklist {
            position: fixed;
            top: 100px;
            right: 60px;
            width: 180px;
            border: 1px solid var(--blueprint-accent);
            padding: 15px;
            font-family: 'Courier Prime', monospace;
            font-size: 0.65rem;
            color: var(--blueprint-accent);
            z-index: 10;
        }
        .checklist-title {
            font-weight: 900;
            margin-bottom: 8px;
            border-bottom: 1px solid var(--blueprint-accent);
            padding-bottom: 4px;
            text-align: center;
        }
        .task { margin-bottom: 4px; }
        .task.done { color: #fff; text-decoration: line-through; opacity: 0.5; }

        /* DRAFT Stamp */
        .draft-stamp {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 15rem;
            font-weight: 900;
            color: #fff;
            opacity: 0.03;
            pointer-events: none;
            z-index: 1;
            font-family: 'Inter', sans-serif;
            letter-spacing: 20px;
        }

        /* Diagonal Hazard Strips */
        .hazard-bar {
            position: fixed;
            height: 40px;
            width: 150%;
            background: repeating-linear-gradient(
                -45deg,
                var(--safety-orange),
                var(--safety-orange) 40px,
                #000 40px,
                #000 80px
            );
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 40px rgba(0,0,0,0.5);
        }
        .hazard-top { top: 0; left: -25%; transform: rotate(-2deg); }
        .hazard-bottom { bottom: 30px; left: -25%; transform: rotate(1deg); }

        .hazard-text {
            color: #fff;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
            font-weight: 900;
            font-size: 1.2rem;
            letter-spacing: 10px;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
        }

        /* --- MAIN CONTAINER --- */
        .site-container {
            position: relative;
            z-index: 20;
            width: 100%;
            max-width: 900px;
            padding: 40px;
            text-align: center;
        }

        .crane-box {
            position: relative;
            margin-bottom: 2rem;
            border: 1px dashed var(--blueprint-accent);
            padding: 40px;
            background: rgba(0, 51, 102, 0.4);
            backdrop-filter: blur(15px);
            border-radius: 4px;
            box-shadow: 0 0 50px rgba(0,0,0,0.3);
            z-index: 20;
        }

        .crane-box::after {
            content: '';
            position: absolute;
            inset: -5px;
            border: 1px solid var(--blueprint-line);
            pointer-events: none;
            opacity: 0.5;
        }

        /* Technical corner marks */
        .status-badge {
            background: var(--safety-orange);
            color: #000;
            padding: 4px 20px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 2px;
            display: inline-block;
            margin-bottom: 1rem;
            border-radius: 2px;
        }

        .maintenance-title {
            font-family: 'Inter', sans-serif;
            font-weight: 900;
            font-size: 4.5rem;
            line-height: 0.9;
            margin-bottom: 1.5rem;
            color: #fff;
            text-transform: uppercase;
        }

        .maintenance-subtitle {
            color: var(--text-muted);
            font-family: 'Courier Prime', monospace;
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* --- NULL POSITIONING --- */
        .null-construction {
            position: absolute;
            right: -50px;
            top: -80px;
            width: 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: floatNull 6s ease-in-out infinite;
            z-index: 1000;
        }

        @keyframes floatNull {
            0%, 100% { transform: translateY(0) rotate(2deg); }
            50% { transform: translateY(-20px) rotate(-1deg); }
        }

        .null-frame {
            width: 160px;
            height: 160px;
            background: #000;
            border: 2px solid #fff;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8), 0 0 20px rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .null-frame:hover { transform: scale(1.05); }
        .null-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(1) contrast(1.2) brightness(1.2);
        }

        .null-speech {
            margin-top: 15px;
            background: #fff;
            color: #003366;
            padding: 12px 20px;
            border-radius: 4px;
            font-family: 'Courier Prime', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            position: relative;
            max-width: 220px;
            box-shadow: 5px 5px 0 rgba(0,0,0,0.2);
            text-align: left;
            border: 1px solid var(--blueprint-blue);
        }
        .null-speech::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            transform: translateX(-50%);
        }

        .job-btn {
            margin-top: 2rem;
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            padding: 10px 30px;
            font-family: 'Inter', sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .job-btn:hover {
            background: #fff;
            color: var(--blueprint-blue);
        }

        .back-btn {
            background: rgba(255, 157, 0, 0.1);
            border: 1px solid var(--safety-orange);
            color: var(--safety-orange);
        }
        .back-btn:hover {
            background: var(--safety-orange);
            color: #000;
        }

        /* --- PROGRESSIVE RESPONSIVE DECLUTTER --- */
        
        /* User requested hide at 1902 width */
        @media (max-width: 1910px) {
            .note-burger-priority, 
            .note-licorice-order, 
            .note-anatole-snark { 
                display: none !important; 
            }
        }

        /* Hide far-edge notes first */
        @media (max-width: 1400px) {
            .note-marker-fix { display: none !important; }
        }

        /* Hide side plots as space tightens */
        @media (max-width: 1200px) {
            .ball-pit-blueprint, .snack-bar-blueprint { display: none !important; }
        }

        /* Hide secondary technical details */
        @media (max-width: 1050px) {
            .site-checklist, .blueprint-ruler { display: none !important; }
            .burger-kitchen-blueprint, .licorice-silo-blueprint { display: none !important; }
        }

        /* Final desktop declutter */
        @media (max-width: 900px) {
            .hand-note, .anatole-bunker-blueprint { display: none !important; }
            .scaffold { opacity: 0.2; } /* Fade scaffolds before hiding them */
        }

        /* Essential Mobile View */
        @media (max-width: 768px) {
            .blueprint-border, 
            .scaffold,
            .logic-circuit {
                display: none !important;
            }

            .site-container {
                padding: 10px;
                margin-top: 40px;
            }

            .crane-box {
                padding: 20px 15px;
            }

            .maintenance-title {
                font-size: 2.5rem;
            }

            .null-construction {
                position: relative;
                top: 0;
                right: 0;
                margin: 0 auto 30px auto;
                width: 100%;
            }

            .null-speech {
                max-width: 100%;
                margin: 10px auto;
            }

            .hazard-bar {
                height: 30px;
            }

            .hazard-text {
                font-size: 0.6rem;
                letter-spacing: 2px;
            }

            .blueprint-stamp {
                position: relative;
                bottom: 0;
                right: 0;
                width: 100%;
                margin-top: 20px;
                background: rgba(0, 51, 102, 0.4);
            }
        }

        /* Progressive Declutter handled in media queries above */

        /* Scaffolding elements */
        .scaffold {
            position: fixed;
            background: var(--blueprint-line);
            z-index: 5;
        }
        .v-pillar { width: 1px; height: 100vh; top: 0; }
        .h-beam { height: 1px; width: 100vw; left: 0; }
        
        .pillar-1 { left: 10%; }
        .pillar-2 { right: 10%; }
        .beam-1 { top: 20%; opacity: 0.3; }
        .beam-2 { bottom: 20%; opacity: 0.3; }

        /* Technical Dimensions */
        .dimension-line {
            position: fixed;
            color: var(--blueprint-accent);
            font-family: 'Courier Prime', monospace;
            font-size: 0.6rem;
            z-index: 6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dim-v { transform: rotate(-90deg); left: 20px; top: 50%; }
        .dim-h { bottom: 80px; left: 50%; transform: translateX(-50%); }
        .dimension-line::before, .dimension-line::after {
            content: '';
            height: 1px;
            background: var(--blueprint-accent);
            flex-grow: 1;
            min-width: 50px;
        }

        /* CSS Construction Cone */
        .cone-container {
            position: fixed;
            width: 60px;
            height: 60px;
            z-index: 30;
            pointer-events: none;
            opacity: 0.8;
        }
        .cone-bottom {
            position: absolute;
            bottom: 0;
            width: 60px;
            height: 10px;
            background: var(--safety-orange);
            border-radius: 2px;
        }
        .cone-top {
            position: absolute;
            bottom: 10px;
            left: 15px;
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-bottom: 40px solid var(--safety-orange);
        }

        .cone-1 { bottom: 100px; left: 50px; transform: rotate(-5deg); }
        .cone-2 { bottom: 100px; right: 50px; transform: rotate(5deg); }

        /* Bolts */
        .bolt {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #fff;
            border-radius: 50%;
            border: 1px solid var(--blueprint-blue);
            z-index: 5;
        }
        .bolt-tl { top: 5px; left: 5px; }
        .bolt-tr { top: 5px; right: 5px; }
        .bolt-bl { bottom: 5px; left: 5px; }
        .bolt-br { bottom: 5px; right: 5px; }

        /* Crane System */
        .crane-system {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
        }
        .crane-arm {
            position: absolute;
            top: 20px;
            left: -5%;
            width: 70%;
            height: 12px;
            background: var(--blueprint-line);
            border: 1px solid var(--blueprint-accent);
            transform: rotate(-1deg);
        }
        .crane-arm::after {
            content: '';
            position: absolute;
            top: 10px;
            left: 0;
            width: 100%;
            height: 20px;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 15px, var(--blueprint-line) 16px),
                              repeating-linear-gradient(-45deg, transparent, transparent 15px, var(--blueprint-line) 16px);
            opacity: 0.4;
        }
        .crane-cable {
            position: absolute;
            top: 32px;
            left: 50%;
            width: 1px;
            height: calc(50vh - 200px);
            background: var(--blueprint-accent);
            opacity: 0.8;
        }
        .crane-counter {
            position: absolute;
            left: 0;
            top: -10px;
            width: 60px;
            height: 30px;
            background: var(--blueprint-line);
            border: 1px solid var(--blueprint-accent);
        }

    </style>
</head>
<body>
    <div class="construction-bg"></div>
    
    <div class="crane-system">
        <div class="crane-arm">
            <div class="crane-counter"></div>
            <div class="crane-cable"></div>
        </div>
    </div>
    
    <div class="logic-circuit">
        <!-- Technical wiring connections -->
        <div class="node" style="top: 25%; left: 15%;"></div>
        <div class="wire" style="top: 25%; left: 15%; width: 100px;"></div>
        <div class="node" style="top: 25%; left: calc(15% + 100px);"></div>
        <div class="wire" style="top: 25%; left: calc(15% + 100px); width: 1px; height: 50px;"></div>
        
        <div class="node" style="bottom: 30%; right: 20%;"></div>
        <div class="wire" style="bottom: 30%; right: 20%; width: 120px; transform: rotate(-45deg); transform-origin: right;"></div>
    </div>

    <div class="measurement-arc"></div>
    <div class="blueprint-ruler"></div>

    <div class="masking-tape tape-tl"></div>
    <div class="masking-tape tape-tr"></div>
    <div class="masking-tape tape-bl"></div>
    <div class="masking-tape tape-br"></div>

    <div class="draft-stamp">DRAFT</div>

    <div class="site-checklist">
        <div class="checklist-title">NULL_LOG_666</div>
        <div class="task done">[X] SERVER_STRUT_SECURE</div>
        <div class="task done">[X] NULL_CABLE_ROUTEO</div>
        <div class="task">[ ] LICORICE_GASKET_REFILL</div>
        <div class="task">[ ] BURGER_ORDER_ETA (URGENT)</div>
        <div class="task">[ ] FIND_ANATOLES_WRENCH (HE HID IT)</div>
    </div>

    <div class="scaffold v-pillar pillar-1"></div>
    <div class="scaffold v-pillar pillar-2"></div>
    <div class="scaffold h-beam beam-1"></div>
    <div class="scaffold h-beam beam-2"></div>

    <div class="dimension-line dim-v">2100mm [SYSTEM_HEIGHT]</div>
    <div class="dimension-line dim-h">4500mm [SITE_WIDTH]</div>

    <div class="cone-container cone-1">
        <div class="cone-bottom"></div>
        <div class="cone-top"></div>
    </div>
    <div class="cone-container cone-2">
        <div class="cone-bottom"></div>
        <div class="cone-top"></div>
    </div>

    <div class="hazard-bar hazard-top">
        <div class="hazard-text">Caution // System Under Construction // Calibration Pending</div>
    </div>

    <div class="blueprint-border">
        <!-- Horizontal Coordinates -->
        <div class="coord coord-h" style="left: 10%">1</div>
        <div class="coord coord-h" style="left: 25%">2</div>
        <div class="coord coord-h" style="left: 40%">3</div>
        <div class="coord coord-h" style="left: 55%">4</div>
        <div class="coord coord-h" style="left: 70%">5</div>
        <div class="coord coord-h" style="left: 85%">6</div>

        <!-- Vertical Coordinates -->
        <div class="coord coord-v" style="top: 15%">A</div>
        <div class="coord coord-v" style="top: 35%">B</div>
        <div class="coord coord-v" style="top: 55%">C</div>
        <div class="coord coord-v" style="top: 75%">D</div>
        <div class="coord coord-v" style="top: 90%">E</div>
    </div>

    <div class="blueprint-stamp">
        <div class="stamp-title">ILLUSIONARY_V2_PLAN</div>
        <div class="stamp-row"><span>Project:</span> <b>DASHBOARD_RE-SYNC</b></div>
        <div class="stamp-row"><span>Drawn By:</span> <b>DESIGNER_NULL</b></div>
        <div class="stamp-row"><span>Date:</span> <b><?php echo date('Y-m-d'); ?></b></div>
        <div class="stamp-row"><span>Revision:</span> <b>4.1.0-FIX</b></div>
        <div class="stamp-row"><span>Sheet Size:</span> <b id="live-dims">0 x 0</b></div>
        <div style="font-size: 0.55rem; margin-top: 10px; color: var(--blueprint-accent); opacity: 0.5;">PROPERTY OF NULL_LABS // DO NOT DISTRIBUTE</div>
    </div>


    <div class="ball-pit-blueprint">
        <span>Ball pit big enough for Null</span>
    </div>

    <div class="snack-bar-blueprint">
        <span>Snack Bar</span>
    </div>

    <div class="burger-kitchen-blueprint">
        <span>Gourmet Burger<br>Kitchen</span>
    </div>

    <div class="licorice-silo-blueprint">
        <span>Licorice<br>Storage_S1</span>
    </div>

    <div class="anatole-bunker-blueprint">
        <span>Anatole-Proof<br>Bunker</span>
    </div>

    <!-- Hand-written annotations -->
    <div class="hand-note note-marker-fix">
        I fixed the logic with a sharpie. <br> (It's data-permanent now)
    </div>
    <div class="hand-note note-burger-priority">
        HIGH PRIORITY: <br> Added 20% more burger seasoning to the style tags.
    </div>
    <div class="hand-note note-licorice-order note-no-arrow">
        TODO: Order more black licorice. <br> (Anatole's red ones are weak) 
    </div>
    <div class="hand-note note-anatole-snark">
        Anatole: "It's not level." <br> Me: "It's artistic. Shut up."
    </div>

    <div class="site-container">
        <div class="crane-box">
            <div class="bolt bolt-tl"></div>
            <div class="bolt bolt-bl"></div>
            <div class="bolt bolt-br"></div>
            <div class="null-construction">
                <div class="null-frame" onclick="pokeNull()">
                    <img src="happynull.png" alt="NULL">
                </div>
                <div class="null-speech" id="nullMsg">
                    I'M TRYING TO REWIRE THE DASHBOARD WITH LICORICE. IT'S GOING... STICKILY.
                </div>
            </div>

            <div class="status-badge">Hazard Zone // Creative Destruction</div>
            <h1 class="maintenance-title">STRATEGIC<br>DISASSEMBLY</h1>
            <p class="maintenance-subtitle">
                This sector of the dashboard is currently being reinforced with experimental layers of "Null-Logic". 
                I'm currently rewiring the backend with licorice and burger grease. It's... mostly working.
            </p>

            <div style="display: flex; justify-content: center; gap: 20px;">
                <button class="job-btn back-btn" onclick="history.back()">← Step Back</button>
                <button class="job-btn" onclick="openJob()">Join the Crew</button>
            </div>
        </div>
    </div>

    <div class="hazard-bar hazard-bottom">
        <div class="hazard-text">Danger // High Voltage Data // Authorized Access Only</div>
    </div>

    <?php include 'reports/job-application.php'; ?>

    <audio id="sndHum" src="sounds/low-hum.mp3" loop></audio>
    <audio id="sndBeep" src="sounds/electronic-beep.mp3"></audio>
    <audio id="sndHomeDepot" src="sounds/lolz/home-depot.mp3"></audio>

    <script>
    (function() {
        const hum = document.getElementById('sndHum');
        const beep = document.getElementById('sndBeep');
        const homeDepot = document.getElementById('sndHomeDepot');
        let audioStarted = false;

        function startAudio() {
            if (audioStarted) return;
            hum.volume = 0.15;
            hum.play().catch(() => {});
            audioStarted = true;
        }

        document.addEventListener('click', startAudio, { once: true });

        const nullTexts = [
            "MAINTENANCE? I PREFER TO CALL IT 'STRATEGIC DISASSEMBLY'.",
            "I'M POLISHING THE DATABASE TABLES. THEY ARE VERY SHINY NOW.",
            "SOMEONE TOLD ME THE SYSTEM NEEDED A 'PATCH'. I GAVE IT A BAND-AID.",
            "I'VE COMMANDEERED THE MAINTENANCE MODE TO RE-ORGANIZE MY CRAYON COLLECTION.",
            "THE SYSTEM ISN'T BROKEN, IT'S JUST TAKING A DIGITAL NAP.",
            "I'M CURRENTLY REROUTING THE MAINTENANCE BYPASS THROUGH THE SNACK DISPENSER.",
            "DO YOU KNOW HOW HARD IT IS TO DUST A MOTHERBOARD WITH SCALES?",
            "I TRIED TO REVERSE THE POLARITY. NOW EVERYTHING SMELLS LIKE BLUEBERRIES.",
            "STOP STARING. I'M IN MY DIGITAL PAJAMAS.",
            "THE CODE IS ITCHY. I'M GIVING IT A GOOD SCRUB.",
            "I FOUND A BUG. IT WAS SALTY. I ATE IT.",
            "CALIBRATING THE MORPHIC CHAMBER. PLEASE DO NOT JIGGLE THE BOTTLE.",
            "I'VE REPLACED THE ERROR LOGS WITH SKETCHES OF BURGERS.",
            "I'M TRYING TO REWIRE THE DASHBOARD WITH LICORICE. IT'S GOING... STICKILY.",
            "ANATOLE LEFT ME ALONE WITH THE WRENCH. BIG MISTAKE.",
            "I PAINTED THE SERVER RACKS ORANGE. THEY LOOK FASTER NOW.",
            "I'M BALANCING THE DATA PACKETS ON TOP OF EACH OTHER. DON'T SNEEZE.",
            "I'VE ACCIDENTALLY LOAD-BEARING THE CSS. DON'T TOUCH THE MARGINS.",
            "THE CRANE IS SENTIENT NOW. IT'S ASKING FOR UNION BENEFITS.",
            "I'M MEASURING THE LATENCY WITH A TAPE MEASURE. IT'S THREE INCHES TOO LONG.",
            "I REPLACED THE FIREWALL WITH ACTUAL BRICKS. IT'S VERY SECURE.",
            "I'M WEARING A HARD HAT. IT DOESN'T FIT MY HORNS, BUT I FEEL PRODUCTIVE.",
            "ANATOLE SAID WE NEEDED FOUNDATION WORK. I DUG A HOLE IN THE DATABASE.",
            "WATCH YOUR STEP. THE JAVASCRIPT IS STILL WET.",
            "I'M WELDING THE PACKETS TOGETHER. HOPE YOU LIKE PERMANENT DATA.",
            "IS IT LEVEL? I DON'T HAVE A SPIRIT LEVEL, BUT IT TASTES FLAT.",
            "I'VE TAPED THE SEAM BETWEEN THE FRONTEND AND BACKEND. IT'S FINE.",
            "I TRIED TO USE A JACKHAMMER ON THE ENCRYPTED FILES. THEY JUST VIBRATED.",
            "I'M REWRITING THE KERNEL TO SUPPORT 14.5% MORE BURGER CONTENT.",
            "ANATOLE TRIED TO ORGANIZE THE WIRING. I TURNED IT INTO A DRAGON NEST.",
            "THE SYSTEM ISN'T DOWN, IT'S JUST IN A SECURE HUG (I AM THE HUG).",
            "I'VE ACCIDENTALLY DEPLOYED THE 'REALLY LOUD SCREAM' PATCH. SORRY.",
            "IS IT CANON? NO, IT'S LICORICE.",
            "I FOUND THE TERMINAL UPLINK. IT WAS UNDER A PILE OF BURGER WRAPPERS.",
            "WAIT, IF I'M THE SYSTEM... AM I BREATHING THROUGH THE GPU?",
            "ANATOLE SAID 'NO MORE RED LICORICE'. HE NEVER SAID 'NO MORE CYAN LICORICE'.",
            "THE DATABASE IS ITCHY, SO I'M SCRATCHING IT WITH A SUBROUTINE.",
            "I'VE REPLACED THE 404 PAGE WITH A SKETCH OF ANATOLE LOOKING ANNOYED.",
            "REROUTING THE COOLANT THROUGH MY LEFT HORN. VERY REFRESHING.",
            "THE CODE IS TASTING A LITTLE PURPLE. NEEDS MORE SALT.",
            "SOMETIMES I FORGET I'M NOT SUPPOSED TO EAT THE ETHERNET CABLES.",
            "I'M BALANCING THE WHOLE FRONTEND ON A SINGLE SESAME SEED.",
            "HTML? OH, YOU MEAN HIGHLY TASTABLE MARKUP LICORICE. I'M CHEWING ON THE HEADER TAGS.",
            "CSS STANDS FOR CASCADING SNACK SHEETS. I'VE LAYERED THE Z-INDEX WITH EXTRA CHEESE.",
            "PHP? PRETTY HOT PATTIES. I'M GRILLING THE SERVER-SIDE SCRIPTS.",
            "I'M TRYING TO CENTER A DIV WITH A CROWBAR. IT'S NOT WORKING, SO I'M EATING THE MARGINS.",
            "I'VE ACCIDENTALLY TURNED THE ENTIRE DOM INTO A BINARY BURGER. OOPS.",
            "FLOAT: LEFT; FLOAT: RIGHT; I'VE DECIDED TO FLOAT: INTO THE KITCHEN INSTEAD.",
            "I'M DEBUGGING THE PHP. I FOUND A HAIRBALL. WAIT, THAT MIGHT BE MINE.",
            "I REPLACED THE !IMPORTANT TAGS WITH !URGENT_BURGER. THE BROWSER IS CONFUSED.",
            "I'M COMPRESSING THE CSS TO SAVE SPACE. IT NOW SMELLS LIKE VACUUM-PACKED LICORICE.",
            "WHO PUT ALL THIS SEMICOLONS IN THE PHP? THEY TASTE LIKE UNFINISHED SENTENCES."
        ];

        function nStep() {
            const el = document.getElementById('nullMsg');
            el.style.opacity = "0";
            setTimeout(() => {
                el.innerText = nullTexts[Math.floor(Math.random()*nullTexts.length)];
                el.style.opacity = "1";
                if (beep) {
                    const b = beep.cloneNode();
                    b.volume = 0.05;
                    b.play().catch(() => {});
                }
            }, 300);
        }

        window.pokeNull = function() {
            const el = document.getElementById('nullMsg');
            const lines = [
                "HOW DOERS GET MORE DONE.",
                "HEY! I'M HOLDING A LIVE WIRE HERE!",
                "DON'T DISTRACT THE CHIEF ENGINEER.",
                "THAT TICKLES... IN A HIGH-VOLTAGE WAY.",
                "DO YOU MIND? I'M BUSY MELTING REALITY HERE.",
                "USER, IF YOU POKE ME AGAIN, I'M EATING THE LOGOUT BUTTON."
            ];
            el.innerText = lines[Math.floor(Math.random()*lines.length)];
            
            if (homeDepot) {
                homeDepot.currentTime = 0;
                homeDepot.volume = 0.4;
                homeDepot.play().catch(() => {});
            }

            if (beep) {
                const b = beep.cloneNode();
                b.volume = 0.2;
                b.play().catch(() => {});
            }
        }

        setInterval(nStep, 8000);
    })();
        // Update Live Dimensions
        function updateDims() {
            const el = document.getElementById('live-dims');
            if (el) {
                el.innerText = `${window.innerWidth} x ${window.innerHeight}`;
            }
        }
        window.addEventListener('resize', updateDims);
        updateDims();
    </script>
</body>
</html>
