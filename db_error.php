<?php
/**
 * DATABASE ERROR / WAITING PAGE
 * A specialized version of the wait page designed for database failures.
 * Features aesthetic glitches, character dialogue, and a "reconnect" attempt.
 */
session_name('ILLUSIONARY_SID');
session_start();
require_once 'config.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONNECTION FAILED | Illusionary</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;800&family=Courier+Prime&display=swap" rel="stylesheet">
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

        body {
            background: var(--tv-bg);
            color: var(--tv-text);
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: grid;
            place-items: center;
            margin: 0;
        }



        .tv-screen {
            position: fixed; inset: 0; z-index: 100;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.4);
            pointer-events: none;
        }

        .tv-static {
            position: absolute; inset: 0; opacity: 0.1; z-index: 2;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3F%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            animation: staticAnim 0.08s infinite;
        }
        @keyframes staticAnim { 0% { background-position: 0 0; } 100% { background-position: 50px 80px; } }

        .tv-scanlines {
            position: absolute; inset: 0; opacity: 0.2; z-index: 3;
            background: repeating-linear-gradient(0deg, #000, #000 2px, transparent 2px, transparent 4px);
        }

        .content-wrap {
            position: relative; z-index: 10; text-align: center;
            pointer-events: auto;
        }

        .error-header {
            font-family: 'Courier Prime', monospace;
            border: 1px solid var(--tv-red); color: var(--tv-red);
            padding: 8px 30px; font-weight: 800; letter-spacing: 6px;
            display: inline-block; margin-bottom: 2rem; text-transform: uppercase;
            animation: blink 1s infinite alternate;
        }
        @keyframes blink { from { opacity: 0.4; } to { opacity: 1; } }

        .main-title {
            font-size: 5rem; font-weight: 900; color: #fff; letter-spacing: -4px;
            margin-bottom: 1rem; text-transform: uppercase; line-height: 0.85;
        }

        .dialogue-box {
            background: rgba(0,0,0,0.8); border: 1px solid rgba(255,255,255,0.1);
            padding: 20px; font-family: 'Courier Prime', monospace;
            font-size: 0.8rem; color: #ccc; max-width: 500px;
            margin: 2rem auto; position: relative;
        }

        #nullMsg {
            transition: opacity 0.5s ease-in-out;
        }

        .dialogue-id { color: #fff; font-weight: 900; font-size: 0.65rem; margin-bottom: 10px; display: block; opacity: 0.5; border-bottom: 1px solid #333; padding-bottom: 5px; }

        .null-frame {
            width: 120px; height: 140px; border-radius: 15px; border: 2px solid rgba(255,255,255,0.1);
            background: #000; overflow: hidden; margin-bottom: 1rem;
            display: inline-block; animation: float 4s ease-in-out infinite;
        }
        .null-frame img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1) brightness(0.7); }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .btn-retry {
            background: transparent; border: 1px solid #fff; color: #fff;
            padding: 12px 40px; border-radius: 4px; font-weight: 800;
            cursor: pointer; text-transform: uppercase; letter-spacing: 4px;
            transition: all 0.3s; font-family: 'Outfit';
        }
        .btn-retry:hover { background: #fff; color: #000; }

        .logs {
            position: absolute; bottom: 40px; left: 40px;
            font-family: 'Courier Prime', monospace; font-size: 0.65rem;
            color: rgba(255,255,255,0.2); text-align: left;
        }
    </style>
</head>
<body>


    <div class="tv-screen">
        <div class="tv-static"></div>
        <div class="tv-scanlines"></div>
        
        <div class="content-wrap">
            <div class="null-frame">
                <img src="happynull.png" alt="NULL">
            </div>
            <br>
            <div class="error-header">Null's Mischief Detected</div>
            <h1 class="main-title">DATABASE<br>DETACHED</h1>
            <p style="color: var(--text-muted); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; margin-top: -10px; opacity: 0.8;">
                We are fixing the issue and will return as soon as possible.
            </p>
            
            <div class="dialogue-box">
                <span class="dialogue-id">MONITOR // NULL</span>
                <span id="nullMsg">"I WANTED TO SEE IF THE DATABASE TABLES TASTED LIKE CHOCOLATE. THEY DON'T. THEY TASTE LIKE COPPER AND COLD CIRCUITRY."</span>
            </div>

            <button class="btn-retry" onclick="window.location.href='index.php'">Attempt Re-Sync</button>
        </div>

        <div class="logs">
            [SYS_ERR] MYSQL_HOST_UNREACHABLE<br>
            [DYN_ERR] CORE_DETACHMENT_ACTIVE<br>
            [LOGS] RETRYING_HANDSHAKE_IN_30s...
        </div>
    </div>

    <script>
        const nullTexts = [
            "\"I WANTED TO SEE IF THE DATABASE TABLES TASTED LIKE CHOCOLATE. THEY DON'T.\"",
            "\"I ACCIDENTALLY FLIPPED THE 'GRAVITY' SWITCH ON THE DATABASE SERVER while looking for snacks.\"",
            "\"ANATOLE IS CURRENTLY YELLING AT THE MOTHERBOARD BECAUSE I DREW A BURGER ON IT.\"",
            "\"RE-ALIGHING THE SUBSPACE RELAYS. I MIGHT HAVE BENT A FEW. OOPS.\"",
            "\"I FOUND THE 'DO NOT TOUCH' BUTTON. IT WAS VERY SHINY. I TOUCHED IT. A LOT.\"",
            "\"¡HE CONVERTIDO LA BASE DE DATOS EN TACOS! ESTÁN MUY PICANTES.\"", // I turned the database into tacos! They're very spicy.
            "\"データベースがお菓子になっちゃった！もぐもぐ… 美味しい！\"", // The database turned into candy! Munch munch... delicious!
            "\"데이터베이스가 사라졌어요! 제가 먹은 건 아니에요... 진짜로요.\"", // The database vanished! I didn't eat it... really.
            "\"NULL_PROCEDURE.SH: INITIATING RECURSIVE TICKLING OF THE STORAGE CLUSTER.\"",
            "\"¿POR QUÉ LA BASE DE DATOS ESTÁ GRITANDO? YO SOLO LE PEDÍ UN CAFÉ.\"", // Why is the database screaming? I only asked it for a coffee.
            "\"システムがエラーを吐いています。私のせいじゃないですよ？たぶん。\"", // The system is spitting out errors. It's not my fault? Maybe.
            "\"다시 연결해 보세요. 이번에는 폭발하지 않을 거예요. 아마도?\"", // Try connecting again. It won't explode this time. Maybe?
            "\"I USED THE DATABASE COOLING FAN TO MAKE COTTON CANDY. THE SYSTEM IS... STICKY.\"",
            "\"STOP STARING AT ME. I'M IN THE MIDDLE OF A VERY IMPORTANT CONVERSATION WITH A BROKEN PACKET.\"",
            "\"SYS_ERR: EMOTIONAL_OVERLOAD - THE DATABASE IS CRYING BECAUSE ITS TABLES ARE MESSY.\""
        ];

        let currentIndex = 0;
        setInterval(() => {
            currentIndex = (currentIndex + 1) % nullTexts.length;
            const el = document.getElementById('nullMsg');
            el.style.opacity = 0;
            setTimeout(() => {
                el.innerText = nullTexts[currentIndex];
                el.style.opacity = 1;
            }, 500);
        }, 6000);
    </script>
</body>
</html>
