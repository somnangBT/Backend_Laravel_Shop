<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeliciousEats | Admin</title>
    <!-- Favicon -->
    <link rel="icon" href="https://i.ytimg.com/vi/x_9SdeVjfe4/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAHJnuMKW_ny8rT3ZCRZPM8jsPCRQ" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script>
        // Color Themes Definition
        window.APP_THEMES = {
            cyan: {
                id: 'cyan',
                name: 'Ocean Cyan',
                subtitle: 'Modern & Cyber Clean',
                badge: 'Popular',
                primary: '#06b6d4',
                secondary: '#3b82f6',
                accent: '#38bdf8',
                rgb: '6, 182, 212',
                hsl: '189 94% 43%',
                gradient: 'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)',
                ringClass: 'ring-cyan-500',
                borderClass: 'border-cyan-500',
                glow: 'rgba(6, 182, 212, 0.4)'
            },
            purple: {
                id: 'purple',
                name: 'Cyber Purple',
                subtitle: 'Vibrant, Futuristic & Neon',
                badge: 'Creative',
                primary: '#a855f7',
                secondary: '#ec4899',
                accent: '#c084fc',
                rgb: '168, 85, 247',
                hsl: '271 91% 65%',
                gradient: 'linear-gradient(135deg, #a855f7 0%, #ec4899 100%)',
                ringClass: 'ring-purple-500',
                borderClass: 'border-purple-500',
                glow: 'rgba(168, 85, 247, 0.4)'
            },
            emerald: {
                id: 'emerald',
                name: 'Emerald Mint',
                subtitle: 'Natural, Fresh & Growth',
                badge: 'Eco Fresh',
                primary: '#10b981',
                secondary: '#14b8a6',
                accent: '#34d399',
                rgb: '16, 185, 129',
                hsl: '160 84% 39%',
                gradient: 'linear-gradient(135deg, #10b981 0%, #14b8a6 100%)',
                ringClass: 'ring-emerald-500',
                borderClass: 'border-emerald-500',
                glow: 'rgba(16, 185, 129, 0.4)'
            },
            amber: {
                id: 'amber',
                name: 'Sunset Amber',
                subtitle: 'Warmth, Luxury & Energy',
                badge: 'Warm Gold',
                primary: '#f59e0b',
                secondary: '#f97316',
                accent: '#fbbf24',
                rgb: '245, 158, 11',
                hsl: '38 92% 50%',
                gradient: 'linear-gradient(135deg, #f59e0b 0%, #f97316 100%)',
                ringClass: 'ring-amber-500',
                borderClass: 'border-amber-500',
                glow: 'rgba(245, 158, 11, 0.4)'
            },
            rose: {
                id: 'rose',
                name: 'Ruby Rose',
                subtitle: 'Bold, Passionate & Vivid',
                badge: 'High Energy',
                primary: '#f43f5e',
                secondary: '#e11d48',
                accent: '#fb7185',
                rgb: '244, 63, 94',
                hsl: '350 89% 60%',
                gradient: 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
                ringClass: 'ring-rose-500',
                borderClass: 'border-rose-500',
                glow: 'rgba(244, 63, 94, 0.4)'
            },
            indigo: {
                id: 'indigo',
                name: 'Electric Indigo',
                subtitle: 'Royal, Deep & Executive',
                badge: 'Executive',
                primary: '#6366f1',
                secondary: '#3b82f6',
                accent: '#818cf8',
                rgb: '99, 102, 241',
                hsl: '239 84% 67%',
                gradient: 'linear-gradient(135deg, #6366f1 0%, #3b82f6 100%)',
                ringClass: 'ring-indigo-500',
                borderClass: 'border-indigo-500',
                glow: 'rgba(99, 102, 241, 0.4)'
            }
        };

        window.setAdminTheme = function(themeId, shouldBroadcast = true) {
            const theme = window.APP_THEMES[themeId] || window.APP_THEMES.cyan;
            const root = document.documentElement;
            root.style.setProperty('--sidebar-accent', theme.hsl);
            root.style.setProperty('--sidebar-ring', theme.hsl);
            root.style.setProperty('--primary', theme.hsl);
            root.style.setProperty('--theme-color', theme.primary);
            root.style.setProperty('--theme-color-rgb', theme.rgb);
            root.style.setProperty('--theme-gradient', theme.gradient);
            root.style.setProperty('--theme-accent', theme.accent);
            root.setAttribute('data-color-theme', theme.id);
            localStorage.setItem('admin_color_theme', theme.id);

            // Update top navbar pill if available
            const navThemeName = document.getElementById('navbar-current-theme-name');
            const navThemeDot = document.getElementById('navbar-current-theme-dot');
            if (navThemeName) navThemeName.textContent = theme.name.replace('Ocean ', '').replace('Cyber ', '').replace('Emerald ', '').replace('Sunset ', '').replace('Ruby ', '').replace('Electric ', '');
            if (navThemeDot) navThemeDot.style.background = theme.gradient;

            if (shouldBroadcast) {
                window.dispatchEvent(new CustomEvent('adminColorThemeChanged', { 
                    detail: { theme: theme.id, themeData: theme } 
                }));
            }
        };

        // Initialize theme before DOM renders to prevent flicker
        (function() {
            // Get database preference (dark_mode is boolean: 1/0 or true/false)
            const serverDarkMode = @json((bool)($adminSettings->dark_mode ?? true));
            const savedTheme = localStorage.getItem('theme');
            
            // Prefer localStorage if explicitly saved, otherwise fall back to database setting
            const isDark = (savedTheme !== null) ? (savedTheme === 'dark') : serverDarkMode;
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }

            const defaultTheme = @json($adminSettings->color_theme ?? 'cyan');
            const savedColorTheme = localStorage.getItem('admin_color_theme') || defaultTheme;
            window.setAdminTheme(savedColorTheme, false);
        })();

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sidebar: {
                            DEFAULT: 'hsl(var(--sidebar-background))',
                            foreground: 'hsl(var(--sidebar-foreground))',
                            accent: 'hsl(var(--sidebar-accent))',
                            'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
                            border: 'hsl(var(--sidebar-border))',
                            ring: 'hsl(var(--sidebar-ring))',
                        },
                        primary: {
                            DEFAULT: 'hsl(var(--primary))',
                            foreground: 'hsl(var(--primary-foreground))',
                        },
                        border: 'hsl(var(--border))',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <style>
        :root {
            --sidebar-background: 222 47% 11%;
            --sidebar-foreground: 210 40% 98%;
            --sidebar-accent: 189 94% 43%;
            --sidebar-accent-foreground: 222 47% 11%;
            --sidebar-border: 217 33% 17%;
            --sidebar-ring: 189 94% 43%;
            
            --primary: 189 94% 43%;
            --primary-foreground: 222 47% 11%;
            --border: 217 33% 17%;
            
            --sidebar-width: 16rem;
            --sidebar-width-collapsed: 5rem;

            --theme-color: #06b6d4;
            --theme-color-rgb: 6, 182, 212;
            --theme-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --theme-accent: #38bdf8;
        }

        /* ========================================================
           LIGHT MODE COMPREHENSIVE STYLING (Active when NOT .dark)
           ======================================================== */
        html:not(.dark) {
            --sidebar-background: 0 0% 100%;
            --sidebar-foreground: 222 47% 11%;
            --sidebar-border: 214 32% 91%;
            --sidebar-accent-foreground: 0 0% 100%;
            --border: 214 32% 91%;
        }

        /* Light Canvas & Layout */
        html:not(.dark) body {
            background-color: #f8fafc !important;
            color: #1e293b !important;
        }

        html:not(.dark) main {
            background-color: #f8fafc !important;
            color: #1e293b !important;
        }

        /* Sidebar in Light Mode */
        html:not(.dark) #sidebar {
            background-color: #ffffff !important;
            border-right: 1px solid #e2e8f0 !important;
            box-shadow: 2px 0 12px 0 rgba(0, 0, 0, 0.04) !important;
        }

        html:not(.dark) .sidebar-logo-text {
            color: #0f172a !important;
        }

        html:not(.dark) .sidebar-group-label {
            color: #64748b !important;
        }

        html:not(.dark) .sidebar-menu-item {
            color: #334155 !important;
        }

        html:not(.dark) .sidebar-menu-item:hover:not(.bg-sidebar-accent) {
            background-color: #f1f5f9 !important;
            color: var(--theme-color) !important;
        }

        html:not(.dark) .sidebar-menu-item.bg-sidebar-accent {
            background-color: var(--theme-color) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px -2px rgba(var(--theme-color-rgb), 0.35) !important;
        }

        html:not(.dark) .sidebar-profile {
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) .sidebar-profile-name div:first-child {
            color: #0f172a !important;
        }

        html:not(.dark) .sidebar-profile-name div:last-child {
            color: #64748b !important;
        }

        /* Header / Navbar in Light Mode */
        html:not(.dark) header {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
        }

        html:not(.dark) header button:not(#navbar-theme-btn) {
            color: #475569 !important;
        }

        html:not(.dark) header button:not(#navbar-theme-btn):hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        html:not(.dark) #navbar-theme-btn {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html:not(.dark) #navbar-theme-menu {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
        }

        html:not(.dark) #navbar-theme-menu button:hover {
            background-color: #f8fafc !important;
        }

        html:not(.dark) #navbar-theme-menu span {
            color: #475569 !important;
        }

        /* Footer in Light Mode */
        html:not(.dark) footer {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
        }

        /* Universal Cards & Containers */
        html:not(.dark) .bg-gray-950,
        html:not(.dark) .bg-gray-900,
        html:not(.dark) .bg-gray-850,
        html:not(.dark) .bg-gray-800,
        html:not(.dark) [class*="bg-gray-950/"],
        html:not(.dark) [class*="bg-gray-900/"],
        html:not(.dark) [class*="bg-gray-850/"],
        html:not(.dark) [class*="bg-gray-800/"] {
            background-color: #ffffff !important;
        }

        html:not(.dark) .bg-gray-700,
        html:not(.dark) [class*="bg-gray-700/"] {
            background-color: #f1f5f9 !important;
        }

        html:not(.dark) .bg-cyan-950,
        html:not(.dark) [class*="bg-cyan-950/"] {
            background-color: #e0f2fe !important;
            border-color: #bae6fd !important;
        }

        html:not(.dark) .bg-emerald-950,
        html:not(.dark) [class*="bg-emerald-950/"] {
            background-color: #dcfce7 !important;
            border-color: #bbf7d0 !important;
        }

        html:not(.dark) .bg-indigo-950,
        html:not(.dark) [class*="bg-indigo-950/"] {
            background-color: #e0e7ff !important;
            border-color: #c7d2fe !important;
        }

        html:not(.dark) .bg-amber-950,
        html:not(.dark) [class*="bg-amber-950/"] {
            background-color: #fef3c7 !important;
            border-color: #fde68a !important;
        }

        /* Reset dark gradient page wrappers */
        html:not(.dark) .min-h-screen[class*="bg-gradient-to-"],
        html:not(.dark) [class*="from-gray-950"],
        html:not(.dark) [class*="from-gray-900"],
        html:not(.dark) [class*="from-gray-800"] {
            background-image: none !important;
            background-color: #f8fafc !important;
        }

        /* Keep theme gradient elements vibrant */
        html:not(.dark) .theme-btn-gradient,
        html:not(.dark) .theme-bg-gradient {
            background-image: var(--theme-gradient) !important;
        }

        /* Borders */
        html:not(.dark) .border-gray-800,
        html:not(.dark) .border-gray-700,
        html:not(.dark) .border-gray-600,
        html:not(.dark) [class*="border-gray-800/"],
        html:not(.dark) [class*="border-gray-700/"],
        html:not(.dark) [class*="border-gray-600/"] {
            border-color: #e2e8f0 !important;
        }

        /* Typography */
        html:not(.dark) .text-white,
        html:not(.dark) .text-gray-100,
        html:not(.dark) .text-gray-200 {
            color: #0f172a !important;
        }

        html:not(.dark) .text-gray-300,
        html:not(.dark) .text-gray-400 {
            color: #475569 !important;
        }

        html:not(.dark) .text-gray-500 {
            color: #64748b !important;
        }

        html:not(.dark) h1, 
        html:not(.dark) h2, 
        html:not(.dark) h3, 
        html:not(.dark) h4, 
        html:not(.dark) h5, 
        html:not(.dark) h6 {
            color: #0f172a !important;
        }

        /* Maintain crisp white text on solid colored buttons */
        html:not(.dark) .theme-btn-gradient,
        html:not(.dark) button[class*="bg-emerald-"],
        html:not(.dark) button[class*="bg-cyan-"],
        html:not(.dark) button[class*="bg-blue-"],
        html:not(.dark) button[class*="bg-red-"],
        html:not(.dark) button[class*="bg-indigo-"],
        html:not(.dark) button[class*="bg-purple-"],
        html:not(.dark) a[class*="bg-emerald-"],
        html:not(.dark) a[class*="bg-cyan-"],
        html:not(.dark) a[class*="bg-blue-"],
        html:not(.dark) a[class*="bg-red-"],
        html:not(.dark) a[class*="bg-indigo-"],
        html:not(.dark) a[class*="bg-purple-"],
        html:not(.dark) a[class*="bg-green-"],
        html:not(.dark) .btn-primary,
        html:not(.dark) .btn-success,
        html:not(.dark) .btn-danger,
        html:not(.dark) .theme-btn-gradient *,
        html:not(.dark) button[class*="bg-emerald-"] *,
        html:not(.dark) button[class*="bg-cyan-"] *,
        html:not(.dark) a[class*="bg-emerald-"] *,
        html:not(.dark) a[class*="bg-cyan-"] * {
            color: #ffffff !important;
        }

        /* Secondary & Outline Buttons */
        html:not(.dark) a[class*="bg-gray-800"],
        html:not(.dark) button[class*="bg-gray-800"] {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }
        html:not(.dark) a[class*="bg-gray-800"]:hover,
        html:not(.dark) button[class*="bg-gray-800"]:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Form Inputs & Controls */
        html:not(.dark) input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="file"]),
        html:not(.dark) select,
        html:not(.dark) textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) input:focus,
        html:not(.dark) select:focus,
        html:not(.dark) textarea:focus {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: var(--theme-color) !important;
            box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb), 0.15) !important;
        }

        html:not(.dark) input::placeholder,
        html:not(.dark) textarea::placeholder {
            color: #94a3b8 !important;
        }

        /* Tables */
        html:not(.dark) table {
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) thead,
        html:not(.dark) thead tr,
        html:not(.dark) thead th,
        html:not(.dark) th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) tbody,
        html:not(.dark) tbody tr,
        html:not(.dark) td {
            background-color: #ffffff !important;
            border-color: #f1f5f9 !important;
            color: #1e293b !important;
        }

        html:not(.dark) tbody tr:hover,
        html:not(.dark) tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Settings Card specifics */
        html:not(.dark) .theme-card-box {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) .theme-card-option:hover .theme-card-box:not(.is-selected) {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) .theme-card-option.is-selected .theme-card-box {
            background-color: #ffffff !important;
            border-color: var(--card-accent-color) !important;
        }

        html:not(.dark) .settings-nav-item:not(.active) {
            color: #475569 !important;
        }

        html:not(.dark) .settings-nav-item:not(.active):hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        html:not(.dark) label div.bg-gray-800 {
            background-color: #cbd5e1 !important;
        }

        /* Clean shadows in Light Mode */
        html:not(.dark) .shadow-xl,
        html:not(.dark) .shadow-2xl,
        html:not(.dark) .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.04) !important;
        }
        
        .sidebar-expanded {
            width: var(--sidebar-width);
            transition: width 0.3s ease;
        }
        
        .sidebar-collapsed {
            width: var(--sidebar-width-collapsed);
            transition: width 0.3s ease;
        }
        
        .sidebar-collapsed .sidebar-text,
        .sidebar-collapsed .sidebar-group-label,
        .sidebar-collapsed .sidebar-profile-name,
        .sidebar-collapsed .sidebar-logo-text {
            display: none;
        }
        
        .sidebar-collapsed .sidebar-menu-item {
            justify-content: center;
        }
        
        .sidebar-collapsed .sidebar-profile {
            justify-content: center;
        }
        
        .sidebar-rail {
            position: absolute;
            top: 0;
            right: -4px;
            bottom: 0;
            width: 4px;
            cursor: ew-resize;
            background: transparent;
            z-index: 10;
        }
        
        .sidebar-rail:hover::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background-color: hsl(var(--sidebar-border));
        }

        /* Dynamic Theme Classes */
        .theme-btn-gradient {
            background-image: var(--theme-gradient) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px 0 rgba(var(--theme-color-rgb), 0.35) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .theme-btn-gradient:hover {
            filter: brightness(1.12);
            box-shadow: 0 6px 20px 0 rgba(var(--theme-color-rgb), 0.5) !important;
            transform: translateY(-1px);
        }
        .theme-bg-gradient {
            background-image: var(--theme-gradient) !important;
        }
        .theme-bg-color {
            background-color: var(--theme-color) !important;
        }
        .theme-text-color {
            color: var(--theme-color) !important;
        }
        .theme-border-color {
            border-color: var(--theme-color) !important;
        }
        .theme-shadow-glow {
            box-shadow: 0 10px 25px -5px rgba(var(--theme-color-rgb), 0.35) !important;
        }
        .theme-shadow-glow-sm {
            box-shadow: 0 4px 14px 0 rgba(var(--theme-color-rgb), 0.25) !important;
        }
        .theme-ring-glow:focus, .theme-ring-glow.active {
            box-shadow: 0 0 0 2px rgba(var(--theme-color-rgb), 0.6) !important;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>

<body class="bg-gray-950 text-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-expanded bg-sidebar text-sidebar-foreground relative shadow-lg">
            <!-- Sidebar Rail for resizing -->
            <div class="sidebar-rail" id="sidebar-rail"></div>
            
            <!-- Logo and Title -->
            <div class="flex items-center h-16 px-4 border-b border-sidebar-border">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-sidebar-accent transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                    <h1 class="ml-3 text-xl font-bold sidebar-logo-text">DeliciousEats</h1>
                </div>
                <button id="sidebar-toggle" class="ml-auto text-sidebar-foreground hover:text-sidebar-accent transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <div class="py-4 flex flex-col h-[calc(100%-4rem)] overflow-y-auto">
                <!-- Main Navigation -->
                <div class="px-3 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Main</div>
                    
                    <!-- Dashboard -->
                    <a href="/dashboard" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors" aria-label="Dashboard">
                        <i class="fas fa-tachometer-alt w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Dashboard</span>
                    </a>
                    
                    <!-- Products -->
                    <a href="/products" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('products*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors" aria-label="Products">
                        <i class="fas fa-box w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Products</span>
                    </a>
                    
                    <!-- Categories -->
                    <a href="/categories" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('categories*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors" aria-label="Categories">
                        <i class="fas fa-tags w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Categories</span>
                    </a>

                    <!-- Slide -->
                    <a href="/slide-heroes" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('slide*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors" aria-label="Slide">
                        <i class="fas fa-slideshare w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Slide</span>
                    </a>
                </div>

                <!-- Orders & Kitchen Section -->
                <div class="px-3 mt-6 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Orders & Kitchen</div>
                    
                    <!-- Orders Management -->
                    <a href="{{ route('orders.index') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('orders') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-shopping-bag w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Orders</span>
                    </a>

                    <!-- Kitchen Display (KDS) -->
                    <a href="{{ route('orders.kitchen') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('orders/kitchen*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-fire-burner w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Kitchen (KDS)</span>
                    </a>
                </div>

                <!-- Reports Section -->
                <div class="px-3 mt-6 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Reports & Analytics</div>
                    
                    <!-- Sales & Revenue Report -->
                    <a href="{{ route('reports.sales') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('reports/sales*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-chart-pie w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Sales & Revenue</span>
                    </a>

                    <!-- Customer Reports -->
                    <a href="{{ route('reports.customers') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('reports/customers*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-chart-line w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Customer Reports</span>
                    </a>
                </div>
                
                <!-- Management Section -->
                <div class="px-3 mt-6 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Management</div>
                    
                    <!-- User & Staff Roles (Admin Only) -->
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('users*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                            <i class="fas fa-user-shield w-5 h-5"></i>
                            <span class="ml-3 sidebar-text">Staff & Roles</span>
                        </a>
                    @endif

                    <a href="{{ route('clients.index') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('clients*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-handshake w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Clients</span>
                    </a>

                    <a href="{{ route('employees.index') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('employees*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-id-badge w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Employees</span>
                    </a>
                </div>               
                
                <!-- Settings Section -->
                <div class="px-3 mt-6 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Settings</div>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="/settings" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('settings*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                            <i class="fas fa-cog w-5 h-5"></i>
                            <span class="ml-3 sidebar-text">Settings</span>
                        </a>
                    @endif
                    <a href="/profile" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('profile*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-user-circle w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Profile</span>
                    </a>
                </div>
                
                <!-- User Profile and Logout -->
                <div class="mt-auto border-t border-sidebar-border pt-4 px-3">
                    <div class="sidebar-profile flex items-center p-3 mb-2">
                        <div class="h-9 w-9 rounded-full bg-sidebar-accent/20 flex items-center justify-center text-sidebar-accent font-bold transition-colors">
                            {{ auth()->user()->name[0] ?? 'U' }}
                        </div>
                        <div class="ml-3 sidebar-profile-name">
                            <div class="text-sm font-medium flex items-center gap-1.5">
                                <span>{{ auth()->user()->name ?? 'User' }}</span>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded bg-sidebar-border text-sidebar-foreground">
                                    {{ auth()->user()->role ?? 'admin' }}
                                </span>
                            </div>
                            <div class="text-xs text-sidebar-foreground/70">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-menu-item flex items-center h-10 w-full px-3 rounded-md text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                            <i class="fas fa-sign-out-alt w-5 h-5"></i>
                            <span class="ml-3 sidebar-text">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Navigation Bar -->
            <header class="h-16 border-b border-gray-800/80 bg-gray-950/95 backdrop-blur-md flex items-center px-6 text-white shadow-sm z-30">
                <button id="mobile-sidebar-toggle" class="mr-4 md:hidden text-gray-400 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <div class="flex-1"></div>
                
                <!-- Right side navigation items -->
                <div class="flex items-center space-x-3">
                    <!-- Quick Theme Palette Selector -->
                    <div class="relative" id="navbar-theme-dropdown-container">
                        <button id="navbar-theme-btn" type="button" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-gray-800 hover:border-gray-700 bg-gray-900/90 hover:bg-gray-850 text-gray-200 transition-all text-xs font-medium shadow-sm" title="Change Theme Accent">
                            <span id="navbar-current-theme-dot" class="w-3.5 h-3.5 rounded-full shadow-sm theme-bg-gradient transition-all"></span>
                            <span class="hidden sm:inline font-medium" id="navbar-current-theme-name">Cyan</span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                        </button>
                        
                        <div id="navbar-theme-menu" class="hidden absolute right-0 mt-2 w-60 rounded-xl bg-gray-950 border border-gray-800 shadow-2xl p-3 z-50 backdrop-blur-md">
                            <div class="px-1 py-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider flex items-center justify-between border-b border-gray-800 mb-2.5">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-palette theme-text-color"></i> Accent Theme
                                </span>
                                <a href="{{ route('settings.index') }}#appearance" class="text-xs hover:underline theme-text-color font-medium">Settings &rarr;</a>
                            </div>
                            <div class="grid grid-cols-3 gap-2" id="navbar-theme-swatches">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="text-gray-400 hover:text-white relative p-2 rounded-lg hover:bg-gray-900 transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    
                    <!-- Dark / Light Theme Toggle -->
                    <button id="theme-toggle" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-900 transition-colors" title="Toggle Dark/Light Mode">
                        <i class="fas fa-moon text-lg dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block text-lg text-amber-400"></i>
                    </button>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-6 bg-gray-950 min-h-[calc(100vh-8rem)]">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="border-t border-gray-800/80 py-4 px-6 text-center text-sm text-gray-400 bg-gray-950">
                <p>
                    Developed by <a href="https://web.telegram.org/k/" class="font-medium text-sidebar-accent hover:underline" target="_blank" rel="noopener noreferrer">Youn Somnang</a>
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    &copy; 2025 DeliciousEats. All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    <script>
        // Populate and handle the Quick Theme Dropdown in Navbar
        (function() {
            const dropdownBtn = document.getElementById('navbar-theme-btn');
            const dropdownMenu = document.getElementById('navbar-theme-menu');
            const swatchesContainer = document.getElementById('navbar-theme-swatches');

            if (dropdownBtn && dropdownMenu && swatchesContainer) {
                // Populate swatch buttons
                Object.values(window.APP_THEMES).forEach(theme => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'flex flex-col items-center gap-1.5 p-2 rounded-lg hover:bg-gray-900 transition-all text-center group border border-transparent hover:border-gray-800';
                    btn.setAttribute('data-theme-key', theme.id);
                    btn.innerHTML = `
                        <div class="w-6 h-6 rounded-full shadow-sm ring-1 ring-white/10 group-hover:scale-110 transition-transform" style="background: ${theme.gradient};"></div>
                        <span class="text-[11px] font-medium text-gray-300 group-hover:text-white leading-tight">${theme.name.split(' ')[1] || theme.name}</span>
                    `;
                    btn.addEventListener('click', () => {
                        window.setAdminTheme(theme.id);
                        dropdownMenu.classList.add('hidden');
                    });
                    swatchesContainer.appendChild(btn);
                });

                // Toggle dropdown open/close
                dropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });

                // Close on outside click
                document.addEventListener('click', (e) => {
                    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') dropdownMenu.classList.add('hidden');
                });
            }
        })();

        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        
        function toggleSidebar() {
            if (sidebar.classList.contains('sidebar-expanded')) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                sidebarToggle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                `;
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                sidebarToggle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                `;
            }
        }
        
        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('md:block');
            });
        }
        
        // Dark Mode Toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const isCurrentlyDark = document.documentElement.classList.contains('dark');
                const willBeDark = !isCurrentlyDark;
                
                if (willBeDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }

                // Dispatch event so active components on the page (like Settings switch) update immediately
                window.dispatchEvent(new CustomEvent('adminDarkModeChanged', { 
                    detail: { isDark: willBeDark } 
                }));

                // Sync with backend database in background
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) {
                    fetch('{{ route("settings.toggle-theme") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ dark_mode: willBeDark })
                    }).catch(err => console.debug('Theme sync error:', err));
                }
            });
        }
        
        // Sidebar Rail Resize (Optional advanced feature)
        const sidebarRail = document.getElementById('sidebar-rail');
        if (sidebarRail) {
            let isResizing = false;
            sidebarRail.addEventListener('mousedown', (e) => {
                isResizing = true;
                document.addEventListener('mousemove', handleMouseMove);
                document.addEventListener('mouseup', () => {
                    isResizing = false;
                    document.removeEventListener('mousemove', handleMouseMove);
                }, { once: true });
            });
            
            function handleMouseMove(e) {
                if (!isResizing) return;
                const newWidth = e.clientX;
                if (newWidth >= 200 && newWidth <= 400) {
                    sidebar.style.width = `${newWidth}px`;
                    document.documentElement.style.setProperty('--sidebar-width', `${newWidth}px`);
                }
            }
        }
    </script>
</body>
</html>
