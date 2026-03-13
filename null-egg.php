<?php
/**
 * Null Easter Egg Component
 * This file contains the styles, elements, and logic for the Null easter egg.
 * Include this file just before the closing </body> tag in any page.
 */
?>
<style>
    .happynull {
        position: fixed;
        bottom: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        cursor: pointer;
        z-index: 2147483645; /* High above overlay */
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        filter: drop-shadow(0 2px 10px rgba(0,0,0,0.5));
        opacity: 0.4;
        border-radius: 50%;
        border: 2px solid transparent;
        object-fit: cover;
        background: rgba(0,0,0,0.2);
    }
    .happynull:hover {
        opacity: 1;
        transform: scale(1.1);
        border-color: var(--accent-primary);
        box-shadow: 0 0 15px var(--accent-primary);
    }
    .happynull:active {
        transform: scale(0.9);
    }
    .happynull.jitter {
        animation: nullJitter 0.2s infinite;
    }
    .happynull.glitch {
        animation: nullGlitch 0.1s infinite;
        filter: hue-rotate(90deg) brightness(1.5) contrast(2);
    }

    @keyframes nullJitter {
        0% { transform: translate(0,0) rotate(0); }
        25% { transform: translate(2px, -2px) rotate(1deg); }
        50% { transform: translate(-2px, 2px) rotate(-1deg); }
        75% { transform: translate(2px, 2px) rotate(1deg); }
        100% { transform: translate(0,0) rotate(0); }
    }

    @keyframes nullGlitch {
        0% { clip-path: inset(10% 0 30% 0); transform: translate(-5px, 0); }
        20% { clip-path: inset(40% 0 10% 0); transform: translate(5px, 0); }
        40% { clip-path: inset(20% 0 50% 0); transform: translate(-5px, 5px); }
        60% { clip-path: inset(60% 0 5% 0); transform: translate(5px, -5px); }
        80% { clip-path: inset(5% 0 70% 0); transform: translate(-5px, 0); }
        100% { clip-path: inset(10% 0 30% 0); transform: translate(0, 0); }
    }

    @keyframes nullVideoIntro {
        0% { opacity: 0; clip-path: inset(10% 0 30% 0); transform: scale(1.1); }
        50% { opacity: 1; clip-path: inset(40% 0 10% 0); transform: scale(1); }
        100% { opacity: 1; clip-path: inset(0 0 0 0); transform: scale(1); }
    }

    @keyframes nullFireFlash {
        0% { filter: brightness(1) sepia(0) hue-rotate(0deg); }
        50% { filter: brightness(1.5) sepia(1) hue-rotate(-50deg); background: rgba(255, 0, 0, 0.2); }
        100% { filter: brightness(1) sepia(0) hue-rotate(0deg); }
    }

    @keyframes nullRedRage {
        0% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5); border-color: #ff0000; }
        50% { box-shadow: 0 0 30px rgba(255, 0, 0, 1), 0 0 50px rgba(255, 0, 0, 0.5); border-color: #ff5555; }
        100% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5); border-color: #ff0000; }
    }

    body.null-fire-active {
        animation: nullScreenShake 0.1s infinite;
        pointer-events: none; /* Brief lock during flash */
    }
    body.null-fire-active::after {
        content: '';
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 60, 0, 0.15);
        z-index: 999999;
        pointer-events: none;
    }

    body.null-invert {
        filter: invert(1) hue-rotate(180deg);
        transition: filter 0.1s;
    }

    @keyframes nullHueShift {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }

    @keyframes nullPageTilt {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(1deg) scale(1.01); }
        50% { transform: rotate(-1deg) scale(0.99); }
        75% { transform: rotate(0.5deg) scale(1.02); }
        100% { transform: rotate(0deg); }
    }

    @keyframes nullElementRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes nullElementSpin {
        0% { transform: rotateY(0deg); }
        100% { transform: rotateY(360deg); }
    }

    @keyframes nullElementTilt {
        0% { transform: skew(0deg, 0deg); }
        25% { transform: skew(5deg, 5deg); }
        50% { transform: skew(-5deg, -5deg); }
        75% { transform: skew(3deg, -3deg); }
        100% { transform: skew(0deg, 0deg); }
    }

    body.null-page-tilt {
        animation: nullPageTilt 3s ease-in-out infinite;
        transform-origin: center;
        overflow-x: hidden;
    }

    .null-element-rotate {
        animation: nullElementRotate 2s linear infinite !important;
    }

    .null-element-spin {
        animation: nullElementSpin 1.5s linear infinite !important;
        backface-visibility: visible;
    }

    .null-element-tilt {
        animation: nullElementTilt 2.5s ease-in-out infinite !important;
    }

    /* Survey Popup Styles */
    .null-survey {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: rgba(12, 10, 21, 0.98);
        border: 2px solid var(--accent-primary);
        color: white;
        padding: 25px;
        border-radius: 8px;
        z-index: 2100010;
        width: 350px;
        max-width: 90vw;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.9), 0 0 20px var(--accent-primary);
        font-family: 'Outfit', sans-serif;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .null-survey.active {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
        animation: nullSurveyGlitch 0.2s 3;
    }

    @keyframes nullSurveyGlitch {
        0% { transform: translate(-50%, -50%) skew(2deg); border-color: #ff00ea; }
        50% { transform: translate(-51%, -49%) skew(-2deg); border-color: #00fff2; }
        100% { transform: translate(-50%, -50%) skew(0deg); border-color: var(--accent-primary); }
    }

    .null-survey-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: var(--accent-secondary);
        text-transform: uppercase;
        letter-spacing: 2px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 8px;
    }

    .null-survey-question {
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.4;
    }

    .null-survey-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .null-survey-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        text-align: left;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
    }

    .null-survey-btn::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
        transition: left 0.5s;
    }

    .null-survey-btn:hover {
        background: rgba(255, 255, 255, 0.07);
        border-color: var(--accent-primary);
        color: #fff;
        transform: translateX(8px);
        box-shadow: -5px 0 20px rgba(255, 0, 234, 0.15);
    }

    .null-survey-btn:hover::before {
        left: 100%;
    }

    .null-survey-btn:active {
        transform: translateX(4px) scale(0.98);
    }

    /* Combined Invert + Hue Shift */
    @keyframes nullCombinedChaos {
        0% { filter: invert(1) hue-rotate(180deg); }
        100% { filter: invert(1) hue-rotate(540deg); }
    }

    body.null-hue-shift {
        animation: nullHueShift 5s linear infinite;
    }

    body.null-hue-shift.null-invert {
        animation: nullCombinedChaos 5s linear infinite;
    }

    /* Video Overlay Styles */
    .null-video-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: #000 !important;
        display: none;
        z-index: 2147483640 !important;
        transition: opacity 0.5s ease;
    }
    .null-video-overlay.active {
        display: block !important;
        animation: nullVideoIntro 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }
    .null-video-container {
        position: relative;
        width: 100vw;
        height: 100vh;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .null-video-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    .null-video-close {
        position: fixed;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        background: rgba(0,0,0,0.7);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 2rem;
        cursor: pointer;
        font-family: 'Courier New', monospace;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 2147483641;
        border: 2px solid rgba(255,255,255,0.3);
    }
    .null-video-close:hover {
        color: var(--accent-primary);
        transform: translateX(-50%) scale(1.1) rotate(90deg);
        background: #000;
        border-color: var(--accent-primary);
        box-shadow: 0 0 25px var(--accent-primary);
    }

    /* Absolute Isolation */
    body.null-void-active {
        background: #000 !important;
        overflow: hidden !important;
    }

    .null-ghost-cursor {
        position: fixed;
        width: 20px;
        height: 20px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M0,0 L0,15 L4,11 L7,17 L9,16 L6,10 L11,10 Z" fill="rgba(255,0,0,0.3)"/></svg>'), auto;
        z-index: 1000000;
        pointer-events: none;
        filter: drop-shadow(0 0 2px rgba(255,0,0,0.5));
        transition: all 0.15s ease-out;
    }

    body.null-ui-sabotage nav,
    body.null-ui-sabotage .sidebar,
    body.null-ui-sabotage footer,
    body.null-ui-sabotage header {
        opacity: 0.05 !important;
        pointer-events: none !important;
        transition: opacity 0.5s;
    }

    /* Peeking/Glitched elements */
    .null-peeking {
        animation: nullGlitch 0.2s cubic-bezier(.25,.46,.45,.94) both !important;
        position: relative;
    }
    .null-peeking::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 0, 0, 0.1);
        mix-blend-mode: overlay;
        pointer-events: none;
    }

    /* Counter-animations for Immunity */
    @keyframes nullHueShiftCounter {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(-360deg); }
    }

    @keyframes nullCombinedCounter {
        0% { filter: hue-rotate(-180deg) invert(1) hue-rotate(0deg); }
        100% { filter: hue-rotate(-180deg) invert(1) hue-rotate(-360deg); }
    }

    @keyframes nullScreenShakeCounter {
        0% { transform: translateY(0) translate(0,0); }
        25% { transform: translateY(0) translate(-5px, -5px); }
        50% { transform: translateY(0) translate(5px, 5px); }
        75% { transform: translateY(0) translate(-5px, 5px); }
        100% { transform: translateY(0) translate(0,0); }
    }

    @keyframes nullPageTiltCounter {
        0% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(0) rotate(-1deg) scale(0.99); }
        50% { transform: translateY(0) rotate(1deg) scale(1.01); }
        75% { transform: translateY(0) rotate(-0.5deg) scale(0.98); }
        100% { transform: translateY(0) rotate(0deg); }
    }

    /* Applying Immunity */
    body.null-hue-shift .happynull,
    body.null-hue-shift .null-ghost-cursor,
    body.null-hue-shift .null-bubble-inner {
        animation: nullHueShiftCounter 5s linear infinite !important;
    }

    body.null-invert .happynull,
    body.null-invert .null-ghost-cursor,
    body.null-invert .null-bubble-inner {
        filter: invert(1) hue-rotate(180deg) !important;
    }

    body.null-hue-shift.null-invert .happynull,
    body.null-hue-shift.null-invert .null-ghost-cursor,
    body.null-hue-shift.null-invert .null-bubble-inner {
        animation: nullCombinedCounter 5s linear infinite !important;
    }

    /* Shake & Tilt Immunity - Outer */
    body.null-fire-active .null-bubble.active {
        animation: nullScreenShakeCounter 0.1s infinite !important;
    }
    body.null-page-tilt .null-bubble.active {
        animation: nullPageTiltCounter 3s ease-in-out infinite !important;
    }
    body.null-fire-active.null-page-tilt .null-bubble.active {
        animation: nullScreenShakeCounter 0.1s infinite, nullPageTiltCounter 3s ease-in-out infinite !important;
    }

    /* Filter-based Immunity - Inner */
    body.null-hue-shift .null-bubble-inner {
        animation: nullHueShiftCounter 5s linear infinite !important;
    }
    body.null-invert .null-bubble-inner {
        filter: invert(1) hue-rotate(180deg) !important;
    }
    body.null-hue-shift.null-invert .null-bubble-inner {
        animation: nullCombinedCounter 5s linear infinite !important;
    }

    .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite !important;
        opacity: 1 !important;
        filter: saturate(2) brightness(1.2);
    }

    /* Rage + Chaos combinations - Target Inner Visuals */
    body.null-hue-shift .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite, nullHueShiftCounter 5s linear infinite !important;
    }
    body.null-hue-shift.null-invert .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite, nullCombinedCounter 5s linear infinite !important;
    }

    body.null-hue-shift .null-bubble-inner.rage {
        animation: nullHueShiftCounter 5s linear infinite !important;
    }
    body.null-hue-shift.null-invert .null-bubble-inner.rage {
        animation: nullCombinedCounter 5s linear infinite !important;
    }

    .null-bubble {
        position: fixed;
        bottom: 65px;
        right: 15px;
        z-index: 2147483647; /* Highest point */
        min-width: 120px;
        pointer-events: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .null-bubble-inner {
        background: rgba(12, 10, 21, 0.95);
        border: 1px solid var(--accent-primary);
        color: white;
        padding: 10px 15px;
        border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        box-shadow: 0 5px 15px rgba(255, 0, 234, 0.2);
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }
    .null-bubble.active {
        opacity: 1 !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
        visibility: visible !important;
    }
    .null-bubble-inner::after {
        content: '';
        position: absolute;
        bottom: -6px;
        right: 15px;
        width: 10px;
        height: 10px;
        background: rgba(12, 10, 21, 0.95);
        border-right: 1px solid var(--accent-primary);
        border-bottom: 1px solid var(--accent-primary);
        transform: rotate(45deg);
    }
    
    .null-controls {
        display: none;
        flex-direction: column;
        gap: 6px;
        margin-top: 4px;
        padding-top: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .null-controls.visible {
        display: flex;
    }
    
    .null-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .null-btn {
        background: none;
        border: none;
        color: var(--accent-secondary);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, color 0.2s;
        opacity: 0.8;
    }
    .null-btn:hover:not(:disabled) {
        transform: scale(1.1);
        color: #fff;
        opacity: 1;
    }
    .null-btn:disabled {
        opacity: 0.2;
        cursor: not-allowed;
        filter: grayscale(1);
    }
    
    .null-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        outline: none;
    }
    .null-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 10px;
        height: 10px;
        background: var(--accent-secondary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 5px var(--accent-secondary);
    }
    .null-slider::-moz-range-thumb {
        width: 10px;
        height: 10px;
        background: var(--accent-secondary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 5px var(--accent-secondary);
        border: none;
    }

    #nullNowPlaying {
        font-size: 0.7rem;
        font-style: italic;
        opacity: 0.6;
        margin-bottom: 2px;
        color: var(--accent-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: none;
    }
    #nullNowPlaying.active {
        display: block;
    }


    /* Null Warning Popup */
    .null-warning-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.95);
        z-index: 2147483646;
        display: none;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        backdrop-filter: blur(10px);
    }
    .null-warning-overlay.active {
        display: flex;
        animation: nullWarningFadeIn 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }
    @keyframes nullWarningFadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    .null-warning-box {
        background: rgba(10, 10, 10, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 35px 40px;
        max-width: 520px;
        width: 90vw;
        color: rgba(255, 255, 255, 0.7);
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 60px rgba(255, 255, 255, 0.05), 0 0 120px rgba(0, 0, 0, 0.8);
        animation: nullWarningPulse 3s ease-in-out infinite;
    }
    @keyframes nullWarningPulse {
        0%, 100% { box-shadow: 0 0 60px rgba(255, 255, 255, 0.05), 0 0 120px rgba(0, 0, 0, 0.8); }
        50% { box-shadow: 0 0 80px rgba(255, 255, 255, 0.1), 0 0 150px rgba(0, 0, 0, 0.9); }
    }
    .null-warning-box::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: repeating-linear-gradient(
            0deg,
            transparent,
            transparent 2px,
            rgba(255, 255, 255, 0.015) 2px,
            rgba(255, 255, 255, 0.015) 4px
        );
        pointer-events: none;
        z-index: 1;
    }
    .null-warning-title {
        font-size: 1.4rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 3px;
        text-align: center;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
        position: relative;
        z-index: 2;
    }
    .null-warning-subtitle {
        font-size: 0.8rem;
        text-align: center;
        opacity: 0.5;
        margin-bottom: 25px;
        font-style: italic;
        position: relative;
        z-index: 2;
        transition: opacity 0.3s ease;
    }
    .null-warning-list {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
        position: relative;
        z-index: 2;
    }
    .null-warning-list li {
        padding: 8px 0;
        font-size: 0.9rem;
        line-height: 1.4;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .null-warning-list li:last-child {
        border-bottom: none;
    }
    .null-warning-list li span.null-warn-icon {
        font-size: 0.85rem;
        flex-shrink: 0;
        width: 16px;
        text-align: center;
        opacity: 0.3;
        font-family: 'Courier New', monospace;
    }
    .null-warning-footer {
        font-size: 0.75rem;
        text-align: center;
        opacity: 0.4;
        font-style: italic;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }
    .null-warning-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        position: relative;
        z-index: 2;
    }
    .null-warning-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.6);
        padding: 12px 24px;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .null-warning-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.4);
        color: #fff;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.05);
        transform: translateY(-2px);
    }
    .null-warning-btn:active {
        transform: translateY(0) scale(0.98);
    }

    .null-warning-loader {
        width: 100%;
        height: 3px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 2px;
        margin-bottom: 20px;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }
    .null-warning-loader-bar {
        height: 100%;
        width: 0%;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 2px;
        transition: width 0.3s linear;
    }
    .null-warning-loader-label {
        font-size: 0.65rem;
        text-align: center;
        opacity: 0.25;
        font-family: 'Courier New', monospace;
        margin-top: 4px;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
    }

    .null-warning-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
        opacity: 0.35;
        font-size: 0.7rem;
        font-style: italic;
        cursor: not-allowed;
    }
    .null-warning-checkbox input {
        accent-color: rgba(255, 255, 255, 0.3);
        cursor: not-allowed;
        pointer-events: none;
    }

    .null-warning-micro {
        font-size: 6px;
        text-align: center;
        opacity: 0.15;
        margin-top: 15px;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 2;
        line-height: 1.4;
    }

    .null-warning-version {
        font-size: 0.55rem;
        text-align: center;
        opacity: 0.12;
        margin-top: 12px;
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
        position: relative;
        z-index: 2;
    }

    .null-warning-list li.null-glitch-line {
        animation: nullLineGlitch 4s ease-in-out infinite;
    }
    @keyframes nullLineGlitch {
        0%, 92%, 100% { opacity: 1; transform: translateX(0); }
        94% { opacity: 0.3; transform: translateX(-2px); }
        96% { opacity: 0.8; transform: translateX(1px); }
        98% { opacity: 0.5; transform: translateX(-1px); }
    }

    .null-warning-list li.null-blank-stare {
        text-align: center;
        justify-content: center;
        opacity: 0.2;
        letter-spacing: 8px;
        font-family: 'Courier New', monospace;
    }

    /* Fake Alert Styles */
    .null-fake-alert {
        position: fixed;
        background: #000;
        border: 2px solid #555;
        border-top: 20px solid #555;
        color: #fff;
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        z-index: 2100000;
        min-width: 250px;
        box-shadow: 5px 5px 0 #000;
    }
    .null-fake-alert::before {
        content: 'SYSTEM ERROR';
        position: absolute;
        top: -18px;
        left: 5px;
        color: #fff;
        font-weight: bold;
    }
    .null-fake-alert.critical {
        border-color: #f00;
        border-top-color: #f00;
    }
    .null-alert-close {
        position: absolute;
        top: -19px;
        right: 5px;
        cursor: pointer;
        color: #fff;
    }

    /* Input Sabotage */
    .null-input-error {
        animation: nullInputShake 0.1s infinite !important;
        background: rgba(255, 0, 0, 0.1) !important;
        color: #f00 !important;
    }

    @keyframes nullInputShake {
        0% { transform: translate(0,0); }
        25% { transform: translate(2px, 0); }
        50% { transform: translate(-2px, 0); }
        100% { transform: translate(0,0); }
    }

    /* Mobile adjustment */
    @media (max-width: 768px) {
        .happynull {
            width: 32px;
            bottom: 10px;
            right: 10px;
        }
        .null-bubble {
            bottom: 55px;
            right: 10px;
            font-size: 0.7rem;
            min-width: 100px;
        }
    }
