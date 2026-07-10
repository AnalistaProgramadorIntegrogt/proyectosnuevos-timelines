<x-guest-layout>
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #050505;
            position: relative;
            overflow: hidden;
            perspective: 1000px; /* 3D Perspective applied to container */
        }
        /* Minimalist High-Tech Vector Background with Movement */
        .vector-bg-wrapper {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
            background-color: #050505;
        }

        .grid-bg {
            position: absolute;
            inset: -100px;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: panGrid 15s linear infinite;
        }

        @keyframes panGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Glowing Particles Layer for constant ambient movement */
        .particles-layer {
            position: absolute;
            width: 100%;
            height: 200%;
            top: -100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.4) 1px, transparent 1px);
            background-size: 100px 100px;
            background-position: 0 0, 50px 50px;
            animation: driftUp 30s linear infinite;
            opacity: 0.3;
        }

        @keyframes driftUp {
            0% { transform: translateY(0); }
            100% { transform: translateY(50%); }
        }

        .parallax-layer {
            position: absolute;
            inset: -5%;
            width: 110%;
            height: 110%;
            transition: transform 0.1s linear;
        }

        .buildings-container {
            position: absolute;
            bottom: -5%;
            left: -2%;
            height: 110vh;
            aspect-ratio: 6 / 10;
            opacity: 0.9; /* High visibility */
        }

        .crane-container {
            position: absolute;
            bottom: -5%;
            right: -2%;
            height: 110vh;
            aspect-ratio: 4 / 6;
            opacity: 0.8;
        }

        .connections-container {
            position: absolute;
            top: 50%;
            right: max(5vw, 50px);
            transform: translateY(-50%);
            height: 60vh;
            aspect-ratio: 4 / 6;
            opacity: 1;
        }

        .dash-path {
            fill: none;
            stroke: rgba(255, 255, 255, 0.2);
            stroke-width: 1.5;
            stroke-dasharray: 6 6;
            animation: dashAnim 20s linear infinite;
        }

        @keyframes dashAnim {
            to { stroke-dashoffset: -1000; }
        }

        .float-1 { animation: floatAnim 6s ease-in-out infinite; transform-origin: center; }
        .float-2 { animation: floatAnim 6s ease-in-out infinite -2s; transform-origin: center; }
        .float-3 { animation: floatAnim 6s ease-in-out infinite -4s; transform-origin: center; }

        @keyframes floatAnim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 768px) {
            .parallax-layer {
                inset: 0;
                width: 100%;
                height: 100%;
                transform: none !important;
            }
            .buildings-container {
                left: -20%;
                opacity: 0.3;
            }
            .crane-container, .connections-container {
                display: none; /* Simplify mobile view */
            }
            .login-card {
                margin: 1rem;
                padding: 2rem 1.5rem !important; /* Slightly smaller padding on mobile */
                border-radius: 1.5rem !important;
            }
        }
        .login-card {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 0 50px rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 28rem;
            padding: 2.5rem 2.5rem;
            margin: 1rem;
            z-index: 10;
            position: relative;
        }
        .welcome-icon {
            background-color: #111;
            color: white;
            padding: 0.75rem;
            border-radius: 1rem;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .input-group {
            position: relative;
            margin-top: 0.375rem;
        }
        .input-field {
            display: block;
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 2.5rem !important; /* Force padding */
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            color: #111827;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white !important;
            box-shadow: none !important;
        }
        .input-field:focus {
            outline: none !important;
            border-color: #000 !important;
            box-shadow: 0 0 0 1px #000 !important;
        }
        .input-icon-left {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            padding-left: 0.75rem;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: #9ca3af;
        }
        .input-icon-right {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            padding-right: 0.75rem;
            display: flex;
            align-items: center;
            color: #9ca3af;
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .input-icon-right:hover {
            color: #4b5563;
        }
        .login-btn {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: white !important;
            background-color: #0a0a0a !important;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 1.5rem;
        }
        .login-btn:hover {
            background-color: #1a1a1a !important;
        }
        .divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .divider-line {
            border-bottom: 1px solid #e5e7eb;
            width: 25%;
        }
        .divider-text {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0 0.75rem;
        }
        .ms-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            background-color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
        }
        .ms-btn:hover {
            background-color: #f9fafb;
        }
    </style>

    <div class="login-wrapper" x-data="{ mouseX: 0, mouseY: 0 }" @mousemove="mouseX = ($event.clientX / window.innerWidth) - 0.5; mouseY = ($event.clientY / window.innerHeight) - 0.5;">
        
        <!-- New Animated Vector Background -->
        <div class="vector-bg-wrapper">
            <!-- Continuous Animated Grid & Particles -->
            <div class="grid-bg"></div>
            <div class="particles-layer"></div>

            <!-- Buildings Layer (Moves slowest) -->
            <div class="parallax-layer" :style="`transform: translate(${mouseX * 10}px, ${mouseY * 10}px);`">
                <div class="buildings-container">
                    <svg viewBox="0 0 600 1000" width="100%" height="100%" preserveAspectRatio="xMinYMax meet">
                        <!-- Neon Glow Filter -->
                        <defs>
                            <filter id="neon-glow" x="-50%" y="-50%" width="200%" height="200%">
                                <feGaussianBlur stdDeviation="6" result="blur1" />
                                <feGaussianBlur stdDeviation="12" result="blur2" />
                                <feMerge>
                                    <feMergeNode in="blur2" />
                                    <feMergeNode in="blur1" />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                            <!-- Glow for lines -->
                            <filter id="glow-lines" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="3" result="blur" />
                                <feMerge>
                                    <feMergeNode in="blur" />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                        </defs>

                        <!-- Background Skyscraper -->
                        <polygon points="50,1000 50,300 200,350 200,1000" fill="rgba(20,20,20,0.8)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
                        <polygon points="200,350 350,200 350,1000" fill="rgba(10,10,10,0.9)" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                        <polyline points="50,300 200,200 350,200" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                        
                        <!-- Main Glowing Skyscraper -->
                        <polygon points="120,1000 120,400 350,450 350,1000" fill="rgba(15,15,15,0.95)" stroke="rgba(255,255,255,0.9)" stroke-width="3" filter="url(#neon-glow)"/>
                        <polygon points="350,450 500,350 500,1000" fill="rgba(0,0,0,0.8)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <polygon points="120,400 270,300 500,350 350,450" fill="rgba(40,40,40,0.4)" stroke="rgba(255,255,255,0.7)" stroke-width="2"/>
                        
                        <!-- Glowing structural grid lines -->
                        <g stroke="rgba(255,255,255,0.3)" stroke-width="2" filter="url(#glow-lines)">
                            <!-- Horizontals -->
                            <line x1="120" y1="500" x2="350" y2="550"/>
                            <line x1="120" y1="650" x2="350" y2="700"/>
                            <line x1="120" y1="800" x2="350" y2="850"/>
                            <line x1="120" y1="950" x2="350" y2="1000"/>
                            <!-- Verticals -->
                            <line x1="196" y1="416" x2="196" y2="1000"/>
                            <line x1="273" y1="433" x2="273" y2="1000"/>
                        </g>

                        <!-- Pulsing Data Stream -->
                        <path d="M350,450 Q 500,400 600,200" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="2" stroke-dasharray="10 15" filter="url(#glow-lines)">
                            <animate attributeName="stroke-dashoffset" from="100" to="0" dur="2s" repeatCount="indefinite" />
                        </path>
                        <circle cx="350" cy="450" r="5" fill="white" filter="url(#neon-glow)">
                            <animate attributeName="r" values="3;6;3" dur="2s" repeatCount="indefinite"/>
                        </circle>
                    </svg>
                </div>
            </div>

            <!-- Crane Layer (Moves medium) -->
            <div class="parallax-layer" :style="`transform: translate(${mouseX * -15}px, ${mouseY * -15}px);`">
                <div class="crane-container">
                    <svg viewBox="0 0 400 600" width="100%" height="100%" preserveAspectRatio="xMaxYMax meet">
                        <!-- Mast -->
                        <rect x="300" y="100" width="20" height="500" fill="rgba(20,20,20,0.8)" stroke="rgba(255,255,255,0.6)" stroke-width="2" filter="url(#glow-lines)"/>
                        <!-- Cross bracing -->
                        <path d="M300 120 L320 140 M300 140 L320 160 M300 160 L320 180 M300 180 L320 200 M300 200 L320 220 M300 220 L320 240 M300 240 L320 260 M300 260 L320 280 M300 280 L320 300 M300 300 L320 320 M300 320 L320 340 M300 340 L320 360 M300 360 L320 380 M300 380 L320 400 M300 400 L320 420 M300 420 L320 440 M300 440 L320 460 M300 460 L320 480 M300 480 L320 500 M300 500 L320 520 M300 520 L320 540 M300 540 L320 560 M300 560 L320 580 M300 580 L320 600" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
                        <path d="M320 120 L300 140 M320 140 L300 160 M320 160 L300 180 M320 180 L300 200 M320 200 L300 220 M320 220 L300 240 M320 240 L300 260 M320 260 L300 280 M320 280 L300 300 M320 300 L300 320 M320 320 L300 340 M320 340 L300 360 M320 360 L300 380 M320 380 L300 400 M320 400 L300 420 M320 420 L300 440 M320 440 L300 460 M320 460 L300 480 M320 480 L300 500 M320 500 L300 520 M320 520 L300 540 M320 540 L300 560 M320 560 L300 580 M320 580 L300 600" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
                        
                        <!-- Jib -->
                        <rect x="50" y="80" width="320" height="20" fill="rgba(20,20,20,0.8)" stroke="rgba(255,255,255,0.6)" stroke-width="2" filter="url(#glow-lines)"/>
                        <path d="M50 100 L70 80 M70 100 L90 80 M90 100 L110 80 M110 100 L130 80 M130 100 L150 80 M150 100 L170 80 M170 100 L190 80 M190 100 L210 80 M210 100 L230 80 M230 100 L250 80 M250 100 L270 80 M270 100 L290 80 M290 100 L310 80 M310 100 L330 80 M330 100 L350 80 M350 100 L370 80" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>
                        
                        <!-- Top point -->
                        <path d="M300 80 L310 30 L320 80" fill="rgba(10,10,10,0.9)" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
                        <circle cx="310" cy="30" r="5" fill="white" filter="url(#neon-glow)">
                            <animate attributeName="r" values="4;7;4" dur="1.5s" repeatCount="indefinite"/>
                        </circle>
                        <path d="M310 30 L100 80" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                        <path d="M310 30 L370 80" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>

                        <!-- Hook cable -->
                        <line x1="120" y1="100" x2="120" y2="250" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <circle cx="120" cy="250" r="4" fill="white" filter="url(#glow-lines)"/>
                    </svg>
                </div>
            </div>

            <!-- Nodes Layer (Moves fastest) -->
            <div class="parallax-layer" :style="`transform: translate(${mouseX * -25}px, ${mouseY * -25}px);`">
                <div class="connections-container">
                    <svg viewBox="0 0 400 600" width="100%" height="100%" preserveAspectRatio="xMidYMid meet">
                        <!-- Subtle Curved connections -->
                        <path d="M100 150 C 200 150, 150 300, 250 300" class="dash-path"/>
                        <path d="M250 300 C 350 300, 250 500, 150 500" class="dash-path"/>
                        <circle cx="100" cy="150" r="3" fill="rgba(255,255,255,0.8)" />
                        <circle cx="250" cy="300" r="3" fill="rgba(255,255,255,0.8)" />
                        <circle cx="150" cy="500" r="3" fill="rgba(255,255,255,0.8)" />

                        <!-- Minimalist Floating Groups -->
                        <g class="float-1">
                            <rect x="76" y="126" width="48" height="48" rx="12" fill="rgba(10,10,10,0.8)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                            <!-- Clipboard icon -->
                            <g transform="translate(88, 138) scale(1)">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </g>

                        <g class="float-2">
                            <rect x="226" y="276" width="48" height="48" rx="12" fill="rgba(10,10,10,0.8)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                            <!-- Chart icon -->
                            <g transform="translate(238, 288) scale(1)">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </g>

                        <g class="float-3">
                            <rect x="126" y="476" width="48" height="48" rx="12" fill="rgba(10,10,10,0.8)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                            <!-- Hardhat / Helmet icon -->
                            <g transform="translate(138, 488) scale(1)">
                                <path d="M20 14.66V20a2 2 0 01-2 2H4a2 2 0 01-2-2v-5.34A5.368 5.368 0 015.368 9.3h9.264A5.368 5.368 0 0120 14.66z M12 3a4.5 4.5 0 00-4.5 4.5M16.5 7.5A4.5 4.5 0 0012 3" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Logo Header -->
        <div style="margin-bottom: 2rem; z-index: 10; position: relative; text-align: center;">
            <x-authentication-card-logo />
        </div>

        <!-- Glowing Card Container -->
        <div class="login-card" @mousemove.stop>
            
            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 font-semibold text-sm text-green-600">
                    {{ $value }}
                </div>
            @endsession

            <!-- Welcome Header -->
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div class="welcome-icon">
                    <svg style="width: 2rem; height: 2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.2;">Bienvenido</h2>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Inicia sesión para continuar</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Email Input -->
                    <div>
                        <label for="email" style="display: block; font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.375rem;">Email</label>
                        <div class="input-group">
                            <div class="input-icon-left">
                                <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" class="input-field" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Ingresa tu email" />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" style="display: block; font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.375rem;">Password</label>
                        <div class="input-group" x-data="{ show: false }">
                            <div class="input-icon-left">
                                <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" :type="show ? 'text' : 'password'" class="input-field" name="password" required autocomplete="current-password" placeholder="Ingresa tu contraseña" />
                            <button type="button" class="input-icon-right" @click="show = !show">
                                <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    <path x-show="show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" style="display: none;"></path>
                                    <path x-show="show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" style="display: none;"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem;">
                    <label for="remember_me" style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="remember_me" name="remember" style="border-radius: 0.25rem; border-color: #d1d5db; color: #000;" />
                        <span style="margin-left: 0.5rem; font-size: 0.875rem; color: #4b5563; font-weight: 500;">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size: 0.875rem; font-weight: 600; color: #111827; text-decoration: underline;">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="login-btn">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Log in
                </button>
                
                <div class="divider">
                    <span class="divider-line"></span>
                    <span class="divider-text">O INICIA CON</span>
                    <span class="divider-line"></span>
                </div>

                <a href="{{ route('auth.microsoft') }}" class="ms-btn">
                    <svg style="height: 1.25rem; width: 1.25rem;" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0H0v10h10V0z" fill="#f25022"/>
                        <path d="M21 0H11v10h10V0z" fill="#7fba00"/>
                        <path d="M10 11H0v10h10V11z" fill="#00a4ef"/>
                        <path d="M21 11H11v10h10V11z" fill="#ffb900"/>
                    </svg>
                    Microsoft
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>
