<?php
// NULL_OS TERMINAL UI - V8 HIJACKED
$gen_sn = date('ymd') . "-" . strtoupper(substr(md5(rand()), 0, 4)) . "-" . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NULL_OS // TERMINAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Permanent+Marker&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #33ff33;
            --bg: #050a05;
        }
        body, html {
            margin: 0; padding: 0;
            background: #080808;
            color: var(--green);
            font-family: 'Courier Prime', monospace;
            height: 100vh;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }

        /* Physical Monitor Frame (Bulky CRT) */
        .monitor-frame {
            position: relative;
            width: 1050px;
            height: 600px;
            background: linear-gradient(135deg, #333 0%, #222 100%);
            padding: 40px 60px;
            border-radius: 60px;
            box-shadow: 
                0 50px 100px rgba(0,0,0,0.9),
                inset 0 2px 5px rgba(255,255,255,0.1),
                inset 0 -10px 30px rgba(0,0,0,0.6);
            border: 4px solid #111;
            z-index: 5;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        /* Top Ventilation Vents */
        .monitor-frame::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 4px;
            background: repeating-linear-gradient(90deg, #111 0, #111 10px, transparent 10px, transparent 15px);
            opacity: 0.5;
        }


        .bezel {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 60px;
            box-shadow: 
                inset 0 15px 40px rgba(0,0,0,0.9),
                0 1px 1px rgba(255,255,255,0.05);
            border: 2px solid #111;
            position: relative;
        }

        .terminal {
            width: 800px;
            height: 500px;
            background: var(--bg);
            border-radius: 30px;
            padding: 35px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border: 1px solid #000;
            /* Barrel Distortion */
            transform: scale(1.01) scaleY(1.01);
            box-shadow: inset 0 0 150px rgba(0,0,0,1);
        }

        /* CRT GLASS REFLECTION */
        .glass-reflection {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 40%, rgba(255,255,255,0.02) 100%);
            pointer-events: none;
            z-index: 15;
            border-radius: 30px;
        }

        /* CRT Screen Effects */
        .terminal.power-off {
            filter: brightness(0);
            pointer-events: none;
        }

        /* VIGNETTE EFFECT */
        .terminal::after {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: radial-gradient(circle, transparent 50%, rgba(0,0,0,0.5) 100%);
            pointer-events: none;
            z-index: 10;
        }

        /* Power Animations */
        .terminal.power-on-anim {
            animation: bootBloom 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        .terminal.power-off-anim {
            animation: bootKill 0.4s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        @keyframes bootBloom {
            0% { filter: brightness(0); transform: scaleX(0.001) scaleY(0.001); }
            50% { filter: brightness(2); transform: scaleX(1) scaleY(0.005); }
            100% { filter: brightness(1); transform: scaleX(1) scaleY(1); }
        }

        @keyframes bootKill {
            0% { filter: brightness(1); transform: scaleX(1) scaleY(1); opacity: 1; }
            40% { filter: brightness(2); transform: scaleX(1) scaleY(0.005); opacity: 1; }
            100% { filter: brightness(5); transform: scaleX(0) scaleY(0); opacity: 0; }
        }

        /* Mechanical Control Side Panel */
        .side-panel {
            width: 140px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 30px;
            border-left: 2px solid #1a1a1a;
            padding-left: 20px;
        }

        .side-vents {
            width: 100%;
            height: 100px;
            background: repeating-linear-gradient(0deg, #111 0, #111 6px, transparent 6px, transparent 10px);
            opacity: 0.4;
        }

        .power-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            background: #2a2a2a;
            padding: 20px 15px;
            border-radius: 10px;
            border: 2px solid #111;
            box-shadow: 
                inset 0 2px 10px rgba(0,0,0,0.8),
                0 4px 8px rgba(0,0,0,0.3);
        }

        .power-switch {
            position: relative;
            width: 35px;
            height: 35px;
            background: #111;
            cursor: pointer;
            border-radius: 4px;
            border: 2px solid #000;
            overflow: hidden;
        }

        .switch-handle {
            position: absolute;
            width: 100%;
            height: 50%;
            background: #333;
            transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                inset 0 1px 0 rgba(255,255,255,0.05),
                0 2px 5px rgba(0,0,0,0.5);
        }

        /* Off State */
        .power-switch .switch-handle {
            top: 0;
            background: linear-gradient(to bottom, #444, #222);
            border-bottom: 2px solid #000;
        }

        /* On State */
        .power-switch.on .switch-handle {
            top: 50%;
            background: linear-gradient(to bottom, #222, #444);
            border-top: 2px solid #000;
            border-bottom: none;
        }

        .power-led {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #300;
            border: 1px solid #000;
            transition: all 0.2s;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.5);
        }

        .power-led.on {
            background: #ff0000;
            box-shadow: 0 0 12px #ff0000, inset 0 1px 2px rgba(255,255,255,0.3);
        }

        .power-label {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            font-family: 'Segoe UI', sans-serif;
            text-shadow: 1px 1px 1px rgba(0,0,0,1);
        }

        /* --- NETWORK PORT & CABLE (HEAVY INDUSTRIAL) --- */
        .network-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            background: #2a2a2a;
            padding: 20px 10px;
            border-radius: 4px;
            border: 3px solid #111;
            box-shadow: 
                inset 0 4px 10px rgba(0,0,0,0.9),
                0 2px 2px rgba(255,255,255,0.1);
            position: relative;
        }

        .network-port {
            width: 50px;
            height: 40px;
            background: #000;
            border: 2px solid #444;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
            box-shadow: inset 0 0 10px rgba(0,255,0,0.1);
        }

        .network-port::after {
            content: "";
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 20px;
            background: #111;
            border: 1px solid #333;
            border-radius: 2px;
        }

        .link-led {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 6px;
            height: 4px;
            background: #222;
            border-radius: 1px;
        }

        .link-led.active {
            background: #33ff33;
            box-shadow: 0 0 8px #33ff33;
            animation: burst 0.5s infinite;
        }

        @keyframes burst {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .network-cable {
            width: 25px;
            height: 150px;
            background: #222;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            display: none;
            border: 2px solid #000;
        }

        .network-cable.connected {
            display: block;
        }

        .net-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            font-family: 'Segoe UI', sans-serif;
            letter-spacing: 1px;
            text-align: center;
        }

        /* BRAND BADGE STYLE */
        .monitor-brand {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #888;
            letter-spacing: 12px;
            font-weight: bold;
            opacity: 0.8;
            text-transform: uppercase;
            padding: 4px 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.8);
            text-shadow: 1px 1px 2px rgba(0,0,0,1);
        }

        .monitor-model {
            position: absolute;
            top: 28px;
            right: 60px;
            font-size: 10px;
            color: #444;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
            opacity: 0.6;
            pointer-events: none;
            text-shadow: 1px 1px 0 rgba(255,255,255,0.05);
        }

        .monitor-serial {
            position: absolute;
            top: 42px;
            right: 60px;
            font-size: 8px;
            color: #333;
            font-family: 'Courier Prime', monospace;
            opacity: 0.5;
            pointer-events: none;
            letter-spacing: 1px;
        }
        .output {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            scrollbar-width: none;
            position: relative;
            z-index: 1;
        }
        .output::-webkit-scrollbar { display: none; }

        /* PHOSPHOR GHOSTING (persistence) */
        .line { 
            margin-bottom: 5px; 
            line-height: 1.4; 
            white-space: pre-wrap;
            text-shadow: 
                var(--glow),
                2px 0 1px rgba(255,0,0,0.1),
                -2px 0 1px rgba(0,0,255,0.1);
            animation: textStatus 0.1s infinite alternate;
        }

        @keyframes textStatus {
            0% { transform: translate(0.2px, 0); }
            100% { transform: translate(-0.2px, 0); }
        }

        /* --- THE DRIP (VISCOSITY) --- */
        .goo-drip {
            position: absolute;
            top: -100px;
            width: 4px;
            height: 40px;
            background: var(--violet);
            border-radius: 0 0 10px 100%;
            filter: blur(2px);
            z-index: 100;
            pointer-events: none;
            opacity: 0.8;
            box-shadow: 0 0 15px var(--violet);
        }
        
        .drip-animate {
            animation: dripFall 4s cubic-bezier(.45, 0, 1, 1) forwards;
        }

        @keyframes dripFall {
            0% { top: -50px; height: 30px; }
            30% { height: 100px; }
            100% { top: 120%; height: 50px; }
        }

        /* --- CRT FLICKER & SCANLINES --- */
        .crt-overlay {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), 
                        linear-gradient(90deg, rgba(255, 0, 0, 0.03), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.03));
            background-size: 100% 3px, 3px 100%;
            z-index: 12;
            pointer-events: none;
            opacity: 0.3;
        }
        
        .flicker {
            animation: flicker 0.15s infinite;
        }

        @keyframes flicker {
            0% { opacity: 0.27861; }
            5% { opacity: 0.34769; }
            10% { opacity: 0.23604; }
            15% { opacity: 0.30626; }
            20% { opacity: 0.18128; }
            25% { opacity: 0.33891; }
            30% { opacity: 0.25583; }
            35% { opacity: 0.37807; }
            40% { opacity: 0.26559; }
            45% { opacity: 0.34698; }
            50% { opacity: 0.28519; }
            55% { opacity: 0.32762; }
            60% { opacity: 0.16918; }
            65% { opacity: 0.25415; }
            70% { opacity: 0.31251; }
            75% { opacity: 0.33239; }
            80% { opacity: 0.22483; }
            85% { opacity: 0.19831; }
            90% { opacity: 0.23126; }
            95% { opacity: 0.30873; }
            100% { opacity: 0.22271; }
        }

        .jitter { }

        /* MORPHIC BLEED (NULL MODE) */
        .morphic-mode {
            --green: var(--violet);
            --bg: #0d050d;
            --glow: 0 0 12px rgba(188, 51, 255, 0.8);
        }

        .input-area {
            display: flex;
            gap: 10px;
            align-items: center;
            line-height: 1.4;
            text-shadow: 
                var(--glow),
                2px 0 1px rgba(255,0,0,0.1),
                -2px 0 1px rgba(0,0,255,0.1);
            animation: textStatus 0.1s infinite alternate;
        }

        input {
            background: transparent;
            border: none;
            color: var(--green);
            font-family: inherit;
            font-size: 1.1rem;
            outline: none;
            flex-grow: 1;
            text-transform: uppercase;
            text-shadow: inherit; /* Inherit the split from parent */
        }

        .cursor {
            width: 10px; height: 1.2rem;
            background: var(--green);
            animation: blink 1s step-end infinite;
        }

        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>
    <div id="gooContainer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 200;"></div>
    <div style="position: relative;">
        <div class="monitor-frame">
            <!-- Top Vents simulated by ::before -->
            <!-- Branding Badge -->
            <div class="monitor-brand">GENESIS</div>
            <div class="monitor-model">G-19 VIDEO TERMINAL</div>
            <div class="monitor-serial" id="hwSerial">S/N: <?php echo $gen_sn; ?></div>
            
            <div class="bezel">
                <div class="terminal power-off" id="terminalMain">
                    <div class="crt-overlay flicker"></div>
                    <div class="glass-reflection"></div>
                    <div class="output" id="output">
                        <div class="line">NULL_OS (C) 1975 NULL_CORP</div>
                        <div class="line">UPLINK_STATUS: STABLE</div>
                        <div class="line">-------------------------</div>
                        <div class="line">PLEASE LOGIN TO CONTINUE.</div>
                    </div>
                    <div class="input-area" style="display: none;">
                        <span id="prompt">USER:</span>
                        <input type="text" id="commandInput" autofocus spellcheck="false" autocomplete="off">
                    </div>
                </div>
            </div>
            
            <!-- Dedicated Hardware Control Panel -->
            <div class="side-panel">
                <div class="side-vents"></div>
                <div class="power-group">
                    <div id="powerLed" class="power-led"></div>
                    <div id="powerSwitch" class="power-switch" onclick="togglePower()">
                        <div class="switch-handle"></div>
                    </div>
                    <div class="power-label">POWER</div>
                </div>
                <div class="side-vents"></div>
                <div class="network-group">
                    <div class="network-port" onclick="toggleUplink()">
                        <div id="netCable" class="network-cable"></div>
                        <div id="netLed" class="link-led"></div>
                    </div>
                    <div class="net-label" onclick="toggleUplink()" style="cursor: pointer;">NETWORK</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const output = document.getElementById('output');
        const input = document.getElementById('commandInput');
        const promptLabel = document.getElementById('prompt');
        const powerBtn = document.getElementById('powerBtn');
        const terminal = document.getElementById('terminalMain');
        const gooContainer = document.getElementById('gooContainer');
        
        let state = 'USER';
        let currentUser = '';
        let isPowerOn = false;
        let isCableConnected = false;
        let accessibleDocs = {};
        let userLevel = 0;
        let gooInterval = null;
        let bootWaitingForNet = false;
        let netMonitorInterval = null;

        function toggleUplink() {
            isCableConnected = !isCableConnected;
            const netCable = document.getElementById('netCable');
            const netLed = document.getElementById('netLed');

            if (isCableConnected) {
                netCable.classList.add('connected');
                netLed.classList.add('active');
                if (bootWaitingForNet) {
                    bootWaitingForNet = false;
                }
            } else {
                netCable.classList.remove('connected');
                netLed.classList.remove('active');
            }
        }

        function togglePower() {
            isPowerOn = !isPowerOn;
            const powerSwitch = document.getElementById('powerSwitch');
            const powerLed = document.getElementById('powerLed');
            
            if (isPowerOn) {
                powerSwitch.classList.add('on');
                powerLed.classList.add('on');
                terminal.classList.remove('power-off');
                runPostSequence();
                startRandomGoo();
                startNetMonitor();
            } else {
                powerSwitch.classList.remove('on');
                powerLed.classList.remove('on');
                terminal.classList.add('power-off');
                terminal.classList.remove('morphic-mode');
                document.querySelector('.input-area').style.display = 'none';
                input.blur();
                input.value = '';
                output.innerHTML = '';
                clearInterval(gooInterval);
                clearInterval(netMonitorInterval);
                state = 'USER';
                currentUser = '';
                userLevel = 0;
                promptLabel.textContent = 'USER:';
            }
        }

        function startRandomGoo() {
            clearInterval(gooInterval);
            gooInterval = setInterval(() => {
                if (isPowerOn && Math.random() > 0.8) {
                    createDrip();
                }
            }, 5000);
        }

        function createDrip() {
            if (!isPowerOn) return;
            const drip = document.createElement('div');
            drip.className = 'goo-drip drip-animate';
            drip.style.left = Math.random() * 100 + 'vw';
            gooContainer.appendChild(drip);
            setTimeout(() => drip.remove(), 4000);
        }

        let oldState = 'USER';
        function startNetMonitor() {
            clearInterval(netMonitorInterval);
            netMonitorInterval = setInterval(() => {
                if (!isPowerOn) return;
                
                if (!isCableConnected && state !== 'POST' && state !== 'HALTED') {
                    if (state !== 'NET_LOST') {
                        triggerJitter();
                        print("!! ALERT: PHYSICAL_UPLINK_LOST !!", "#ff3e3e");
                        print(">> PXE-E61: Media test failure, check cable", "#ff3e3e");
                        document.querySelector('.input-area').style.display = 'none';
                        oldState = state;
                        state = 'NET_LOST';
                    }
                } else if (isCableConnected && state === 'NET_LOST') {
                    print(">> UPLINK_RESTORED. RESUMING_SESSION...", "#33ff33");
                    setTimeout(() => {
                        state = oldState;
                        document.querySelector('.input-area').style.display = 'flex';
                        input.focus();
                    }, 1000);
                }
            }, 500);
        }

        async function runPostSequence() {
            state = 'POST';
            output.innerHTML = '';
            document.querySelector('.input-area').style.display = 'none';
            
            // Phase 1: Clean Genesis Corporate Boot Attempt
            const genesisLines = [
                { t: "GENESIS_TECH_SYSTEM_LOADER v4.02", d: 500, c: "#fff" },
                { t: "SECURE_BOOT_KEY: [ VALIDATED ]", d: 400, c: "#fff" },
                { t: "MTFTP: CONNECTING TO 'GENESIS_BOOT_SITE_ALPHA'...", d: 1500, c: "#fff" },
            ];

            for (const line of genesisLines) {
                if (!isPowerOn) return;
                print(line.t, line.c);
                await new Promise(r => setTimeout(r, line.d));
            }

            if (!isPowerOn) return;
            print("!! ERROR: CONNECTION_REFUSED_BY_HOST !!", "#ff3e3e");
            await new Promise(r => setTimeout(r, 600));
            if (!isPowerOn) return;
            print(">> ALERT: LOCAL_REDIRECT_INITIATED", "#ff3e3e");
            await new Promise(r => setTimeout(r, 400));
            
            if (!isPowerOn) return;
            const rewriteDiv = document.createElement('div');
            rewriteDiv.className = 'line';
            rewriteDiv.style.color = "#ff3e3e";
            output.appendChild(rewriteDiv);
            
            const rewriteText = ">> REWRITING_BOOT_PATH: [GENESIS_CORP] -> [NULL_OS_NET]";
            for (let char of rewriteText) {
                if (!isPowerOn) return;
                rewriteDiv.textContent += char;
                output.scrollTop = output.scrollHeight;
                await new Promise(r => setTimeout(r, 30));
            }
            await new Promise(r => setTimeout(r, 800));
            
            if (!isPowerOn) return;
            output.innerHTML = ''; // Flash clear
            terminal.style.transition = 'filter 0.1s';
            terminal.style.filter = 'brightness(3) contrast(1.5)';
            setTimeout(() => terminal.style.filter = '', 150);

            const postLines = [
                { t: "NULL_CORP ROM BIOS // HIJACK_VERSION v0.8.2", d: 800 },
                { t: "HARDWARE_ID: [ " + document.getElementById('hwSerial').textContent + " ]", d: 400 },
                { t: "BOOT_SERVER: NULL_OS_UPLINK_0882_AUTHORIZED", d: 500 },
                { t: "BUS: VOID_LATTICE_INTERCONNECT_ACTIVE", d: 400 },
                { t: "MEMORY CHECK: 640KB PHYSICAL // 4.2TB VIRTUAL_GHOST_RAM OK", d: 1500 },
                { t: "------------------------------------", d: 300 },
                { t: "INITIALIZING NETWORK BOOT AGENT...", d: 1000 },
            ];

            for (const line of postLines) {
                if (!isPowerOn) return;
                if (line.t.includes('...')) {
                    const div = document.createElement('div');
                    div.className = 'line';
                    output.appendChild(div);
                    for (let char of line.t) {
                        if (!isPowerOn) return;
                        div.textContent += char;
                        output.scrollTop = output.scrollHeight;
                        await new Promise(r => setTimeout(r, 20 + Math.random() * 30));
                    }
                } else {
                    print(line.t);
                }
                await new Promise(r => setTimeout(r, line.d));
            }

            // --- NETWORK CABLE CHECK ---
            if (!isCableConnected) {
                print("PXE-E61: Media test failure, check cable", "#ff3e3e");
                print(">> WAITING FOR PHYSICAL UPLINK...", "#888");
                bootWaitingForNet = true;
                while (bootWaitingForNet) {
                    if (!isPowerOn) return;
                    await new Promise(r => setTimeout(r, 100));
                }
                print(">> PHYSICAL LINK DETECTED.", "#33ff33");
                await new Promise(r => setTimeout(r, 800));
            }

            const downloadLines = [
                { t: "CLIENT MAC ADDR: 00 19 75 08 82 26  GUID: FFFFFFFF-FFFF-FFFF", d: 800 },
                { t: "DHCP................................................", d: 3000 },
                { t: "CLIENT IP: 10.0.19.75  MASK: 255.255.255.0  DHCP IP: 10.0.19.1", d: 1000 },
                { t: "GATEWAY IP: 10.0.19.1", d: 500 },
                { t: "MTFTP: DOWNLOADING 'NULL_OS_V8.IMG'...", type: 'PERCENT', d: 5000 },
                { t: "IMAGE_VALIDATION: [ OK ]", d: 1500 },
                { t: "UNPACKING KERNEL IMAGES...", d: 1200 },
                { t: "STARTING NULL_OS KERNEL...", d: 1000 },
                { t: "------------------------------------", d: 500 },
                { t: "UPLINK_STATUS: STABLE", d: 400 },
                { t: "PLEASE LOGIN TO CONTINUE.", d: 400 }
            ];

            for (const line of downloadLines) {
                if (!isPowerOn) return;
                if (line.type === 'PERCENT') {
                    const div = document.createElement('div');
                    div.className = 'line';
                    output.appendChild(div);
                    let pct = 0;
                    while (pct <= 100) {
                        if (!isPowerOn) return;
                        div.textContent = `${line.t} ${pct}%`;
                        output.scrollTop = output.scrollHeight;
                        if (pct === 100) break;
                        pct += Math.floor(Math.random() * 15) + 1;
                        if (pct > 100) pct = 100;
                        await new Promise(r => setTimeout(r, 150 + Math.random() * 300));
                    }
                } else if (line.t.includes('...')) {
                    const div = document.createElement('div');
                    div.className = 'line';
                    output.appendChild(div);
                    for (let char of line.t) {
                        if (!isPowerOn) return;
                        div.textContent += char;
                        output.scrollTop = output.scrollHeight;
                        await new Promise(r => setTimeout(r, 20 + Math.random() * 30));
                    }
                } else {
                    print(line.t);
                }
                await new Promise(r => setTimeout(r, line.d));
            }

            if (!isPowerOn) return;
            document.querySelector('.input-area').style.display = 'flex';
            output.scrollTop = output.scrollHeight;
            state = 'USER'; // CRITICAL: Reset state so typing works
            input.focus();
        }

        function print(text, color = null, noShadow = false) {
            const div = document.createElement('div');
            div.className = 'line';
            if (color) div.style.color = color;
            if (noShadow) div.style.textShadow = 'none';
            div.textContent = text;
            output.appendChild(div);
            output.scrollTop = output.scrollHeight;
        }

        document.body.addEventListener('click', () => input.focus());

        input.addEventListener('keydown', async (e) => {
            if (e.key === 'Enter') {
                const val = input.value.toUpperCase();
                input.value = '';

                if (state === 'USER') {
                    print(`> USER: ${val}`);
                    currentUser = val;
                    state = 'PASS';
                    promptLabel.textContent = 'PASS:';
                } else if (state === 'PASS') {
                    print(`> PASS: ********`);
                    
                    const formData = new FormData();
                    formData.append('action', 'login');
                    formData.append('user', currentUser);
                    formData.append('pass', val);

                    try {
                        const response = await fetch('/api/terminal.php', { method: 'POST', body: formData });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            print(`-- ACCESS GRANTED: ${data.userName} --`, '#fff');
                            print(`MESSAGE: ${data.msg}`);
                            currentUser = data.user;
                            userLevel = data.level;
                            
                            // Morphic Color Shift
                            if (currentUser === 'NULL') {
                                terminal.classList.add('morphic-mode');
                                createDrip(); // Immediate splash
                            }

                            state = 'CMD';
                            promptLabel.textContent = 'CMD>';
                        } else {
                        print(`${data.msg || "ERROR 401: UNAUTHORIZED"}`, '#ff3e3e');
                            state = 'USER';
                            promptLabel.textContent = 'USER:';
                        }
                    } catch (e) {
                        print("!! CONNECTION ERROR !!", '#ff3e3e');
                        state = 'USER';
                        promptLabel.textContent = 'USER:';
                    }
                } else if (state === 'CMD') {
                    print(`CMD> ${val}`);
                    handleCommand(val);
                }
            }
        });

        async function handleCommand(cmd) {
            const parts = cmd.split(' ');
            const baseCmd = parts[0].toUpperCase();

            // Local-only UI commands
            if (baseCmd === 'CLEAR') {
                output.innerHTML = '';
                return;
            }
            if (baseCmd === 'LOGOUT' || baseCmd === 'SHUTDOWN') {
                print(">> TERMINATING_REMOTE_UPLINK...");
                await new Promise(r => setTimeout(r, 600));
                print(">> SHREDDING_SESSION_BUFFERS...");
                await new Promise(r => setTimeout(r, 400));
                print(">> DISCONNECTED.", "#ff3e3e");
                await new Promise(r => setTimeout(r, 1000));
                
                // Clear and Halt
                output.innerHTML = '';
                terminal.classList.remove('morphic-mode');
                
                const haltDiv = document.createElement('div');
                haltDiv.className = 'line';
                haltDiv.style.color = '#ff3e3e';
                haltDiv.style.fontSize = '1.2rem';
                haltDiv.style.textAlign = 'center';
                haltDiv.style.marginTop = '20%';
                haltDiv.textContent = 'SYSTEM_HALTED.';
                output.appendChild(haltDiv);
                
                const safeDiv = document.createElement('div');
                safeDiv.className = 'line';
                safeDiv.style.textAlign = 'center';
                safeDiv.textContent = 'IT IS NOW SAFE TO POWER OFF YOUR TERMINAL.';
                output.appendChild(safeDiv);
                
                document.querySelector('.input-area').style.display = 'none';
                state = 'HALTED';
                return;
            }

            // All other commands go to the Kernel (API)
            const fd = new FormData();
            fd.append('action', 'command');
            fd.append('cmd', cmd);
            fd.append('currentUser', currentUser);
            
            try {
                const response = await fetch('/api/terminal.php', { method: 'POST', body: fd });
                const data = await response.json();
                
                // Even on 4xx/5xx errors, the kernel might still return thematic output
                if (data.output) {
                    data.output.forEach(line => print(line));
                }

                if (data.stream) {
                    for (const line of data.stream) {
                        print(line);
                        // Brief pause between lines for "drawing" feel
                        await new Promise(r => setTimeout(r, 50));
                    }
                }

                if (data.richContent) {
                    if (data.richContent.type === 'ASCII') {
                        data.richContent.content.split('\n').forEach(line => print(line, "#33ff33", true));
                    } else {
                        print(data.richContent.content);
                    }
                }

                if (data.footer) {
                    print(data.footer, "#fff");
                }

                if (data.action === 'DECRYPT_FLOW') {
                    await runDecryptionUI(data.filename, data.content);
                }

            } catch (e) {
                triggerJitter();
                print("ERROR 500: KERNEL_COMMUNICATION_FAILURE", "#ff3e3e");
            }
        }

        function triggerJitter() {
            // Disabled
        }

        async function runDecryptionUI(filename, content) {
            print(`>> TARGET: ${filename}`);
            print(`>> BRUTE_FORCING_RESONANCE_KEYS...`);
            
            const progressLine = document.createElement('div');
            progressLine.className = 'line';
            output.appendChild(progressLine);
            
            let progress = 0;
            while (progress <= 100) {
                progressLine.textContent = `[${'='.repeat(Math.floor(progress/5)).padEnd(20, ' ')}] ${progress}%`;
                output.scrollTop = output.scrollHeight;
                if (progress === 100) break;
                progress += Math.floor(Math.random() * 10) + 2;
                if (progress > 100) progress = 100;
                await new Promise(r => setTimeout(r, 100 + Math.random() * 200));
            }
            
            print(`>> ACCESS_GRANTED: ENCRYPTION_LATTICE_BROKEN`, "#fff");
            await new Promise(r => setTimeout(r, 600));
            
            print(`--- BEGIN_DECRYPTED_STREAM: ${filename} ---`, "#fff");
            
            // Glitchy reveal of content
            const contentLine = document.createElement('div');
            contentLine.className = 'line';
            output.appendChild(contentLine);
            
            const words = content.split(' ');
            for (let word of words) {
                contentLine.textContent += word + " ";
                output.scrollTop = output.scrollHeight;
                await new Promise(r => setTimeout(r, 30 + Math.random() * 50));
            }
            
            print(`--- END_STREAM ---`, "#fff");
        }
    </script>
</body>
</html>