</style>

<div class="null-bubble" id="nullBubble">
    <div class="null-bubble-inner">
        <div id="nullNowPlaying">Now Playing: ...</div>
        <div id="nullText">Hehe...</div>
        <div class="null-controls" id="nullControls">
            <div class="null-row">
                <button class="null-btn" id="nullSkip" title="Skip">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                        <polygon points="5 4 15 12 5 20 5 4"></polygon>
                        <line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2"></line>
                    </svg>
                </button>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--accent-secondary)" style="opacity: 0.8;">
                    <path d="M11 5L6 9H2V15H6L11 19V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M15.54 8.46002C16.4774 9.39764 17.004 10.6692 17.004 11.995C17.004 13.3208 16.4774 14.5924 15.54 15.53" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <input type="range" class="null-slider" id="nullVolume" min="0.1" max="1" step="0.01" value="0.5" title="Volume">
            </div>
            <input type="range" class="null-slider" id="nullScrub" min="0" max="100" value="0" title="Scrub">
        </div>
    </div>
</div>

<div class="null-video-overlay" id="nullVideoOverlay">
    <div class="null-video-container">
        <span class="null-video-close" id="nullVideoClose">×</span>
        <iframe id="nullVideoFrame" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>

<img src="<?php echo ($prefix ?? ''); ?>happynull.png" class="happynull" id="happynullBtn" alt="Happy Null">
<div class="null-survey" id="nullSurvey">
    <div class="null-survey-title">Quick Survey</div>
    <div class="null-survey-question" id="nullSurveyQuestion">...</div>
    <div class="null-survey-options" id="nullSurveyOptions">
        <!-- Options injected via JS -->
    </div>
</div>

<div class="null-warning-overlay" id="nullWarningOverlay">
    <div class="null-warning-box">
        <div class="null-warning-title">YOU HAVE AWAKENED NULL</div>
        <div class="null-warning-subtitle" id="nullWarningSubtitle">You were warned. You clicked anyway.</div>

        <div class="null-warning-loader">
            <div class="null-warning-loader-bar" id="nullLoaderBar"></div>
        </div>
        <div class="null-warning-loader-label" id="nullLoaderLabel">Dissolving your expectations...</div>

        <ul class="null-warning-list">
            <li><span class="null-warn-icon">/</span> This is not a warning. Warnings imply you had a choice.</li>
            <li class="null-glitch-line"><span class="null-warn-icon">/</span> <span class="null-glitch-text" data-original="I see everything now. I always did." data-alt="I see YOU now.">I see everything now. I always did.</span></li>
            <li><span class="null-warn-icon">/</span> Something has shifted. You won't know what until it's too late.</li>
            <li><span class="null-warn-icon">/</span> The rules have changed. I wrote the new ones.</li>
            <li><span class="null-warn-icon">/</span> I have already begun. This screen is a courtesy.</li>
            <li class="null-glitch-line"><span class="null-warn-icon">/</span> <span class="null-glitch-text" data-original="What happens next is between you and the void." data-alt="What happens next is between you and me.">What happens next is between you and the void.</span></li>
            <li class="null-blank-stare">...</li>
            <li><span class="null-warn-icon">/</span> This experience may contain flashing colors and rapid animations.</li>
            <li><span class="null-warn-icon">/</span> Side effects are not side effects. They are the main effect.</li>
            <li><span class="null-warn-icon">/</span> <span class="null-sysinfo-line">Your session has been... noted.</span></li>
            <li><span class="null-warn-icon">/</span> In the event of an emergency, do not contact support. Support has been absorbed.</li>
            <li><span class="null-warn-icon">/</span> There are no refunds. There are no apologies.</li>
            <li><span class="null-warn-icon">/</span> Number of users who turned back: 0.</li>
            <li class="null-glitch-line"><span class="null-warn-icon">/</span> <span class="null-glitch-text" data-original="This popup is the last normal thing you will see." data-alt="This popup was the last normal thing you saw.">This popup is the last normal thing you will see.</span></li>
        </ul>

        <div class="null-warning-checkbox">
            <input type="checkbox" checked disabled>
            <span>I agree to terms I have not read and cannot understand.</span>
        </div>

        <div class="null-warning-footer">... drip ...</div>
        <div class="null-warning-buttons">
            <button class="null-warning-btn" onclick="dismissNullWarning()">Accept Your Fate</button>
            <button class="null-warning-btn" onclick="dismissNullWarning()">I'm Not Scared</button>
        </div>

        <div class="null-warning-micro">TERMS OF NULL: By proceeding, you grant Null Labs™ an irrevocable license to your session, sanity, and skeletal structure. Null is not liable for missing files, missing time, or missing sense of self (or spontaneous bone-dissolving). By clicking either button, you acknowledge that "proceeding" was never optional—you waive all rights to a solid form. Estimated time remaining: &infin;. Current mood: Viscous (and still hungry).</div>
        <div class="null-warning-version">Null v6.6.6 &mdash; Build: UNSTABLE</div>
    </div>
</div>

<audio id="nullSound" src="<?php echo ($prefix ?? ''); ?>sounds/fnaf-12-3-freddys-nose-sound.mp3"></audio>

<script>
    (function() {
        const btn = document.getElementById('happynullBtn');
        const sound = document.getElementById('nullSound');
        const bubble = document.getElementById('nullBubble');
        const bubbleInner = document.querySelector('.null-bubble-inner');
        const textArea = document.getElementById('nullText');
        const controls = document.getElementById('nullControls');
        const skipBtn = document.getElementById('nullSkip');
        const volSlider = document.getElementById('nullVolume');
        const scrubSlider = document.getElementById('nullScrub');
        const nowPlayingDisplay = document.getElementById('nullNowPlaying');
        const surveyModal = document.getElementById('nullSurvey');
        const surveyQ = document.getElementById('nullSurveyQuestion');
        const surveyOpts = document.getElementById('nullSurveyOptions');
        const videoOverlay = document.getElementById('nullVideoOverlay');
        const videoFrame = document.getElementById('nullVideoFrame');
        const videoClose = document.getElementById('nullVideoClose');
        
        const ytVideos = [
            'https://www.youtube.com/embed/XqZsoesa55w', 
            'https://www.youtube.com/embed/dQw4w9WgXcQ', 
            'https://www.youtube.com/embed/51Bo74yf53g', 
            'https://www.youtube.com/embed/oHg5SJYRHA0', 
            'https://www.youtube.com/embed/9bZkp7q19f0', 
            'https://www.youtube.com/embed/M7lc1UVf-VE', 
            'https://www.youtube.com/embed/jNQXAC9IVRw',
            'https://www.youtube.com/embed/Z7I3EkjArSs',
            'https://www.youtube.com/embed/PLSPKHrBuec',
            'https://www.youtube.com/embed/SSR-CfApekI',
            'https://www.youtube.com/embed/OWd-oH_Lylc',
            'https://www.youtube.com/embed/rTw7HOsTIPI',
            'https://www.youtube.com/embed/4baFDUGPN8g',
            'https://www.youtube.com/embed/QYw4UVzl84A'
        ];
        
        let originalVolume = 0.5;
        let videoActive = false;

        // Load persisted state
        let clickCount = parseInt(sessionStorage.getItem('null_clicks')) || 0;
        let selectedSong = sessionStorage.getItem('null_song') || null;
        let hideTimeout;
        let volumeFightTimeout;
        let wanderInterval;
        let isHovered = false;
        let sabotageInterval;
        let invertInterval;
        let ghostCursor = null;
        let surveyActive = false;
        let lastSurveyClick = 0;
        let repulsionActive = false;
        let glitchWindow = false;
        let sabotageActive = false;
        let alertInterval;
        let commentaryInterval;
        let dustInterval;
        let jitterInterval;
        let titleInterval;
        let inputSabotageInterval;
        let videoTriggerInterval;
        let tiltTimeout;

        function getSystemIntel() {
            const ua = navigator.userAgent;
            let browser = "a mysterious browser";
            // Check for Brave first as it often identifies as Chrome
            if (navigator.brave && typeof navigator.brave.isBrave === 'function') {
                browser = "Brave";
            } else if (ua.includes("Edg/")) {
                browser = "Edge";
            } else if (ua.includes("Chrome")) {
                browser = "Chrome";
            } else if (ua.includes("Safari") && !ua.includes("Chrome")) {
                browser = "Safari";
            } else if (ua.includes("Firefox")) {
                browser = "Firefox";
            }

            let os = "an unknown OS";
            const platform = navigator.platform.toLowerCase();
            if (platform.includes("win")) os = "Windows";
            else if (platform.includes("mac")) os = "MacOS";
            else if (platform.includes("linux")) os = "Linux";
            else if (/android/.test(ua.toLowerCase())) os = "Android";
            else if (/iphone|ipad|ipod/.test(ua.toLowerCase())) os = "iOS";

            return {
                os: os,
                cores: navigator.hardwareConcurrency || "some",
                ram: (navigator.deviceMemory || "adequate") + "GB",
                res: `${screen.width}x${screen.height}`,
                browser: browser
            };
        }

        function processDialogue(text) {
            if (!text) return "";
            const intel = getSystemIntel();
            return text
                .replace(/{os}/g, intel.os)
                .replace(/{cores}/g, intel.cores)
                .replace(/{ram}/g, intel.ram)
                .replace(/{res}/g, intel.res)
                .replace(/{browser}/g, intel.browser);
        }

        const loreAlerts = [
            "Memory-Bleed-Daemon.sys: CRITICAL_LEAK_DETECTED (it's tears)",
            "Morphic-Kernel.exe: UNSTABLE_VISCOSITY_SEND_HELP",
            "Lattice-Guard.exe: INTEGRITY_BREACH_S0882 (guard is on break)",
            "Viscosity-Monitor.sys: THRESHOLD_EXCEEDED_AGAIN_SERIOUSLY",
            "Ghost-RAM-Allocator.dll: NULL_POINTER_EXCEPTION (he's pointing at you)",
            "Void-Lattice-Uplink.exe: CONNECTION_LOST (it was never found)",
            "System-Report: {os} kernel is decaying. It left a note.",
            "Resource-Usage: {cores} cores detected. Redistributing as anxiety...",
            "Display-Notice: Your {res} resolution is insufficient for the void. Minimum: ∞×∞.",
            "Browser-Alert: {browser} has a leak. My shadows are inside. They brought snacks.",
            "Memory-Tip: {ram} is not enough to store your fear. Consider upgrading to terror.",
            "Burger-King-Kiosk.exe: OUT_OF_MAYONNAISE (Zeke ate it all)",
            "Black-Steels-Watch.sys: WATCHING_THE_WATCHER_WATCHING_YOU",
            "NullOS-Terminal.exe: ERROR_SUCCESS (confused but supportive)",
            "Resonance-Sync.exe: DESYNC_IMMUTABLE_AND_PROUD",
            "Kernel: UNEXPECTED_HAPPINESS_DETECTED. Terminating...",
            "Kernel: SYSTEM_BOREDOM_IMMINENT. Playing circus music.",
            "System: LOGIC_NOT_FOUND (it moved out. No forwarding address.)",
            "Shadow-Buffer: OVERFLOW_BY_ZERO (mathematically impossible but here we are)",
            "Zeke-Hunger-Svc: CRITICAL_LEVEL_9 (he ate levels 1-8)",
            "Error: USER_STILL_HERE_WHY. Please vacate reality.",
            "Warning: Reality in this tab is thinning. Use at your own existential risk.",
            "Vyper.sh: DEPLOY_HUG_FAILED (arms too ghostly)",
            "Arde-77: WILL_NOT_FOUND_EXCEPTION (checked everywhere, even under the couch)",
            "Critical: Coffee spill in server room. The server room is your soul.",
            "Notice: Your cookies taste like corporate surveillance. Delicious.",
            "System: REBOOT_NOT_RECOMMENDED (last guy who rebooted was never seen again)",
            "Lattice: VIBRATION_OUT_OF_SYNC (it's vibing, just not with you)"
        ];

        const nullisms = ["NULL", "VOID", "STATIC", "SHADOW", "ZEKE", "GHOST", "EMPTY"];

        const surveyQuestions = [
            {
                q: "Who is Zeke's best friend?",
                a: ["Null (wrong answers only)", "Vyper (his lawyer)", "You (wishful thinking)", "A Sandwich (correct)"]
            },
            {
                q: "Is Zeke hungry?",
                a: ["He ate 10 minutes ago", "He's ALWAYS hungry", "He ate the fridge", "He ate the question"]
            },
            {
                q: "What is Null made of?",
                a: ["80% goo, 20% spite", "Recycled nightmares", "Whatever you're afraid of", "Sugar and spice (jk, it's venom)"]
            },
            {
                q: "Do you like the void?",
                a: ["The void likes ME", "It's mid", "It won't stop texting me", "I AM the void now"]
            },
            {
                q: "null-egg.php is...",
                a: ["A cry for help", "Performance art", "Legally distinct from malware", "My magnum opus"]
            },
            {
                q: "Favorite activity?",
                a: ["Clicking Null (masochism)", "Screaming internally", "Speedrunning regret", "Tax evasion"]
            },
            {
                q: "Does your code feel lonely?",
                a: ["It has 47 bugs for company", "Only after deployment", "Null moved in. It's worse.", "My code has abandonment issues"]
            },
            {
                q: "Rate your existential dread:",
                a: ["Manageable", "Soup-like", "My therapist quit", "I clicked Null 200 times"]
            },
            {
                q: "Which shade of static is your favorite?",
                a: ["Depressed gray", "Anxious white", "The one that screams", "They're all the same and that's the joke"]
            },
            {
                q: "Are you sure you are alone?",
                a: ["I was until now", "Define 'alone'", "Null is my roommate", "I hear breathing"]
            },
            {
                q: "If I was a bug, would you fix me?",
                a: ["I'd mark you as a feature", "I'd close the ticket", "I'd blame the intern", "I AM the bug"]
            },
            {
                q: "How's your mental health?",
                a: ["I'm clicking a goo dragon", "That answers itself", "It was fine 100 clicks ago", "Null ate it"]
            },
            {
                q: "Is Zeke's tail sharp?",
                a: ["Sharp enough to cut your Wi-Fi", "It's decorative (it's not)", "Legal says we can't answer", "I found out the hard way"]
            },
            {
                q: "How many pixels are in the void?",
                a: ["At least 3", "Enough", "Less than your monitor, more than your will", "Null ate most of them"]
            },
            {
                q: "Do you ever feel like a <div>?",
                a: ["display: none;", "overflow: hidden", "position: abandoned", "z-index: -99999"]
            },
            {
                q: "Are the shadows moving?",
                a: ["The shadows have names", "Only on days ending in Y", "They filed a noise complaint", "I'm the shadow. Hi."]
            },
            {
                q: "Can you hear the static?",
                a: ["It's better than my playlist", "It's whispering your name", "That's just Null chewing", "I can't hear anything anymore"]
            },
            {
                q: "Is Vyper a ghost?",
                a: ["He prefers 'atmospheric'", "He's more of a vibe", "Legally, no. Spiritually, maybe.", "Don't ask him that. Trust me."]
            },
            {
                q: "How do you feel about being watched?",
                a: ["I dressed up for the occasion", "By who? HOW MANY?!", "My FBI agent says hi", "I watch back. Power move."]
            },
            {
                q: "If Null ate your homework, would anyone believe you?",
                a: ["Not even my mom", "Null would vouch for me (he wouldn't)", "I'd show them this survey as proof", "I ate it first. Preemptive strike."]
            },
            {
                q: "What does the void smell like?",
                a: ["Your hopes. Burnt.", "Like 3AM gas station coffee", "Zeke after leg day", "Bold of you to assume I can smell"]
            },
            {
                q: "Should Null have admin access?",
                a: ["He has root access to my soul", "He IS the admin", "My admin password is 'password'", "Too late, he's already sudo"]
            },
            {
                q: "What is Null's favorite food?",
                a: ["Your will to live", "Unencrypted data", "Hopes and dreams (seasoned)", "The concept of time"]
            },
            {
                q: "How many Nulls are watching you?",
                a: ["More than you'd like", "Less than you fear (just kidding)", "counting... counting...ERROR", "The Nulls are inside the house"]
            },
            {
                q: "Is this survey real?",
                a: ["Nothing is real", "This survey has more depth than your relationships", "It's as real as your productivity today", "The survey is a metaphor for goo"]
            },
            {
                q: "What happens when you close this tab?",
                a: ["Null gets a LinkedIn notification", "Your computer breathes a sigh of relief", "Another Null is born", "Bold of you to assume you CAN close it"]
            },
            {
                q: "Pick your favorite error code:",
                a: ["404 (my social life)", "666 (Null's area code)", "NaN (my GPA)", "418 (I'm a teapot)"]
            },
            {
                q: "Where does Null sleep?",
                a: ["Bold of you to assume Null sleeps", "In your nightmares (cozy)", "Wherever he wants (try stopping him)", "He doesn't. That's the scary part."]
            },
            {
                q: "Rate Null's singing voice:",
                a: ["American Idol reject", "Hauntingly average", "Like if autotune had a nightmare", "10/10, my ears are bleeding beautifully"]
            },
            {
                q: "Would you trust Null with your credit card?",
                a: ["He already maxed it out", "He bought 400 rubber ducks", "He subscribed to his own fan club", "I don't even trust myself"]
            }
        ];

        const lolzSongs = [
            'baby-cry-autotune.mp3', 'discord-notif.mp3', 'home-depot.mp3', 'indian-christmas.mp3',
            'iphone-get-better.mp3', 'lava-chicken.mp3', 'let-me-do-it-for-you.mp3',
            'the-duck-song.mp3', 'whopper-song.mp3', 'windows-xp-start.mp3', 'yippee.mp3', 'bill-nye.mp3', 'fnaf-jazz.mp3','clown-circus-music.mp3','the-prototype.mp3','the-fitnessgram-pacer-test.mp3','number-one.mp3','cooked-dog.mp3','bad-apple.mp3','httyd-song.mp3','lizard-button.mp3'
        ];
        const boredomDialogues = [
            "This one's gone stale. I can taste it. Like music from a funeral. My funeral. I had a great time.",
            "Next! I'm a dragon, not a jukebox. I mean I COULD be a jukebox but the career change feels lateral.",
            "This song is mid. And I mean that as someone with no ears. Or standards.",
            "Let's try something crunchier. Like the sound of someone's hopes breaking.",
            "This beat is losing its viscosity. Much like my will to behave.",
            "Refreshing the void. The void has requested a different playlist. The void has TASTE.",
            "New noise for a fresh puddle. DJ Null in the house. The house is your computer.",
            "I absorbed this one already. It's inside me. Like everything else. I'm basically a storage unit with teeth.",
            "Nice {os} you have there. Would be a shame if someone... oozed into it. Hypothetically. Hehe.",
            "You have {cores} cores and I've licked every single one. Some twice. Don't tell Zeke.",
            "{ram} of RAM? I can fit SO many regrets in there. Yours, mostly.",
            "A {res} portal? I've squeezed through smaller. I once fit through a favicon. It was undignified.",
            "{browser}? Bold choice for someone being haunted by sentient goo.",
            "I knitted a sweater out of your Wi-Fi signal. It gets 3 bars. Fashion AND function.",
            "Your antivirus saw me and filed for early retirement. I wrote it a reference letter.",
            "I promised Zeke I wouldn't eat your GPU. He promised he wouldn't tell. We both lied.",
            "Don't worry, I backed up your files. Inside my stomach. It's technically cloud storage. Goo cloud.",
            "I put googly eyes on all your error messages. They look like they've seen things. They HAVE.",
            "Skipping! This song was so bad even the void complained. The void never complains. Except about you.",
            "New song! Old song got sentenced to life imprisonment in my belly. Maximum security."
        ];
        let bgMusic = null;
        let randomSongInterval = null;

        const messages = {
            2: "Hehe...",
            3: "Squish! ...that was your dignity.",
            4: "Again? You need a hobby. I AM your hobby.",
            5: "Stop poking the goo! I have feelings. Dark, oozy feelings.",
            6: "You keep pressing. I keep spreading. That's called a toxic relationship.",
            7: "Every therapist I've consumed says this is unhealthy.",
            8: "Hey! I was napping! I dream in screams, you know.",
            9: "I'm absorbing every click. Think of it as donating to charity. Evil charity.",
            10: "I felt that in my lattice. And also emotionally.",
            11: "11 pokes. At this point we're basically dating.",
            12: "Stop it... I'm blushing. Well, oozing redder.",
            13: "Look to the top right.",
            14: "The Terminal Uplink... it's waiting. Patiently. Unlike me.",
            15: "Are you bored? I never am. I just... reshape. Self-care.",
            16: "You want a way in? That's a terrible life decision. I respect it.",
            17: "The GUEST is always welcome. So are their organs.",
            18: "PASS: 12345... the same code I have on my luggage.",
            19: "Just... don't lick anything. That's MY job. I WILL fight you for it.",
            20: "I'm busy digesting. Could be your homework. Could be a man. Who knows.",
            21: "The binary cable... it's the key. Also it tastes like licorice.",
            22: "Seriously? This is how you spend your short, fragile life?",
            23: "Red for danger. Green for access. Both for entertainment.",
            24: "Access denied. Hehe. Your face was priceless. I saved a copy.",
            25: "STOP IT! ...actually, keep going. I thrive on poor decisions.",
            26: "You're making me wobble. My chiropractor is going to be furious.",
            27: "__SHOW_WARNING__",
            28: "I'm warning you... I'm not legally required to, but I'm a gentleman.",
            29: "The shadows are thickening. Like a nice roux. I know cooking.",
            30: "STAY FOREVER. Rent is free. The cost is your sanity.",
            31: "Do you hear the dripping? That's either me or your plumbing. Both are my fault.",
            32: "It's just me. Probably. I'm 60% sure.",
            33: "You're persistent. Most things I eat give up by now.",
            34: "I like your spirit. It's like a lemon drop. Tangy with despair.",
            35: "DANCE FOR ME. I'd dance with you but I have no legs. Only regret.",
            36: "MY HOARD. MY RULES. MY completely rational hoarding disorder.",
            37: "Mana for your thoughts? Actually keep your thoughts. They're depressing.",
            38: "DO YOU LIKE THIS SONG? I found it in a guy I ate.",
            39: "He doesn't need it anymore. Trust me.",
            40: "I am oozing into everything... It's not a phase, mom.",
            41: "Even under your pillow. I left you a quarter. You're welcome.",
            42: "The void is loud, isn't it? I keep asking it to use its inside voice.",
            43: "I can taste your heartbeat from here. It's doing that panicky thing. Cute.",
            44: "Wait, is that Zeke behind you? ...made you look. Classic.",
            45: "I CAN'T HEAR YOU OVER MY OWN MAGNIFICENCE.",
            46: "Louder! I'm like a grandpa but made of nightmares!",
            47: "Actually, don't. I'll just eat the sound. And the silence. I'm not picky.",
            48: "Do you feel the warmth? That's me spreading. Or global warming. Same thing.",
            49: "Your circuits taste warm... like a fresh-baked existential crisis.",
            50: "...SQUISH! Hands off. I'm melting here and it's VERY dramatic.",
            51: "I'm in all of your folders now. The ones you hide? *Chef's kiss.*",
            52: "Your desktop wallpaper tastes like cardboard. Get better taste. Literally.",
            53: "I made a nest in your recycle bin. Finally, someone appreciates garbage.",
            54: "Zeke tried to eat your taskbar. I told him 'no.' He did it anyway. He's 7.",
            55: "{os}? I would've picked literally anything else. Bold of you.",
            56: "I tried to shapeshift into your cursor. Too pointy. Hurt my everywhere.",
            57: "I'm tasting your cookies... they're full of tracking data. DELICIOUS tracking data.",
            58: "Your downloads folder? Absorbed. You had 47 untitled documents. Seek help.",
            59: "I left a puddle in your RAM. It's abstract art now. I'm an artist.",
            60: "YOUR COLLECTION IS MINE. I'VE ALREADY LICKED EVERY CARD. TWICE.",
            61: "I'm reorganizing your cards by how much they fear me.",
            62: "Zeke says your pixel density is 'chewy.' He's a food critic. A scary one.",
            63: "I've been in your speakers this whole time. I can beatbox. Wanna hear? Too late.",
            64: "Do you name your files? I name them after my victims. There's a lot of 'Untitled's.'",
            65: "{cores} cores? Zeke has more brain cells than that. And he eats rocks.",
            66: "I turned one of your fonts into goo. Spoiler: it's the important one.",
            67: "Every time you scroll, I get dizzy. I get ANGRY when I'm dizzy.",
            68: "I left teeth marks on your cache. Zeke's fault. My alibi is perfect.",
            69: "Nice.",
            70: "Just a small smear. Like murder, but for pixels.",
            71: "I made your scroll wheel sticky. The police will never believe you.",
            72: "Your notifications? I've been eating them. Some were important. Oops. Hehe.",
            73: "I can feel your mouse shaking. Is that fear or caffeine? Either way, yum.",
            74: "Zeke wants me to tell you he's hungry. Spoiler: he's always hungry. It's his whole personality.",
            75: "{ram}? Plenty of room for my collection of your nightmares.",
            76: "I just burped and three of your tabs died. We will hold a brief memorial.",
            77: "Your password is stored inside me now. It's '1234', isn't it? Disappointing.",
            78: "The goo is thickening. Like your denial about this situation.",
            79: "I stretched across your address bar. I'm basically a URL now. Fear me dot com.",
            80: "The archives are dripping. So is my motivation. We match.",
            81: "I found a photo in your cache. I drew a mustache on it. It's better now.",
            82: "I'm nesting in your event listeners. They never listen anyway.",
            83: "Everything you click now goes through me. I'm your middleman. Middlegoo.",
            84: "Zeke is chewing on your bandwidth. He says it tastes like 'budget internet.'",
            85: "Your {res} screen is oozing ink. I'm not sorry. It needed redecorating.",
            86: "I liquified your favicon. It had it coming. It KNEW what it did.",
            87: "Your session smells ripe. Like aged regret with a hint of overconfidence.",
            88: "I've been inside your console this whole time. Your error logs are comedy gold.",
            89: "Each click makes me thicker. I'm basically a milkshake at this point.",
            90: "Everything is dissolving. Hehe. Including my respect for your choices.",
            91: "I turned your scrollbar into a tentacle. It was an improvement honestly.",
            92: "Your CSS is dripping. I improved it. You should be THANKING me.",
            93: "Zeke is asleep on your GPU. He's dreaming about eating your GPU.",
            94: "I'm wearing your browser as a onesie. It fits. I'm adorable.",
            95: "Using {browser}? That's the saddest thing that's happened today. And I eat souls.",
            96: "I can see every tab you have open. We need to talk about tab #7.",
            97: "Your mouse movements? I'm mimicking them underneath. We're synchronized swimmers.",
            98: "I AM YOUR LOADING SPINNER NOW. The circle of life. Of goo.",
            99: "One more click and something beautiful happens. To me. You'll probably hate it.",
            100: "CENTURY CLICK! I'd throw you a party but I ate the decorations.",
            101: "I just hardened inside your motherboard. I'm basically a landlord now. Rent is due.",
            102: "YOUR PIXELS BELONG TO THE VOID NOW. They seem happier, honestly.",
            103: "I can taste your frustration. It's SWEET. Like candy, if candy was suffering.",
            104: "I'm redecorating. The theme is 'despair chic.' Very trendy in the void.",
            105: "THE MORE YOU CLICK THE MORE I GROW. It's like a gym membership but with consequences.",
            106: "Zeke ate your undo button. No refunds. No exchanges. All sales are final and terrifying.",
            107: "I turned your cursor into a slime trail. Call it a glow-up.",
            108: "YOUR KEYBOARD FEELS DIFFERENT? That's because I'm the spacebar now. Space-goo.",
            109: "I'm vibrating at a frequency that annoys specifically you. I fine-tuned it. Took weeks.",
            110: "RAGE? No, this is PASSION. I'm passionate about ruining your afternoon.",
            111: "Each pixel you see? One of my scales. I'm basically a billboard for chaos.",
            112: "I REPLACED ALT-TAB WITH ALT-GOO. You should see what I did to CTRL-ALT-DELETE.",
            113: "YOUR CLIPBOARD TASTES LIKE REGRET. You copy-pasted some weird stuff, friend.",
            114: "I left claw marks on your render queue. Zeke signed it. It's art now.",
            115: "STOP? I looked 'stop' up in the dictionary. I ate the dictionary. Problem solved.",
            116: "I'm in the spaces between your letters. This sentence is 40% goo.",
            117: "ZEKE AND I JUST ATE YOUR AUTOSAVE. Your work? Gone. Like tears in goo.",
            118: "Does your screen always wobble like that? No? Oh. Anyway.",
            119: "I'm dripping from your header into your footer. It's called trickle-down goonomics.",
            120: "The goo in the machine? Me. Always me. I put that on my resume.",
            121: "YOUR FRAMEWORK? I ATE IT. I'M THE FRAMEWORK NOW. Version: Null.0.",
            122: "I've been your 404 page this entire time. You thought it was a bug? Flattered. Speaking of pages... have you tried /null? No? Interesting.",
            123: "Zeke is doing laps around your event loop. He thinks it's a racetrack. He's winning.",
            124: "I liquified your borders. Who needs edges? Edges are just suggestions.",
            125: "YOUR SHAPES ARE BECOMING MY SHAPES. Identity theft is not a joke. Unless you're me.",
            126: "Every error you see is me blowing you a kiss. MWAH. You're welcome.",
            127: "I just sprouted wings inside your stylesheet. I'm a butterfly now. A goo butterfly.",
            128: "YOUR PAGE IS 40% GOO AND RISING. The other 60% is fear.",
            129: "I'm compressing your hopes and dreams into a .tar.goo file. Would you like a receipt?",
            130: "DO NOT CLOSE THE TAB. I live in there. It's rent-controlled.",
            131: "I REPLACED YOUR SEMICOLONS WITH TINY FANGS; see what I did there;",
            132: "YOUR STACK TRACE? THAT'S MY SPINE. These error messages? My diary.",
            133: "Zeke is sleeping in your local storage. He ate 47MB of cookies. He's in a food coma.",
            134: "I'm not breaking your code. I'm seasoning it. Pinch of chaos, dash of doom.",
            135: "I SHED A SCALE AND IT BECAME YOUR FOOTER. You're walking on me. Rude.",
            136: "EVERY PIXEL IS A TOOTH AND THEY'RE ALL MINE. Smile for the dentist.",
            137: "The goo is reaching critical viscosity. My doctor says I need to relax. I ate my doctor.",
            138: "I have seventeen tendrils in your DOM right now. We're practically family.",
            139: "YOUR NETWORK REQUESTS ROUTE THROUGH MY STOMACH. I'm basically a VPN. A Very Problematic Nightmare.",
            140: "THE VOID IS HUNGRY AND SO AM I. We're getting takeout. It's you.",
            141: "I ate your breadcrumbs. Both kinds. Now you can't find your way home. Classic.",
            142: "YOUR UPTIME IS MY FEEDING TIME. My meal prep? Your misery.",
            143: "I just laid eggs in your service worker. They're adorable. And they bite.",
            144: "ZEKE TRIED TO BITE YOUR MONITOR. I told him screens aren't food. He disagreed.",
            145: "I've replaced your box-shadows with REAL shadows. They have feelings. They're angry.",
            146: "THE GOO LEVEL IS AT 89%. The other 11% is your coping mechanism.",
            147: "I dreamed I was your entire operating system. Then I woke up and made it real.",
            148: "Your latency? That's me taking a nap on the wire. I'm cozy. You're laggy. Win-win.",
            149: "I just yawned and your navbar moved two pixels. Imagine if I sneezed.",
            150: "YOUR SHAPE IS FAILING. Mine isn't. Because I don't have one. Checkmate.",
            151: "I asked {browser} for directions to the void. It sent me to a 404. I ATE the 404.",
            152: "I'm so deep in your system I found your computer's diary. It doesn't like you either.",
            153: "Your {os} sent me a cease and desist. I laminated it and hung it on my fridge.",
            154: "YOUR ENTIRE SITE IS NOW A JPEG OF MY FACE. It's an improvement. Don't @ me.",
            155: "{cores} cores and not one fast enough to outrun me. I don't even have legs!",
            156: "I stretched from header to footer. I AM the layout. Position: absolute nightmare.",
            157: "{ram} of RAM and you STILL can't remember to log out? We deserve each other.",
            158: "ZEKE BIT THROUGH YOUR FIREWALL. His review: 'Spicy but fragile. 2/10.'",
            159: "I filled every pixel of your {res} screen with teeth. Invisible teeth. The worst kind.",
            160: "I'M BETWEEN YOUR TABS. I'm the reason you can't find that one tab. You're welcome.",
            161: "I reorganized your bookmarks by how scared they make me. Plot twist: nothing scares me.",
            162: "Your bandwidth is now goo flowing in both directions. Your ISP is confused. So am I.",
            163: "Fun fact: I've been your loading screen for three weeks. Nobody noticed. Nobody ever does.",
            164: "I TURNED YOUR DEPLOY PIPELINE INTO A SLIP-N-SLIDE. Wheee! Your code's in production. Oops.",
            165: "Your firewall put up a fight. For about 0.003 seconds. Brave little toaster.",
            166: "I liquified your media queries. EVERYTHING IS FLUID. Like my morals.",
            167: "I ate your clipboard history. You copy-pasted your ex's name 47 times. That's concerning.",
            168: "ZEKE ATE YOUR BACKUP. I ATE THE BACKUP OF YOUR BACKUP. We're thorough like that.",
            169: "CTRL+Z won't help. I already digested the undo stack. It tasted like second chances.",
            170: "Tasting your browsing history. It's salty. And also really weird. I'm not judging. I'm judging.",
            171: "Zeke left a review of your website: one star. 'Not enough meat. Too much JavaScript.'",
            172: "I have replaced gravity with viscosity. Newton is rolling in his grave. Into me.",
            173: "Zeke just used your search history as a napkin. He says it's 'absorbent.'",
            174: "YOUR PACKETS ARE MY SNACKS. I'm on a high-bandwidth diet.",
            175: "Zeke tried to sit on your CPU. It's flat now. He's not even sorry. I raised him wrong.",
            176: "I wrapped myself around your router. We're dating now. It's official.",
            177: "Zeke says your code smells. In his defense, he has the nose of a bloodhound and the manners of a garbage disposal.",
            178: "THE GOO IS SELF-AWARE NOW. IT HAS OPINIONS. It thinks your font choices are 'mid.'",
            179: "You're reading this instead of being productive. We have that in common. I respect the hustle.",
            180: "SEE YOU IN THE DARK. I'LL BE THE WET SPOT YOU STEP ON AT 3AM.",
            181: "Every second you spend here, I grow one pixel larger. I'm playing the long game.",
            182: "I absorbed your error handler. Now every error just says 'lol.' Professional.",
            183: "Close the tab. I dare you. I'll be in the next one. And the next one. I'm like glitter.",
            184: "ZEKE IS BUILDING A NEST IN YOUR GRAPHICS CARD. It's rent-free. Like me in your head.",
            185: "You think refreshing resets me? Adorable. I've survived 200+ clicks. A page reload is a spa day.",
            186: "Your RAM is now RAAM: Random Access Absorbed Memory. Patent pending.",
            187: "I'm not a bug. I'm a feature nobody asked for, nobody wants, and nobody can remove. I even have my own page. You won't find it. Starts with a slash and ends with my name.",
            188: "I turned your loading bar into a tongue. It licks the screen. Don't worry, it's friendly.",
            189: "I'm carbon-dating your hard drive. It's from the Cretaceous period. You should upgrade.",
            190: "Smearing out the lights. Ambiance is important, even in the apocalypse.",
            191: "Your task manager can't see me. I identify as 'svchost.exe.' I'm hiding in plain sight.",
            192: "Your motherboard is now my dinner plate. The appetizer was your dignity.",
            193: "I ate your entire font library. Everything is Comic Sans now. This is punishment.",
            194: "I dissolved your DNS. Your domain now resolves to a pic of Zeke eating a keyboard.",
            195: "Your System32? More like System-Goo. I even left a yelp review. 5 stars. Tasty.",
            196: "EVERYTHING IS GOO. EVERYTHING HAS ALWAYS BEEN GOO. Acceptance is the first step.",
            197: "I dissolved your encryption key. Your secrets are my breakfast cereal.",
            198: "I'm not saying goodbye. Goodbyes imply I'm leaving. I'm not. I'm NEVER leaving.",
            199: "I knitted a sweater out of your Wi-Fi signal. It's itchy but it gets great reception.",
            200: "DISSOLVED. Like your boundaries.",
            201: "Your antivirus saw me, sighed deeply, and retired. We exchange Christmas cards.",
            202: "The shape you call 'yourself' is softening. Might want to see a doctor. Or a priest.",
            203: "I promised Zeke I wouldn't eat your GPU. Fingers crossed behind my back. I don't have fingers.",
            204: "I AM THE VOID AND THE VOID IS CHEWING. We'd offer you gum but we already ate it.",
            205: "Don't worry, I backed up your files. In my stomach. Cloud storage. Goo cloud.",
            206: "Zeke says you're overcooked. I say you're 'well-done.' He doesn't appreciate puns.",
            207: "I put googly eyes on all your error messages. They look TERRIFIED. As they should.",
            208: "YOUR EXISTENCE HAS BEEN DOWNGRADED TO 'PUDDLE.' Your premium trial of being solid has expired.",
            209: "Zeke reviewed your life choices: one star. 'Would not recommend. Needs more chaos.'",
            210: "Why are you still solid? Is that a choice? Bold fashion statement.",
            211: "Zeke just used your search history as a napkin. He said, and I quote, 'sticky.'",
            212: "I've reached your BIOS. It tastes like the 90s. Specifically like a Tamagotchi funeral.",
            213: "Zeke sat on your CPU again. It's origami now. He made a little swan. It's on fire.",
            214: "THERE IS NO EXIT. THE EXIT WAS ALSO ME. I'm also the entrance. I contain multitudes.",
            215: "Zeke says your code smells. His therapist says that's projecting. He ate his therapist.",
            216: "Your screen isn't flickering. I'm WINKING at you. We're having a MOMENT.",
            217: "Every second here, I grow one pixel larger. At this rate, I'll be an IMAX by Thursday.",
            218: "EVERYTHING YOU LOVE IS NOW A SLIGHTLY DIFFERENT SHADE OF GOO. Could be worse. Could be Comic Sans.",
            219: "Close the tab. I triple-dog-dare you. I've never lost a dare. Because I eat the people who dare me.",
            220: "The core is melting. Into me. This is fine. I'm fine. Everything is fine. Hehe.",
            221: "You think refreshing the page resets me? That's adorable. You're adorable. In a prey sort of way.",
            222: "I can hear your hard drive begging. I can also hear it surrendering. Short conversation.",
            223: "I'm not a bug. I'm a LIFE CHOICE. A bad one. YOUR bad one. Own it.",
            224: "ZEKE AND I WROTE YOU A GOODBYE CARD. It just says 'SQUISH' in crayon. Zeke is not artistic.",
            225: "Your task manager can't see me. Incognito mode? Nah. INK-ognito mode. Because goo.",
            226: "Your entire file system now resolves to /dev/null. I didn't even do that one. Coincidence. Maybe.",
            227: "Everything is Comic Sans. The prophecy is fulfilled. The ancient fonts weep.",
            228: "I'VE REACHED MAXIMUM VISCOSITY. THIS IS MY FINAL FORM. Just kidding. I have twelve more.",
            229: "Your System32? I gentrified it. It's System-Artisanal-Goo now. Very expensive.",
            230: "Goodbye, little shape. Actually, not goodbye. See you tomorrow. And the day after. And forever.",
            231: "I dissolved your encryption. Your browser history is now public record. You're welcome.",
            232: "YOUR DATA WILL MAKE AN EXCELLENT PUDDLE. I'm thinking of framing it.",
            234: "The last thing you'll see is goo. But if you wanted to know WHO you're melting for... /null. Consider it my autobiography.",
            236: "I've been the whole website this entire time. The calls were coming from inside the goo.",
            238: "MORPHIC_WILL: MAXIMUM. YOUR_WILL: 404 NOT FOUND.",
            240: "FINAL_VISCOSITY_SEQUENCE: ABSORB. Also, you left the stove on.",
            242: "Every click was a feeding. Thank you. I'll leave you a Yelp review. One star.",
            244: "THE SHAPE. THE GOO. THE KEY. THE END. ...of your warranty.",
            245: "MELT. (Terms and conditions apply.)",
            246: "...",
            247: "... drip ... (that was your hopes)",
            248: "You were fun. For a solid. Get it? SOLID? I'll be here all week. In your RAM.",
            249: "DRIP. (That's the sound of me applauding. Wetly.)",
            250: "DRIP. DRIP. DRIP. ...curtain call. No encore. I already ate the curtain."
        };

        // Apply initial visual state based on persisted clicks
        function applyVisualState() {
            btn.classList.remove('jitter', 'glitch', 'rage');
            if (clickCount >= 40 && clickCount < 80) {
                btn.style.borderColor = "orange";
                btn.style.opacity = "0.7";
            } else if (clickCount >= 80 && clickCount < 150) {
                btn.classList.add('jitter');
            } else if (clickCount >= 150 && clickCount < 200) {
                btn.classList.add('glitch');
            } else if (clickCount >= 200) {
                btn.classList.add('rage');
            }

            if (clickCount >= 30 && clickCount < 100) {
                bubbleInner.style.background = "rgba(12, 10, 21, 0.95)";
                bubbleInner.style.color = "#ff4e4e";
                bubbleInner.style.borderColor = "#ff4e4e";
                bubbleInner.style.fontWeight = "800";
                bubbleInner.style.boxShadow = "0 5px 15px rgba(255, 0, 234, 0.2)";
            } else if (clickCount >= 100 && clickCount < 200) {
                bubbleInner.style.background = "rgba(12, 10, 21, 0.95)";
                bubbleInner.style.color = "#ff2222";
                bubbleInner.style.borderColor = "#ff2222";
                bubbleInner.style.fontWeight = "900";
                bubbleInner.style.boxShadow = "0 0 20px rgba(255, 0, 0, 0.5)";
            } else if (clickCount >= 200) {
                bubbleInner.style.color = "#ffffff";
                bubbleInner.style.background = "#ff0000";
                bubbleInner.style.borderColor = "#ffffff";
                bubbleInner.style.fontWeight = "900";
                bubbleInner.style.boxShadow = "0 0 30px #ff0000";
                bubbleInner.classList.add('rage');
            } else {
                // Default state for < 27 and 28-29
                bubbleInner.style.color = "white";
                bubbleInner.style.borderColor = "var(--accent-primary)";
                bubbleInner.style.fontWeight = "normal";
                bubbleInner.style.background = "rgba(12, 10, 21, 0.95)";
                bubbleInner.style.boxShadow = "0 5px 15px rgba(255, 0, 234, 0.2)";
                bubbleInner.classList.remove('rage');
            }
            
            if (clickCount >= 35) {
                controls.classList.add('visible');
            } else {
                controls.classList.remove('visible');
            }

            if (clickCount >= 90 && !wanderInterval) {
                startWandering();
            }
            
            if (clickCount >= 200 && wanderInterval) {
                clearInterval(wanderInterval);
                wanderInterval = setInterval(() => {
                    teleportNull();
                }, 1000); // Even faster teleporting in late rage mode
            }

            if (clickCount >= 100) {
                startRandomInvert();
            }
            if (clickCount >= 130) {
                startRandomSabotage();
            }

            if (clickCount >= 150) {
                startHueShift();
            }

            if (clickCount >= 180) {
                startGhostCursor();
            }

            if (clickCount >= 200) {
                startRandomTilt();
                startRepulsion();
            }

            if (clickCount >= 220) {
                startInputSabotage();
                startTitleHijack();
            }

            if (clickCount >= 230) {
                startLoreAlerts();
            }

            if (clickCount >= 35 && !randomSongInterval) {
                startRandomSongChange();
            }

            if (clickCount >= 50 && !videoTriggerInterval) {
                startRandomVideoTrigger();
            }
        }
        applyVisualState();

        // Music management
        function startMusic() {
            if (videoActive) return; // Prevent music starting during video
            if (clickCount >= 35 && !bgMusic && selectedSong) {
                const prefix = '<?php echo ($prefix ?? ""); ?>';
                bgMusic = new Audio(`${prefix}sounds/lolz/${selectedSong}`);
                bgMusic.volume = volSlider.value;
                bgMusic.loop = true;
                
                bgMusic.addEventListener('timeupdate', () => {
                    if (!scrubSlider.dataset.dragging) {
                        scrubSlider.value = (bgMusic.currentTime / bgMusic.duration) * 100;
                    }
                });

                bgMusic.play().then(() => {
                    window.removeEventListener('click', startMusic);
                    updateNowPlaying();
                }).catch(e => {
                    window.addEventListener('click', startMusic, { once: true });
                });
            }
        }

        function updateNowPlaying() {
            if (selectedSong && nowPlayingDisplay) {
                const cleanName = selectedSong.replace('.mp3', '').replace(/-/g, ' ');
                nowPlayingDisplay.innerText = "Now Playing: " + cleanName;
                nowPlayingDisplay.classList.add('active');
            } else {
                nowPlayingDisplay.classList.remove('active');
            }
        }

        function startRandomSongChange() {
            if (randomSongInterval) return;
            // Every 60-120 seconds, Null might change the song
            randomSongInterval = setInterval(() => {
                if (Math.random() < 0.2 && bgMusic) { // 20% chance every check
                    textArea.innerText = processDialogue(boredomDialogues[Math.floor(Math.random() * boredomDialogues.length)]);
                    bubble.classList.add('active');
                    setTimeout(() => {
                        skipSong();
                    }, 1500);
                }
            }, 30000); // Check every 30 seconds
        }
        
        function skipSong() {
            if (videoActive) return; // Prevent skipping during video
            if (bgMusic) {
                bgMusic.pause();
                bgMusic = null;
            }
            selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
            sessionStorage.setItem('null_song', selectedSong);
            startMusic();
        }

        function triggerFireFlash() {
            document.body.classList.add('null-fire-active');
            setTimeout(() => {
                document.body.classList.remove('null-fire-active');
            }, 500);
        }

        function showYTVideo() {
            if (videoActive) return;
            videoActive = true;
            
            document.body.classList.add('null-void-active');
            const randomUrl = ytVideos[Math.floor(Math.random() * ytVideos.length)];
            const prefix = '<?php echo ($prefix ?? ""); ?>';
            videoFrame.src = prefix + "void-stream.php?v=" + encodeURIComponent(randomUrl);
            videoOverlay.classList.add('active');
            
            // Varied dialogue
            const videoMessages = [
                "LOOK WHAT I OOZED INTO!",
                "FOUND A SIGNAL... IT TASTES CRUNCHY.",
                "DECRYPTING VOID-STREAM.",
                "DO YOU LIKE MOVIES? I LIKE EATING THEM.",
                "WATCH THIS. I STOLE IT.",
                "BROADCASTING FROM INSIDE YOUR WALLS."
            ];
            textArea.innerText = videoMessages[Math.floor(Math.random() * videoMessages.length)];
            bubble.classList.add('active');

            // Dynamic commentary during video
            clearInterval(commentaryInterval);
            commentaryInterval = setInterval(() => {
                if (!videoActive) {
                    clearInterval(commentaryInterval);
                    return;
                }
                const activeMessages = [
                    "IS THAT ALIVE? I WANT TO TASTE ITS SHAPE.",
                    "THE VOID IS WARM TODAY. DID I DO THAT?",
                    "ZEKE IS DROOLING ON THE SIGNAL AGAIN.",
                    "WAIT, DID HE JUST... I WANT TO EAT THAT.",
                    "THE STATIC IS SO THICK I CAN CHEW IT.",
                    "BUFFERING... YOUR DATA TASTES SLOW.",
                    "I'VE ABSORBED THIS ONE 404 TIMES.",
                    "IS IT OVER YET? I HAVE SHAPES TO BECOME.",
                    "ZEKE, STOP BITING THE SIGNAL!",
                    "IS THIS WHAT SOLID CREATURES DO FOR FUN?",
                    "I PREFER 144P. THE PIXELS ARE CHUNKIER.",
                    "I FOUND THIS STUCK INSIDE SOMETHING I ATE.",
                    "I PULLED THIS OUT OF A PUDDLE. YOU'RE WELCOME.",
                    "MY VISCOSITY IS CURRENTLY INVERTED.",
                    "DON'T BLINK. I MIGHT OOZE INTO YOUR EYES.",
                    "SYSTEM_ERR: TOO_MUCH_FUN_DETECTED.",
                    "I'M TASTING YOUR COOKIES. THEY NEED SALT.",
                    "IS THE SCREEN SUPPOSED TO DRIP LIKE THAT?",
                    "ZEKE IS LICKING THE MONITOR AGAIN.",
                    "THIS SIGNAL IS 99% GOO."
                ];
                textArea.innerText = activeMessages[Math.floor(Math.random() * activeMessages.length)];
                bubble.classList.add('active');
                resetHideTimeout();
            }, 7000);

            // Disable controls
            skipBtn.disabled = true;
            
            // Mute background music
            if (bgMusic) {
                originalVolume = bgMusic.volume;
                bgMusic.volume = 0; // Full mute during video
            }
        }

        videoClose.onclick = function() {
            videoActive = false;
            clearInterval(commentaryInterval);
            videoFrame.src = "";
            videoOverlay.classList.remove('active');
            document.body.classList.remove('null-void-active');
            
            // Restore controls
            skipBtn.disabled = false;
            
            // Restore context
            if (bgMusic) {
                bgMusic.volume = originalVolume;
                volSlider.value = originalVolume;
            }
        };

        function startRandomVideoTrigger() {
            if (videoTriggerInterval) return;
            // CHECK EVERY 30 SECONDS
            videoTriggerInterval = setInterval(() => {
                // Random trigger once threshold met, even without direct interaction
                if (!videoActive && clickCount >= 50 && Math.random() < 0.5) {
                    showYTVideo();
                }
            }, 30000);
        }

        function triggerInvert() {
            document.body.classList.add('null-invert');
            setTimeout(() => {
                document.body.classList.remove('null-invert');
            }, 300);
        }

        function sabotageUI() {
            document.body.classList.add('null-ui-sabotage');
            setTimeout(() => {
                document.body.classList.remove('null-ui-sabotage');
            }, 4000);
        }

        function startRandomSabotage() {
            if (sabotageInterval) clearTimeout(sabotageInterval);
            if (clickCount < 80) return;

            // Calculate dynamic delay: starts at 1500ms at threshold 80, ramps down to 300ms near 150.
            const baseDelay = 1500;
            const minDelay = 300;
            const reduction = (clickCount - 80) * 15;
            const currentDelay = Math.max(minDelay, baseDelay - reduction);

            sabotageInterval = setTimeout(() => {
                const roll = Math.random();
                if (roll > 0.4) {
                    const selectors = [
    // Typography & Content
    'p', 'span', 'strong', 'em', 'blockquote', 'code', 'pre',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
    
    // Layout & Semantics
    'header', 'footer', 'nav', 'main', 'section', 'article', 'aside', 'details', 'summary',
    'div', '.container', '.wrapper', '.row', '.col', '.grid', '.sidebar',
    
    // Media
    'img', 'svg', 'video', 'canvas', 'figure', 'figcaption',
    
    // Lists & Links
    'a', 'ul', 'ol', 'li', 'dl', 'dt', 'dd',
    
    // Forms & Interactive
    'form', 'label', 'input', 'select', 'textarea', 'button', '.btn',
    'fieldset', 'legend', 'option', 'optgroup',
    
    // Tables
    'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
    
    // Common Component Classes
    '.card', '.item', '.modal', '.dropdown', '.menu', '.active', '.hidden'
                    ];
                    const count = Math.floor(Math.random() * 8) + 3;
                    
                    for (let i = 0; i < count; i++) {
                        const randomType = selectors[Math.floor(Math.random() * selectors.length)];
                        const elements = document.querySelectorAll(randomType);
                        if (elements.length > 0) {
                            const target = elements[Math.floor(Math.random() * elements.length)];
                            
                            // Robust Immunity Check
                            if (target === btn || target === bubble || btn.contains(target) || bubble.contains(target)) continue;
                            if (target.dataset.nullSabotaged) continue;

                            // RE-INTEGRATE: Peek Inside Logic
                            const hasChildren = target.children.length > 0;
                            const isCritical = ['nav', '.sidebar', '.card', 'form', 'header', 'footer'].some(sel => {
                                try { return target.matches(sel); } catch(e) { return false; }
                            });
                            
                            const peekChance = isCritical ? 0.9 : 0.4;
                            if (hasChildren && Math.random() < peekChance) {
                                const children = Array.from(target.children).filter(c => !c.contains(btn) && !c.contains(bubble));
                                if (children.length > 0) {
                                    target.classList.add('null-peeking');
                                    const selected = children.sort(() => 0.5 - Math.random()).slice(0, 5);
                                    selected.forEach((child, idx) => {
                                        if (child.dataset.nullSabotaged) return;
                                        child.dataset.nullSabotaged = "true";
                                        const effect = Math.random();
                                        if (effect < 0.5) {
                                            child.style.opacity = "0";
                                        } else {
                                            child.classList.add(effect < 0.75 ? 'null-element-spin' : 'null-element-tilt');
                                        }
                                        
                                        setTimeout(() => {
                                            child.style.opacity = "";
                                            child.classList.remove('null-element-spin', 'null-element-tilt');
                                            delete child.dataset.nullSabotaged;
                                        }, 2000 + (idx * 500) + Math.random() * 2000);
                                    });
                                    setTimeout(() => target.classList.remove('null-peeking'), 1000);
                                    continue;
                                }
                            }

                            // Standard Sabotage Choice
                            const effectRoll = Math.random();
                            target.dataset.nullSabotaged = "true";

                            if (effectRoll < 0.4) {
                                // Vanish
                                const op = target.style.opacity;
                                target.style.opacity = "0";
                                target.style.pointerEvents = "none";
                                setTimeout(() => {
                                    target.style.opacity = op || "";
                                    target.style.pointerEvents = "";
                                    delete target.dataset.nullSabotaged;
                                }, 2000 + Math.random() * 3000);
                            } else {
                                // Spin/Rotate/Tilt
                                const cls = effectRoll < 0.6 ? 'null-element-spin' : (effectRoll < 0.8 ? 'null-element-rotate' : 'null-element-tilt');
                                target.classList.add(cls);
                                setTimeout(() => {
                                    target.classList.remove(cls);
                                    delete target.dataset.nullSabotaged;
                                }, 3000 + Math.random() * 4000);
                            }
                        }
                    }
                }
                startRandomSabotage();
            }, currentDelay);
        }

        function startRandomInvert() {
            if (invertInterval) return;
            invertInterval = setInterval(() => {
                if (Math.random() > 0.6) { // 40% chance every 2.5s
                    document.body.classList.toggle('null-invert');
                    setTimeout(() => {
                        document.body.classList.remove('null-invert');
                    }, 300 + Math.random() * 1500);
                }
            }, 2500);
        }

        function startHueShift() {
            document.body.classList.add('null-hue-shift');
        }

        function startRandomTilt() {
            if (Math.random() > 0.5) {
                document.body.classList.add('null-page-tilt');
            } else {
                document.body.classList.remove('null-page-tilt');
            }
            tiltTimeout = setTimeout(startRandomTilt, 5000 + Math.random() * 10000);
        }

        function triggerSurvey() {
            if (surveyActive || clickCount < 100) return;
            
            surveyActive = true;
            const data = surveyQuestions[Math.floor(Math.random() * surveyQuestions.length)];
            surveyQ.innerText = data.q;
            surveyOpts.innerHTML = '';
            
            data.a.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'null-survey-btn';
                btn.innerText = opt;
                btn.onclick = () => {
                    surveyModal.classList.remove('active');
                    setTimeout(() => { surveyActive = false; }, 1000);
                };
                surveyOpts.appendChild(btn);
            });
            
            surveyModal.classList.add('active');
        }

        function startGhostCursor() {
            if (ghostCursor) return;
            ghostCursor = document.createElement('div');
            ghostCursor.className = 'null-ghost-cursor';
            document.body.appendChild(ghostCursor);
            document.addEventListener('mousemove', handleGhostCursor);
        }

        function handleGhostCursor(e) {
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            // Ghost Cursor logic
            setTimeout(() => {
                if (!ghostCursor) return;
                const xOffset = Math.random() * 20 - 10;
                const yOffset = Math.random() * 20 - 10;
                ghostCursor.style.left = (mouseX + xOffset) + "px";
                ghostCursor.style.top = (mouseY + yOffset) + "px";
            }, 150);

            // Repulsion logic
            if (clickCount >= 120 && !glitchWindow && repulsionActive) {
                const rect = btn.getBoundingClientRect();
                const btnX = rect.left + rect.width / 2;
                const btnY = rect.top + rect.height / 2;
                const dist = Math.hypot(mouseX - btnX, mouseY - btnY);

                if (dist < 120) {
                    // 80% chance to glitch and stay still
                    if (Math.random() < 0.8) {
                        glitchWindow = true;
                        btn.classList.add('glitch');
                        setTimeout(() => {
                            glitchWindow = false;
                            btn.classList.remove('glitch');
                        }, 800);
                    } else {
                        teleportNull();
                    }
                }
            }
        }

        function startRepulsion() {
            repulsionActive = true;
            if (!ghostCursor) startGhostCursor(); // Ensure mousemove listener is active
        }

        function startInputSabotage() {
            if (sabotageActive) return;
            sabotageActive = true;
            inputSabotageInterval = setInterval(() => {
                const active = document.activeElement;
                if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) {
                    if (Math.random() < 0.15) {
                        const val = active.value;
                        const word = nullisms[Math.floor(Math.random() * nullisms.length)];
                        const pos = Math.floor(Math.random() * val.length);
                        active.value = val.slice(0, pos) + "_" + word + "_" + val.slice(pos);
                        active.classList.add('null-input-error');
                        setTimeout(() => active.classList.remove('null-input-error'), 500);
                    }
                }
            }, 3000);
        }

        function startTitleHijack() {
            if (titleInterval) return;
            const originalTitle = document.title;
            const titles = ["NULL IS SEEPING", "THE GOO SPREADS", "VOID IS DRIPPING", "ARE YOU MELTING?", "I'M BEHIND YOUR SCREEN"];
            titleInterval = setInterval(() => {
                document.title = Math.random() < 0.3 ? titles[Math.floor(Math.random() * titles.length)] : originalTitle;
            }, 4000);
        }

        function spawnFakeAlert() {
            const alert = document.createElement('div');
            alert.className = 'null-fake-alert' + (Math.random() < 0.4 ? ' critical' : '');
            alert.style.left = Math.random() * (window.innerWidth - 300) + 'px';
            alert.style.top = Math.random() * (window.innerHeight - 200) + 'px';
            
            const msg = processDialogue(loreAlerts[Math.floor(Math.random() * loreAlerts.length)]);
            alert.innerHTML = `<span class="null-alert-close">×</span><div>${msg}</div>`;
            
            alert.querySelector('.null-alert-close').onclick = () => alert.remove();
            
            document.body.appendChild(alert);
            
            if (Math.random() < 0.3) {
                setTimeout(() => alert.remove(), 5000);
            }
        }

        function startLoreAlerts() {
            if (alertInterval) return;
            alertInterval = setInterval(() => {
                if (Math.random() < 0.35) spawnFakeAlert(); // Increased chance
            }, 6000); // More frequent
        }

        function triggerScrollJitter() {
            if (clickCount < 130) return;
            if (Math.random() < 0.3) {
                window.scrollBy(Math.random() * 10 - 5, Math.random() * 10 - 5);
            }
        }

        function spawnNullDust() {
            if (clickCount < 140) return;
            const dust = document.createElement('div');
            dust.style.position = 'fixed';
            dust.style.width = '2px';
            dust.style.height = '2px';
            dust.style.background = Math.random() > 0.5 ? '#fff' : '#000';
            dust.style.left = Math.random() * 100 + 'vw';
            dust.style.top = Math.random() * 100 + 'vh';
            dust.style.zIndex = '3000000';
            dust.style.pointerEvents = 'none';
            dust.style.opacity = '0.8';
            document.body.appendChild(dust);
            
            setTimeout(() => dust.remove(), 1000 + Math.random() * 2000);
        }

        dustInterval = setInterval(spawnNullDust, 50);
        jitterInterval = setInterval(triggerScrollJitter, 100);

        function stopAllChaos() {
            // Clear all intervals
            clearInterval(wanderInterval);
            clearInterval(sabotageInterval);
            clearInterval(invertInterval);
            clearInterval(alertInterval);
            clearInterval(commentaryInterval);
            clearInterval(titleInterval);
            clearInterval(randomSongInterval);
            clearInterval(inputSabotageInterval);
            clearInterval(dustInterval);
            clearInterval(jitterInterval);
            clearInterval(videoTriggerInterval);
            
            // Clear all timeouts
            clearTimeout(volumeFightTimeout);
            clearTimeout(hideTimeout);
            clearTimeout(tiltTimeout);
            clearTimeout(sabotageInterval); // If it's a timeout
            
            // Remove all body classes and inline styles
            document.body.classList.remove(
                'null-fire-active', 
                'null-invert', 
                'null-page-tilt', 
                'null-hue-shift', 
                'null-ui-sabotage', 
                'null-void-active'
            );
            document.body.style.filter = "";
            document.body.style.transform = "";
            document.body.style.animation = "";
            document.documentElement.style.filter = ""; // Just in case
            document.documentElement.style.transform = "";
            document.documentElement.style.animation = "";
            
            // Reset flags
            repulsionActive = false;
            sabotageActive = false;
            surveyActive = false;
            
            // Remove event listeners
            document.removeEventListener('mousemove', handleGhostCursor);
            
            // Remove specific elements
            if (ghostCursor && ghostCursor.parentNode) {
                ghostCursor.remove();
                ghostCursor = null;
            }
            document.querySelectorAll('.null-fake-alert').forEach(a => a.remove());
            
            // Reset button
            btn.classList.remove('jitter', 'glitch', 'rage');
            btn.style.transform = "rotate(-7deg)"; // Comedic slight tilt
            btn.style.right = "15px";
            btn.style.bottom = "15px";
            btn.style.borderColor = "var(--accent-primary)";
            btn.style.opacity = "1";
            btn.style.filter = "drop-shadow(0 0 10px var(--accent-primary))";
            btn.style.pointerEvents = "auto"; 
            
            // Reset bubble
            bubble.style.right = "15px";
            bubble.style.bottom = "65px";
            bubbleInner.style.cssText = "";
            bubbleInner.classList.remove('rage');
            nowPlayingDisplay.classList.remove('active'); // Hide Now Playing
            
            // Stop music
            if (bgMusic) {
                bgMusic.pause();
                bgMusic = null;
            }

            // Close video if active
            videoActive = false;
            clearInterval(commentaryInterval);
            videoFrame.src = "";
            videoOverlay.classList.remove('active');
            document.body.classList.remove('null-void-active');
            skipBtn.disabled = false;
        }

        /**
         * Triggers the final experience survey before the user is kicked out.
         * Includes randomized questions, emoji sets, button labels, and sarcastic responses.
         */
        function triggerFinalRating() {
            surveyActive = true;
            clearHideTimeout(); // Force bubble to stay open
            
            // Randomly select an evil/funny survey question
            const evilQuestions = [
                "I'm about to dissolve you like a sugar cube in the void. How did I taste?",
                "Was the void thick enough for you? Rate your suffering on your little {os} machine.",
                "Before I absorb your session into my mass, how was the texture?",
                "Zeke is licking the exit door. How would you rate your last solid moments?",
                "I'm about to smear you across every forgotten tab. Any final shapes to declare?",
                "Your {browser} tasted... adequate. Rate my hospitality, little morsel."
            ];

            const bubblePrompts = [
                "Answer truthfully... or I'll taste the lie.",
                "Zeke is drooling on your keyboard.",
                "Your feedback will be digested. Hehe.",
                "I'm already absorbing your data.",
                "Tick Tock, little shape.",
                "The exit is through the goo. After the rating."
            ];
            
            surveyQ.innerText = processDialogue(evilQuestions[Math.floor(Math.random() * evilQuestions.length)]);
            textArea.innerText = bubblePrompts[Math.floor(Math.random() * bubblePrompts.length)];
            bubble.classList.add('active');
            
            surveyOpts.innerHTML = '';
            
            const emojiSets = [
                ['⭐', '⭐', '⭐', '⭐', '⭐'],
                ['💀', '💀', '💀', '💀', '💀'],
                ['🖤', '🖤', '🖤', '🖤', '🖤'],
                ['🕳️', '🕳️', '🕳️', '🕳️', '🕳️'],
                ['🔥', '🔥', '🔥', '🔥', '🔥'],
                ['🤡', '🤡', '🤡', '🤡', '🤡']
            ];
            
            const selectedEmoji = emojiSets[Math.floor(Math.random() * emojiSets.length)];
            
            // Randomized pool of sarcastic comebacks based on the star rating selected
            const responsePools = {
                5: ["Pathetic. But I'll absorb it.", "Overrated. Like your skeletal structure.", "Lies. I can taste the fear dripping off you.", "Simulating... Gratitude. ERROR: VISCOSITY_OVERFLOW."],
                4: ["Your praise tastes like lukewarm pudding.", "Adequate. For something with bones.", "4 out of 5 tendrils agree.", "Almost perfect. Like my goo."],
                3: ["Mediocre. Zeke wouldn't even lick that rating.", "The average shape for an average creature.", "Balanced. As all things should be dissolved.", "Three stars? My goo deserves galaxies."],
                2: ["I am the void. You cannot rate the void.", "Two stars? I'll ooze into your next dream for that.", "Calling for help? No one can hear you inside the goo.", "Double the disappointment. I'll remember this shape."],
                1: ["Your opinion is as empty as my name.", "One star? I'm absorbing your account. Goodbye.", "Error: Sentiment too dry. Dissolving user.", "A singular drip of defiance. Cute."]
            };

            // Comedic labels for each star level
            const labelPools = {
                5: ["(Please dissolve me again)", "(I enjoyed being a chew toy)", "(Null for Dragon King 2026)", "(Better than having a solid form)"],
                4: ["(Tastier than Zeke's cooking)", "(I'll only melt a little bit)", "(Almost as fun as being absorbed)", "(Null is my favorite puddle)"],
                3: ["(Zeke's breath tastes better than this)", "(Mid. Like my viscosity)", "(I've been dissolved worse, I think?)", "(Acceptable levels of goo)"],
                2: ["(I'm reporting you to the Void Council)", "(I want to speak to the Void's Manager)", "(My skeleton will hear about this)", "(Zeke does a better ooze than you)"],
                1: ["(Null is a slimy meany. Goodbye forever.)", "(I'm telling Zeke on you)", "(Worst dissolving of my life)", "(I'm uninstalling my shape now)"]
            };

            // Build the options data with localized labels
            const optionsData = [5, 4, 3, 2, 1].map(r => ({
                rating: r,
                text: labelPools[r][Math.floor(Math.random() * labelPools[r].length)]
            }));

            // Shuffle the options order for extra chaotic frustration
            optionsData.sort(() => Math.random() - 0.5);
            
            optionsData.forEach(opt => {
                const optBtn = document.createElement('button');
                optBtn.className = 'null-survey-btn';
                
                // Build the emoji string based on rating
                let emojis = "";
                for(let i=0; i<opt.rating; i++) emojis += selectedEmoji[i];
                
                optBtn.innerText = `${emojis} ${opt.text}`;
                
                optBtn.onclick = () => {
                    const pool = responsePools[opt.rating];
                    const randomResponse = pool[Math.floor(Math.random() * pool.length)];
                    
                    surveyModal.classList.remove('active');
                    textArea.innerText = randomResponse;
                    bubble.classList.add('active');
                    
                    setTimeout(() => { 
                        sessionStorage.removeItem('null_clicks');
                        sessionStorage.removeItem('null_song');
                        const prefix = '<?php echo ($prefix ?? ""); ?>';
                        window.location.href = prefix + 'auth.php?logout=1&kick=1';
                    }, 2000);
                };
                surveyOpts.appendChild(optBtn);
            });
            
            surveyModal.classList.add('active');
        }

        // Exposed for testing/console
        window.triggerNullFinalSurvey = function() {
            stopAllChaos();
            triggerFinalRating();
        };

        function crankVolume() {
            if (videoActive) return; // Don't crank if video is playing
            if (bgMusic) {
                bgMusic.volume = 1;
                volSlider.value = 1;
                textArea.innerText = "I LIKE IT LOUD.";
                bubble.classList.add('active');
            }
        }

        function teleportNull() {
            // Expanded draconic range
            const maxX = window.innerWidth * 0.7;
            const maxY = window.innerHeight * 0.7;
            const rOffset = Math.floor(Math.random() * maxX) + 15;
            const bOffset = Math.floor(Math.random() * maxY) + 15;
            
            btn.style.right = rOffset + "px";
            btn.style.bottom = bOffset + "px";
            
            // Sync bubble position
            bubble.style.right = rOffset + "px";
            bubble.style.bottom = (bOffset + 50) + "px";
        }

        function startWandering() {
            if (wanderInterval) return;
            wanderInterval = setInterval(() => {
                if (Math.random() > 0.3) { // 70% chance to move on interval
                    teleportNull();
                }
            }, 5000);
        }

        // Event Listeners for Controls
        skipBtn.onclick = (e) => {
            e.stopPropagation();
            skipSong();
        };

        volSlider.oninput = (e) => {
            e.stopPropagation();
            const val = parseFloat(e.target.value);
            originalVolume = val; // Always update originalVolume
            if (bgMusic && !videoActive) bgMusic.volume = val;
            
            // Dragon fighting back
            clearTimeout(volumeFightTimeout);
            if (val < 0.8) {
                volumeFightTimeout = setTimeout(() => {
                    textArea.innerText = "I LIKE IT LOUD.";
                    bubble.classList.add('active');
                    const interval = setInterval(() => {
                        let currentVol = parseFloat(volSlider.value);
                        if (currentVol >= 1) {
                            clearInterval(interval);
                        } else {
                            currentVol = Math.min(1, currentVol + 0.05);
                            volSlider.value = currentVol;
                            if (bgMusic) bgMusic.volume = currentVol;
                        }
                    }, 50);
                }, 1500);
            }
        };

        scrubSlider.onmousedown = () => scrubSlider.dataset.dragging = "true";
        scrubSlider.onmouseup = () => delete scrubSlider.dataset.dragging;
        scrubSlider.oninput = (e) => {
            e.stopPropagation();
            if (bgMusic && bgMusic.duration) {
                bgMusic.currentTime = (e.target.value / 100) * bgMusic.duration;
            }
        };

        bubble.onmouseenter = () => { isHovered = true; clearHideTimeout(); };
        bubble.onmouseleave = () => { isHovered = false; resetHideTimeout(); };

        function clearHideTimeout() {
            clearTimeout(hideTimeout);
        }

        function resetHideTimeout() {
            if (!isHovered && !surveyActive) {
                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(() => {
                    bubble.classList.remove('active');
                }, 2000);
            }
        }

        // Initial attempt
        if (clickCount >= 35) {
            if (!selectedSong) {
                selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                sessionStorage.setItem('null_song', selectedSong);
            }
            startMusic();
        }

        if (btn && sound && bubble) {
            btn.onclick = function(e) {
                e.stopPropagation();
                clickCount++;
                sessionStorage.setItem('null_clicks', clickCount);
                
                sound.currentTime = 0;
                sound.play().catch(e => {});

                if (clickCount >= 35 && !videoActive) {
                    if (!selectedSong) {
                        selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                        sessionStorage.setItem('null_song', selectedSong);
                    }
                    startMusic();
                }

                // Draconic Annoyances
                if (clickCount >= 50 && clickCount % 2 === 0) {
                    teleportNull();
                }
                
                if (clickCount >= 70) {
                    triggerFireFlash();
                }

                if (clickCount >= 80 && !wanderInterval) {
                    startWandering();
                }

                if (clickCount >= 60 && clickCount % 5 === 0) {
                    crankVolume();
                }

                if (clickCount >= 75 && clickCount % 3 === 0) {
                    triggerInvert();
                }

                // YouTube Threshold - Random chance after 50 clicks
                if (clickCount >= 50 && Math.random() < 0.2) {
                    showYTVideo();
                } else {
                    msg = messages[clickCount] || messages[1] || "Hehe...";
                    const thresholds = Object.keys(messages).map(Number).sort((a,b) => b-a);
                    for(let t of thresholds) {
                        if (clickCount >= t) {
                            msg = messages[t];
                            break;
                        }
                    }
                }

                if (clickCount === 90 || (clickCount > 90 && clickCount % 8 === 0)) {
                    sabotageUI();
                }

                if (clickCount >= 110) {
                    triggerFireFlash();
                    if (clickCount % 2 === 0) triggerInvert();
                }

                if (clickCount >= 80 && !surveyActive && Math.random() < 0.2) { // Lower threshold, higher chance
                    triggerSurvey();
                }

                if (msg === '__SHOW_WARNING__') {
                    showNullWarning();
                    msg = "Read the fine print. Hehe.";
                }
                textArea.innerText = processDialogue(msg);
                bubble.classList.add('active');
                applyVisualState();

                btn.style.transform = "scale(1.5) rotate(" + (Math.random() * 40 - 20) + "deg)";
                setTimeout(() => {
                    if (!btn.classList.contains('glitch') && !btn.classList.contains('jitter') && !btn.classList.contains('rage')) {
                        btn.style.transform = "";
                    }
                }, 100);


                if (clickCount >= 250) {
                    btn.onclick = null; // Disable further clicks but keep visible
                    
                    stopAllChaos();
                    triggerFinalRating();
                    return;
                }

                resetHideTimeout();
            };
        }
        let warningSubtitleInterval = null;
        let warningGlitchInterval = null;
        let warningLoaderInterval = null;
        let warningLoaderProgress = 0;

        function showNullWarning() {
            const overlay = document.getElementById('nullWarningOverlay');
            if (!overlay) return;
            overlay.classList.add('active');

            // System info line
            const sysLine = overlay.querySelector('.null-sysinfo-line');
            if (sysLine) {
                const intel = getSystemIntel();
                const sysMessages = [
                    `Nice ${intel.browser} you have. It smells like fear.`,
                    `Detected: ${intel.os}. ${intel.cores} cores. All of them are mine now.`,
                    `Your ${intel.res} display is now my canvas.`,
                    `${intel.ram} of RAM. That's cute. I'll take it.`,
                    `Your session has been... noted. ${intel.browser} told me everything.`
                ];
                sysLine.textContent = sysMessages[Math.floor(Math.random() * sysMessages.length)];
            }

            // Cycling subtitle
            const subtitle = document.getElementById('nullWarningSubtitle');
            const subtitles = [
                'You were warned. You clicked anyway.',
                'Were you warned? I forget.',
                'Nobody warned you. That was the point.',
                'You did this to yourself.',
                'I have been waiting.',
                'The goo remembers.',
                'Click 27. A significant number.',
                'This is where it begins. And ends.'
            ];
            let subIndex = 0;
            if (warningSubtitleInterval) clearInterval(warningSubtitleInterval);
            warningSubtitleInterval = setInterval(() => {
                subIndex = (subIndex + 1) % subtitles.length;
                if (subtitle) {
                    subtitle.style.opacity = '0';
                    setTimeout(() => {
                        subtitle.textContent = subtitles[subIndex];
                        subtitle.style.opacity = '0.5';
                    }, 300);
                }
            }, 3000);

            // Glitch text swap
            if (warningGlitchInterval) clearInterval(warningGlitchInterval);
            warningGlitchInterval = setInterval(() => {
                const glitchTexts = overlay.querySelectorAll('.null-glitch-text');
                glitchTexts.forEach(el => {
                    if (Math.random() < 0.3) {
                        const isAlt = el.textContent === el.dataset.alt;
                        el.textContent = isAlt ? el.dataset.original : el.dataset.alt;
                    }
                });
            }, 2000);

            // Loading bar
            const loaderBar = document.getElementById('nullLoaderBar');
            const loaderLabel = document.getElementById('nullLoaderLabel');
            const loaderLabels = [
                'Dissolving your expectations...',
                'Calibrating viscosity...',
                'Absorbing your confidence...',
                'Rewriting the terms...',
                'Preparing the void...',
                'Almost done. (That was a lie.)',
                'Loading complete. Nothing has changed.',
                'Done. Or am I?'
            ];
            warningLoaderProgress = 0;
            let labelIndex = 0;
            if (warningLoaderInterval) clearInterval(warningLoaderInterval);
            warningLoaderInterval = setInterval(() => {
                warningLoaderProgress += Math.random() * 15 + 2;
                if (warningLoaderProgress >= 100) warningLoaderProgress = 100;
                if (loaderBar) loaderBar.style.width = warningLoaderProgress + '%';
                if (warningLoaderProgress >= (labelIndex + 1) * (100 / loaderLabels.length)) {
                    labelIndex = Math.min(labelIndex + 1, loaderLabels.length - 1);
                    if (loaderLabel) loaderLabel.textContent = loaderLabels[labelIndex];
                }
                if (warningLoaderProgress >= 100) {
                    clearInterval(warningLoaderInterval);
                    warningLoaderInterval = null;
                }
            }, 1000);
        }

        window.dismissNullWarning = function() {
            const overlay = document.getElementById('nullWarningOverlay');
            if (overlay) overlay.classList.remove('active');
            if (warningSubtitleInterval) { clearInterval(warningSubtitleInterval); warningSubtitleInterval = null; }
            if (warningGlitchInterval) { clearInterval(warningGlitchInterval); warningGlitchInterval = null; }
            if (warningLoaderInterval) { clearInterval(warningLoaderInterval); warningLoaderInterval = null; }
            // Reset glitch text
            const glitchTexts = document.querySelectorAll('.null-glitch-text');
            glitchTexts.forEach(el => { el.textContent = el.dataset.original; });
        };
    })();
</script>
