<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LuckShop | Admin</title>
    <!-- Favicon -->
    <link rel="icon" href="/images/luckshop-logo.png" type="image/png">
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

        /* Maintain crisp white text on solid rose/amber action buttons in Light Mode */
        html:not(.dark) button.bg-rose-600,
        html:not(.dark) button.bg-rose-500,
        html:not(.dark) button.bg-rose-700,
        html:not(.dark) a.bg-rose-600,
        html:not(.dark) button.bg-amber-600,
        html:not(.dark) button.bg-rose-600 *,
        html:not(.dark) button.bg-rose-500 *,
        html:not(.dark) a.bg-rose-600 * {
            color: #ffffff !important;
        }

        /* Sleek Global Flash Toast Alerts */
        @keyframes luckshopToastIn {
            from { opacity: 0; transform: translate3d(18px, 0, 0) scale(0.96); }
            to { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
        }
        @keyframes luckshopToastProgress {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }
        @keyframes luckshopConfirmPop {
            from { opacity: 0; transform: scale(0.94) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        #admin-flash-toasts {
            position: fixed;
            top: 76px;
            right: 20px;
            z-index: 95;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: min(390px, calc(100vw - 32px));
            pointer-events: none;
        }

        .luckshop-toast {
            pointer-events: auto;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #111827;
            border: 1px solid #1f2937;
            color: #f9fafb;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.55);
            animation: luckshopToastIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            transition: opacity 0.22s ease, transform 0.22s ease;
        }
        .luckshop-toast.is-leaving {
            opacity: 0;
            transform: translate3d(16px, 0, 0) scale(0.97);
        }
        html:not(.dark) .luckshop-toast {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 18px 36px -10px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(15, 23, 42, 0.04) !important;
        }
        .luckshop-toast-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
        }
        .luckshop-toast--success .luckshop-toast-icon {
            background: rgba(16, 185, 129, 0.14);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }
        .luckshop-toast--error .luckshop-toast-icon {
            background: rgba(244, 63, 94, 0.14);
            color: #f43f5e;
            border: 1px solid rgba(244, 63, 94, 0.28);
        }
        .luckshop-toast--warning .luckshop-toast-icon {
            background: rgba(245, 158, 11, 0.14);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.28);
        }
        .luckshop-toast-title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
        }
        .luckshop-toast-msg {
            font-size: 12.5px;
            margin-top: 2px;
            line-height: 1.45;
            color: #cbd5e1;
            word-break: break-word;
        }
        html:not(.dark) .luckshop-toast-msg {
            color: #475569 !important;
        }
        .luckshop-toast-close {
            margin-left: auto;
            color: #64748b;
            background: transparent;
            border: 0;
            padding: 2px 4px;
            border-radius: 6px;
            cursor: pointer;
            line-height: 1;
            font-size: 14px;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .luckshop-toast-close:hover {
            color: #f8fafc;
            background: rgba(148, 163, 184, 0.16);
        }
        html:not(.dark) .luckshop-toast-close:hover {
            color: #0f172a !important;
            background: #f1f5f9 !important;
        }
        .luckshop-toast-bar {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            transform-origin: left center;
            animation: luckshopToastProgress 4.8s linear forwards;
        }
        .luckshop-toast:hover .luckshop-toast-bar {
            animation-play-state: paused;
        }
        .luckshop-toast--success .luckshop-toast-bar { background: linear-gradient(90deg, #10b981, #34d399); }
        .luckshop-toast--error .luckshop-toast-bar { background: linear-gradient(90deg, #e11d48, #fb7185); }
        .luckshop-toast--warning .luckshop-toast-bar { background: linear-gradient(90deg, #d97706, #fbbf24); }

        /* Sleek Global Confirmation Modal */
        #global-confirm-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        #global-confirm-modal.is-open {
            display: flex;
        }
        .luckshop-confirm-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.72);
            backdrop-filter: blur(4px);
        }
        #global-confirm-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            border-radius: 18px;
            background: #111827;
            border: 1px solid #1f2937;
            color: #f8fafc;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65);
            overflow: hidden;
            animation: luckshopConfirmPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        html:not(.dark) #global-confirm-card {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.05) !important;
        }
        .luckshop-confirm-body {
            padding: 24px 24px 20px;
            text-align: center;
        }
        .luckshop-confirm-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 14px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .luckshop-confirm-icon.is-danger {
            background: rgba(244, 63, 94, 0.14);
            color: #f43f5e;
            border: 1px solid rgba(244, 63, 94, 0.28);
        }
        .luckshop-confirm-icon.is-success {
            background: rgba(16, 185, 129, 0.14);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }
        .luckshop-confirm-icon.is-primary {
            background: rgba(var(--theme-color-rgb), 0.14);
            color: var(--theme-color);
            border: 1px solid rgba(var(--theme-color-rgb), 0.3);
        }
        #global-confirm-title {
            font-size: 17px;
            font-weight: 700;
            line-height: 1.35;
        }
        #global-confirm-message {
            margin-top: 8px;
            font-size: 13.5px;
            line-height: 1.55;
            color: #cbd5e1;
        }
        html:not(.dark) #global-confirm-message {
            color: #475569 !important;
        }
        #global-confirm-hint {
            margin-top: 8px;
            font-size: 11.5px;
            color: #94a3b8;
        }
        .luckshop-confirm-footer {
            display: flex;
            gap: 10px;
            padding: 14px 24px 20px;
            background: rgba(15, 23, 42, 0.35);
            border-top: 1px solid #1f2937;
        }
        html:not(.dark) .luckshop-confirm-footer {
            background: #f8fafc !important;
            border-top-color: #e2e8f0 !important;
        }
        .luckshop-confirm-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 11px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .luckshop-confirm-cancel {
            background: #1f2937;
            color: #e2e8f0;
            border: 1px solid #374151;
        }
        .luckshop-confirm-cancel:hover {
            background: #374151;
        }
        html:not(.dark) .luckshop-confirm-cancel {
            background: #ffffff !important;
            color: #334155 !important;
            border-color: #cbd5e1 !important;
        }
        html:not(.dark) .luckshop-confirm-cancel:hover {
            background: #f1f5f9 !important;
        }
        .luckshop-confirm-ok--danger {
            background: #e11d48 !important;
            color: #ffffff !important;
            border: 1px solid #be123c !important;
            box-shadow: 0 6px 16px -4px rgba(225, 29, 72, 0.45);
        }
        .luckshop-confirm-ok--danger:hover { background: #be123c !important; }
        .luckshop-confirm-ok--success {
            background: #059669 !important;
            color: #ffffff !important;
            border: 1px solid #047857 !important;
            box-shadow: 0 6px 16px -4px rgba(5, 150, 105, 0.45);
        }
        .luckshop-confirm-ok--success:hover { background: #047857 !important; }
        .luckshop-confirm-ok--primary {
            background-image: var(--theme-gradient) !important;
            color: #ffffff !important;
            border: 1px solid transparent !important;
        }
    </style>
</head>

<body class="bg-gray-950 text-gray-100 font-sans">
    <!-- Global Sleek Toast Alerts -->
    <div id="admin-flash-toasts" aria-live="polite">
        @if (session('success'))
            <div class="luckshop-toast luckshop-toast--success" data-auto-toast="true" role="status">
                <div class="luckshop-toast-icon"><i class="fas fa-check-circle"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="luckshop-toast-title">ជោគជ័យ (Success)</div>
                    <div class="luckshop-toast-msg">{{ session('success') }}</div>
                </div>
                <button type="button" class="luckshop-toast-close" onclick="dismissAdminToast(this.closest('.luckshop-toast'))" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="luckshop-toast-bar"></div>
            </div>
        @endif

        @if (session('error'))
            <div class="luckshop-toast luckshop-toast--error" data-auto-toast="true" role="alert">
                <div class="luckshop-toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="luckshop-toast-title">មានបញ្ហា (Error)</div>
                    <div class="luckshop-toast-msg">{{ session('error') }}</div>
                </div>
                <button type="button" class="luckshop-toast-close" onclick="dismissAdminToast(this.closest('.luckshop-toast'))" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="luckshop-toast-bar"></div>
            </div>
        @endif

        @if (session('warning'))
            <div class="luckshop-toast luckshop-toast--warning" data-auto-toast="true" role="status">
                <div class="luckshop-toast-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="luckshop-toast-title">ការជូនដំណឹង (Warning)</div>
                    <div class="luckshop-toast-msg">{{ session('warning') }}</div>
                </div>
                <button type="button" class="luckshop-toast-close" onclick="dismissAdminToast(this.closest('.luckshop-toast'))" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="luckshop-toast-bar"></div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="luckshop-toast luckshop-toast--error" data-auto-toast="true" role="alert">
                <div class="luckshop-toast-icon"><i class="fas fa-times-circle"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="luckshop-toast-title">ទិន្នន័យមិនត្រឹមត្រូវ (Validation Error)</div>
                    <ul class="luckshop-toast-msg list-disc pl-4 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="luckshop-toast-close" onclick="dismissAdminToast(this.closest('.luckshop-toast'))" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="luckshop-toast-bar"></div>
            </div>
        @endif
    </div>

    <!-- Global Sleek Confirmation Modal -->
    <div id="global-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="global-confirm-title">
        <div class="luckshop-confirm-backdrop" id="global-confirm-backdrop"></div>
        <div id="global-confirm-card">
            <div class="luckshop-confirm-body">
                <div class="luckshop-confirm-icon is-danger" id="global-confirm-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h3 id="global-confirm-title">បញ្ជាក់ការលុបទិន្នន័យ</h3>
                <p id="global-confirm-message">តើអ្នកពិតជាចង់លុបទិន្នន័យនេះមែនទេ?</p>
                <p id="global-confirm-hint">សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយវិញបានទេ។</p>
            </div>
            <div class="luckshop-confirm-footer">
                <button type="button" id="global-confirm-cancel" class="luckshop-confirm-btn luckshop-confirm-cancel">
                    បោះបង់ (Cancel)
                </button>
                <button type="button" id="global-confirm-ok" class="luckshop-confirm-btn luckshop-confirm-ok--danger">
                    <i class="fas fa-trash-alt text-xs"></i>
                    <span>បាទ/ចាស លុប</span>
                </button>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-expanded bg-sidebar text-sidebar-foreground relative shadow-lg">
            <!-- Sidebar Rail for resizing -->
            <div class="sidebar-rail" id="sidebar-rail"></div>
            
            <!-- Logo and Title -->
            <div class="flex items-center h-16 px-4 border-b border-sidebar-border">
                <div class="flex items-center">
                    <img
                        src="/images/luckshop-logo.png"
                        alt="LuckShop"
                        class="w-8 h-8 rounded-full object-cover border border-amber-400/60 shrink-0 bg-white"
                    >
                    <h1 class="ml-3 text-xl font-bold sidebar-logo-text">LuckShop</h1>
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

                <!-- Order Requests Section -->
                <div class="px-3 mt-6 space-y-1">
                    <div class="sidebar-group-label text-xs font-medium text-sidebar-foreground/70 px-3 mb-2">Order Requests</div>
                    
                    <!-- Customer Order Requests & Payment Verification -->
                    <a href="{{ route('orders.index') }}" class="sidebar-menu-item flex items-center h-10 px-3 rounded-md text-sm font-medium {{ request()->is('orders*') ? 'bg-sidebar-accent text-sidebar-accent-foreground shadow-sm' : 'hover:bg-sidebar-border/50' }} transition-colors">
                        <i class="fas fa-shopping-bag w-5 h-5"></i>
                        <span class="ml-3 sidebar-text">Order Requests & Payments</span>
                        <span id="sidebar-orders-badge" style="background:#ef4444; color:#ffffff;" class="hidden ml-auto px-1.5 py-0.5 text-[10px] font-bold rounded-full leading-none">0</span>
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

                    <!-- Live Order Notifications Bell -->
                    <a href="{{ route('orders.index') }}" id="navbar-orders-bell" class="text-gray-400 hover:text-white relative p-2 rounded-lg hover:bg-gray-900 transition-colors" title="Customer Order Requests">
                        <i class="fas fa-bell text-lg"></i>
                        <span id="navbar-orders-dot" class="hidden absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-gray-950 animate-pulse"></span>
                    </a>
                    
                    <!-- Dark / Light Theme Toggle -->
                    <button id="theme-toggle" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-900 transition-colors" title="Toggle Dark/Light Mode">
                        <i class="fas fa-moon text-lg dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block text-lg text-amber-400"></i>
                    </button>
                </div>
            </header>
            
            <!-- Page Content -->
            <main id="admin-main-content" class="p-6 bg-gray-950 min-h-[calc(100vh-8rem)]">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="border-t border-gray-800/80 py-4 px-6 text-center text-sm text-gray-400 bg-gray-950">
                <p>
                    Developed by <a href="https://web.telegram.org/k/" class="font-medium text-sidebar-accent hover:underline" target="_blank" rel="noopener noreferrer">Youn Somnang</a>
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    &copy; {{ date('Y') }} LuckShop. All rights reserved.
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

        // Global Auto-Search & Auto-Filter for all GET forms (no Filter button clicks needed)
        (function() {
            const mainEl = document.getElementById('admin-main-content');
            if (!mainEl) return;

            let debounceTimer = null;
            let activeController = null;

            function buildFormUrl(form) {
                const action = form.getAttribute('action') || window.location.pathname;
                const url = new URL(action, window.location.origin);
                const formData = new FormData(form);
                formData.forEach((value, key) => {
                    const str = String(value ?? '').trim();
                    if (str !== '') {
                        url.searchParams.set(key, String(value));
                    } else {
                        url.searchParams.delete(key);
                    }
                });
                return url.toString();
            }

            function syncJsonPayloads(doc) {
                const ordersJsonEl = doc.getElementById('orders-map-json');
                if (ordersJsonEl) {
                    try {
                        window.__ORDERS_MAP = JSON.parse(ordersJsonEl.textContent);
                    } catch (_) {}
                }
            }

            async function performLiveFilter(form, triggerInput) {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                const url = buildFormUrl(form);
                const triggerName = triggerInput && triggerInput.name ? triggerInput.name : null;
                const triggerValue = triggerInput && typeof triggerInput.value === 'string' ? triggerInput.value : null;

                window.history.replaceState({}, '', url);

                const liveRegions = mainEl.querySelectorAll('[data-live-region]');
                liveRegions.forEach((el) => {
                    el.style.transition = 'opacity 150ms';
                    el.style.opacity = '0.55';
                });

                try {
                    const res = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: activeController.signal
                    });
                    if (!res.ok) throw new Error('Network error');
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    // If user continued typing while request was in flight, let the newer request update DOM
                    if (triggerName && triggerInput === document.activeElement && triggerInput.value !== triggerValue) {
                        return;
                    }

                    syncJsonPayloads(doc);

                    const incomingRegions = doc.querySelectorAll('[data-live-region]');
                    if (liveRegions.length > 0 && incomingRegions.length === liveRegions.length) {
                        liveRegions.forEach((currentEl, idx) => {
                            currentEl.innerHTML = incomingRegions[idx].innerHTML;
                            currentEl.style.opacity = '1';
                        });
                        return;
                    }

                    const newMain = doc.getElementById('admin-main-content');
                    if (newMain && !newMain.querySelector('canvas')) {
                        const selStart = triggerInput && typeof triggerInput.selectionStart === 'number' ? triggerInput.selectionStart : null;
                        const selEnd = triggerInput && typeof triggerInput.selectionEnd === 'number' ? triggerInput.selectionEnd : null;
                        mainEl.innerHTML = newMain.innerHTML;
                        if (triggerName) {
                            const restored = mainEl.querySelector(`form [name="${CSS.escape(triggerName)}"]`);
                            if (restored) {
                                restored.focus({ preventScroll: true });
                                if (selStart !== null && typeof restored.setSelectionRange === 'function') {
                                    restored.setSelectionRange(selStart, selEnd);
                                }
                            }
                        }
                    } else if (newMain) {
                        window.location.href = url;
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        liveRegions.forEach((el) => { el.style.opacity = '1'; });
                    }
                }
            }

            // 0ms instant client-side row pre-filter while server AJAX syncs paginated DB results
            function instantRowPreFilter(keyword) {
                const q = String(keyword || '').trim().toLowerCase();
                const regions = mainEl.querySelectorAll('[data-live-region]');
                regions.forEach((region) => {
                    const rows = region.querySelectorAll('tbody tr');
                    rows.forEach((row) => {
                        if (row.querySelector('td[colspan]')) return;
                        if (!q) {
                            row.style.display = '';
                        } else {
                            const text = (row.textContent || '').toLowerCase();
                            row.style.display = text.includes(q) ? '' : 'none';
                        }
                    });
                });
            }

            mainEl.addEventListener('input', (e) => {
                const target = e.target;
                const form = target && target.closest ? target.closest('form') : null;
                if (!form) return;
                if ((form.getAttribute('method') || 'GET').toUpperCase() !== 'GET') return;
                if (target.tagName === 'SELECT' || ['date', 'month', 'checkbox', 'radio'].includes(target.type)) return;

                if (target.name === 'search' || target.name === 'product_search') {
                    instantRowPreFilter(target.value);
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => performLiveFilter(form, target), 250);
            });

            // Prefetch sidebar links on hover to warm Vercel serverless function & speed up navigation
            const prefetchedUrls = new Set();
            document.querySelectorAll('#sidebar a[href]').forEach((link) => {
                link.addEventListener('mouseenter', () => {
                    const href = link.getAttribute('href');
                    if (!href || href === '#' || prefetchedUrls.has(href)) return;
                    prefetchedUrls.add(href);
                    const hint = document.createElement('link');
                    hint.rel = 'prefetch';
                    hint.href = href;
                    document.head.appendChild(hint);
                }, { passive: true });
            });

            mainEl.addEventListener('change', (e) => {
                const target = e.target;
                const form = target && target.closest ? target.closest('form') : null;
                if (!form) return;
                if ((form.getAttribute('method') || 'GET').toUpperCase() !== 'GET') return;
                if (target.tagName === 'SELECT' || ['date', 'month', 'checkbox', 'radio'].includes(target.type)) {
                    clearTimeout(debounceTimer);
                    performLiveFilter(form, target);
                }
            });

            mainEl.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || form.tagName !== 'FORM') return;
                if ((form.getAttribute('method') || 'GET').toUpperCase() !== 'GET') return;
                e.preventDefault();
                clearTimeout(debounceTimer);
                performLiveFilter(form, document.activeElement);
            });
        })();

        // Real-Time New Order Watcher (Instant Popup Toast, Sound Chime, Sidebar Badge & Auto-Refresh Orders Table)
        (function() {
            const pulseUrl = @json(route('orders.live-pulse'));
            const ordersUrl = @json(route('orders.index'));

            const toastWrap = document.createElement('div');
            toastWrap.id = 'live-order-toast-container';
            toastWrap.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:70;display:flex;flex-direction:column;gap:10px;max-width:360px;width:calc(100vw - 40px);pointer-events:none;';
            document.body.appendChild(toastWrap);

            function escapeNoticeHtml(val) {
                return String(val ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function playOrderChime() {
                try {
                    const Ctx = window.AudioContext || window.webkitAudioContext;
                    if (!Ctx) return;
                    const ctx = new Ctx();
                    [587.33, 880.0, 1174.66].forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.11, ctx.currentTime + idx * 0.1);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.1 + 0.25);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + idx * 0.1);
                        osc.stop(ctx.currentTime + idx * 0.1 + 0.26);
                    });
                } catch (_) {}
            }

            function updateOrderBadges(pendingCount) {
                const badge = document.getElementById('sidebar-orders-badge');
                const dot = document.getElementById('navbar-orders-dot');
                const count = Number(pendingCount || 0);
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : String(count);
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                if (dot) {
                    if (count > 0) dot.classList.remove('hidden');
                    else dot.classList.add('hidden');
                }
            }

            function showOrderToast(order, isPaidAlert) {
                if (!order) return;
                const card = document.createElement('div');
                card.style.cssText = 'pointer-events:auto;background:#1f2937;color:#f3f4f6;border:1px solid #374151;border-radius:10px;padding:12px 14px;box-shadow:0 10px 25px rgba(0,0,0,0.35);font-family:Inter,sans-serif;transition:all 0.2s ease;';

                const viaText = order.payment_method === 'aba_khqr'
                    ? 'ABA KHQR'
                    : (['khqr', 'bakong_aba'].includes(order.payment_method) ? 'Bakong KHQR' : 'Cash');
                const paidText = order.payment_status === 'paid'
                    ? 'Paid'
                    : (order.payment_status === 'awaiting_approval' ? 'Verify Claim' : 'Not Paid');

                card.innerHTML =
                    '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">' +
                        '<div style="font-weight:600;font-size:13px;color:#ffffff;">' +
                            (isPaidAlert ? 'Payment Received' : 'New Order') + ' #' + escapeNoticeHtml(order.id) +
                        '</div>' +
                        '<button type="button" style="background:none;border:none;color:#9ca3af;font-size:16px;line-height:1;cursor:pointer;">&times;</button>' +
                    '</div>' +
                    '<div style="font-size:12px;color:#d1d5db;margin-top:4px;">' +
                        escapeNoticeHtml(order.customer_name) + ' · $' + escapeNoticeHtml(order.total) +
                    '</div>' +
                    '<div style="font-size:11px;color:#9ca3af;margin-top:2px;">' +
                        viaText + ' · ' + paidText + (order.time ? ' · ' + escapeNoticeHtml(order.time) : '') +
                    '</div>' +
                    '<div style="margin-top:8px;">' +
                        '<button type="button" class="toast-view-btn" style="background:#374151;color:#f9fafb;border:1px solid #4b5563;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:500;cursor:pointer;">View Order</button>' +
                    '</div>';

                const closeBtn = card.querySelector('button');
                closeBtn.onclick = () => card.remove();

                card.querySelector('.toast-view-btn').onclick = () => {
                    if (document.querySelector('[data-live-region="orders-table"]') && typeof window.openOrderDetail === 'function' && window.__ORDERS_MAP?.[order.id]) {
                        window.openOrderDetail(order.id);
                        card.remove();
                    } else {
                        window.location.href = ordersUrl;
                    }
                };

                toastWrap.appendChild(card);
                setTimeout(() => { if (card.parentNode) card.remove(); }, 10000);
            }

            async function refreshOrdersTableSilently() {
                const tableRegion = document.querySelector('[data-live-region="orders-table"]');
                if (!tableRegion) return;
                if (document.activeElement && document.activeElement.name === 'search') return;

                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('with_counts', '1');
                    const res = await fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    const incomingTable = doc.querySelector('[data-live-region="orders-table"]');
                    if (incomingTable) {
                        tableRegion.innerHTML = incomingTable.innerHTML;
                    }
                    const incomingJson = doc.getElementById('orders-map-json');
                    if (incomingJson) {
                        try { window.__ORDERS_MAP = JSON.parse(incomingJson.textContent); } catch (_) {}
                    }
                    const currentTabs = document.getElementById('orders-status-tabs');
                    const incomingTabs = doc.getElementById('orders-status-tabs');
                    if (currentTabs && incomingTabs) {
                        currentTabs.innerHTML = incomingTabs.innerHTML;
                    }
                } catch (_) {}
            }

            let baselineLatestId = Number(sessionStorage.getItem('luckshop_admin_latest_id') || 0);
            let baselineTotalCount = sessionStorage.getItem('luckshop_admin_total_count');
            let baselineUpdatedAt = sessionStorage.getItem('luckshop_admin_updated_at');
            let baselinePaidWaiting = sessionStorage.getItem('luckshop_admin_paid_waiting');
            let isFirstPulse = baselineLatestId === 0 && baselineTotalCount === null;
            let pollingBusy = false;

            async function pollLiveOrders() {
                if (document.hidden || pollingBusy) return;
                pollingBusy = true;
                try {
                    const res = await fetch(pulseUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();

                    updateOrderBadges(data.pending_count);

                    const latestId = Number(data.latest_id || 0);
                    const totalCount = String(data.total_count ?? 0);
                    const updatedAt = String(data.last_updated_at ?? '');
                    const paidWaiting = Number(data.paid_waiting_count ?? 0);

                    if (!isFirstPulse) {
                        const hasNewOrder = latestId > baselineLatestId;
                        const hasOrderChange = totalCount !== baselineTotalCount || updatedAt !== baselineUpdatedAt;
                        const hasNewPaidClaim = baselinePaidWaiting !== null && paidWaiting > Number(baselinePaidWaiting);

                        if (hasNewOrder) {
                            playOrderChime();
                            await refreshOrdersTableSilently();
                            showOrderToast(data.latest_order, false);
                        } else if (hasOrderChange) {
                            if (hasNewPaidClaim) {
                                playOrderChime();
                                showOrderToast(data.latest_order, true);
                            }
                            await refreshOrdersTableSilently();
                        }
                    }

                    baselineLatestId = latestId;
                    baselineTotalCount = totalCount;
                    baselineUpdatedAt = updatedAt;
                    baselinePaidWaiting = String(paidWaiting);
                    isFirstPulse = false;

                    sessionStorage.setItem('luckshop_admin_latest_id', String(latestId));
                    sessionStorage.setItem('luckshop_admin_total_count', totalCount);
                    sessionStorage.setItem('luckshop_admin_updated_at', updatedAt);
                    sessionStorage.setItem('luckshop_admin_paid_waiting', String(paidWaiting));
                } catch (_) {
                } finally {
                    pollingBusy = false;
                }
            }

            setTimeout(pollLiveOrders, 1200);
            setInterval(pollLiveOrders, 5000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) pollLiveOrders();
            });
        })();

        // Global Flash Toast Auto-Dismiss & Programmatic Helper
        function dismissAdminToast(el) {
            if (!el || el.dataset.dismissed === '1') return;
            el.dataset.dismissed = '1';
            el.classList.add('is-leaving');
            setTimeout(() => {
                if (el.parentNode) el.remove();
            }, 240);
        }

        window.showAdminToast = function(message, type = 'success', customTitle = '') {
            const wrap = document.getElementById('admin-flash-toasts');
            if (!wrap) return;

            const config = {
                success: { title: customTitle || 'ជោគជ័យ (Success)', icon: 'fa-check-circle' },
                error:   { title: customTitle || 'មានបញ្ហា (Error)', icon: 'fa-exclamation-circle' },
                warning: { title: customTitle || 'ការជូនដំណឹង (Warning)', icon: 'fa-exclamation-triangle' }
            }[type] || { title: customTitle || 'ជោគជ័យ (Success)', icon: 'fa-check-circle' };

            const toast = document.createElement('div');
            toast.className = `luckshop-toast luckshop-toast--${type}`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

            const iconBox = document.createElement('div');
            iconBox.className = 'luckshop-toast-icon';
            iconBox.innerHTML = `<i class="fas ${config.icon}"></i>`;

            const contentBox = document.createElement('div');
            contentBox.className = 'min-w-0 flex-1';
            const titleEl = document.createElement('div');
            titleEl.className = 'luckshop-toast-title';
            titleEl.textContent = config.title;
            const msgEl = document.createElement('div');
            msgEl.className = 'luckshop-toast-msg';
            msgEl.textContent = String(message || '');
            contentBox.appendChild(titleEl);
            contentBox.appendChild(msgEl);

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'luckshop-toast-close';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.onclick = () => dismissAdminToast(toast);

            const bar = document.createElement('div');
            bar.className = 'luckshop-toast-bar';

            toast.appendChild(iconBox);
            toast.appendChild(contentBox);
            toast.appendChild(closeBtn);
            toast.appendChild(bar);
            wrap.appendChild(toast);

            bindToastAutoDismiss(toast);
        };

        function bindToastAutoDismiss(toast) {
            let timer = null;
            const startTimer = () => {
                clearTimeout(timer);
                timer = setTimeout(() => dismissAdminToast(toast), 4800);
            };
            toast.addEventListener('mouseenter', () => clearTimeout(timer));
            toast.addEventListener('mouseleave', startTimer);
            startTimer();
        }

        document.querySelectorAll('#admin-flash-toasts [data-auto-toast="true"]').forEach(bindToastAutoDismiss);

        // Global Sleek Confirmation Modal Controller (replaces native browser confirm())
        (function() {
            const modal = document.getElementById('global-confirm-modal');
            if (!modal) return;

            const backdrop = document.getElementById('global-confirm-backdrop');
            const iconBox = document.getElementById('global-confirm-icon');
            const titleEl = document.getElementById('global-confirm-title');
            const msgEl = document.getElementById('global-confirm-message');
            const hintEl = document.getElementById('global-confirm-hint');
            const cancelBtn = document.getElementById('global-confirm-cancel');
            const okBtn = document.getElementById('global-confirm-ok');

            let activeConfirmCallback = null;

            function closeConfirmModal() {
                modal.classList.remove('is-open');
                activeConfirmCallback = null;
            }

            window.openAdminConfirm = function({
                title = 'បញ្ជាក់ការលុបទិន្នន័យ',
                message = 'តើអ្នកពិតជាចង់បន្តសកម្មភាពនេះមែនទេ?',
                hint = '',
                confirmText = 'បាទ/ចាស បន្ត',
                variant = 'danger',
                onConfirm = null
            }) {
                const isSuccess = variant === 'success';
                const isPrimary = variant === 'primary';

                iconBox.className = 'luckshop-confirm-icon ' + (isSuccess ? 'is-success' : isPrimary ? 'is-primary' : 'is-danger');
                iconBox.innerHTML = isSuccess
                    ? '<i class="fas fa-check-circle"></i>'
                    : isPrimary
                    ? '<i class="fas fa-question-circle"></i>'
                    : '<i class="fas fa-trash-alt"></i>';

                titleEl.textContent = title;
                msgEl.textContent = message;
                hintEl.textContent = hint || (isSuccess
                    ? 'ការបញ្ជាទិញនឹងត្រូវបានអនុម័ត និងបន្តដំណើរការភ្លាមៗ។'
                    : isPrimary
                    ? 'សូមពិនិត្យមើលព័ត៌មានមុនពេលបន្ត។'
                    : 'សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយវិញបានទេ។');

                okBtn.className = 'luckshop-confirm-btn ' + (isSuccess ? 'luckshop-confirm-ok--success' : isPrimary ? 'luckshop-confirm-ok--primary' : 'luckshop-confirm-ok--danger');
                okBtn.innerHTML = (isSuccess
                    ? '<i class="fas fa-check text-xs"></i>'
                    : '<i class="fas fa-trash-alt text-xs"></i>') + `<span>${escapeConfirmText(confirmText)}</span>`;
                okBtn.disabled = false;

                activeConfirmCallback = onConfirm;
                modal.classList.add('is-open');
                setTimeout(() => cancelBtn.focus(), 20);
            };

            function escapeConfirmText(str) {
                const div = document.createElement('div');
                div.textContent = String(str || '');
                return div.innerHTML;
            }

            okBtn.addEventListener('click', () => {
                const cb = activeConfirmCallback;
                if (typeof cb === 'function') {
                    okBtn.disabled = true;
                    closeConfirmModal();
                    cb();
                } else {
                    closeConfirmModal();
                }
            });

            cancelBtn.addEventListener('click', closeConfirmModal);
            backdrop.addEventListener('click', closeConfirmModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeConfirmModal();
                }
            });

            // Intercept form submissions with [data-confirm] on form or submitter
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;

                if (form.dataset.confirmed === '1') {
                    delete form.dataset.confirmed;
                    return;
                }

                const submitter = e.submitter || null;
                const trigger = (submitter && submitter.hasAttribute('data-confirm'))
                    ? submitter
                    : (form.hasAttribute('data-confirm') ? form : null);

                if (!trigger) return;

                e.preventDefault();
                e.stopImmediatePropagation();

                const message = trigger.getAttribute('data-confirm') || 'តើអ្នកពិតជាចង់បន្តសកម្មភាពនេះមែនទេ?';
                const variant = trigger.getAttribute('data-confirm-variant') || 'danger';
                const title = trigger.getAttribute('data-confirm-title') || (variant === 'success' ? 'បញ្ជាក់ការអនុម័ត' : 'បញ្ជាក់ការលុបទិន្នន័យ');
                const confirmText = trigger.getAttribute('data-confirm-ok') || (variant === 'success' ? 'បាទ/ចាស អនុម័ត' : 'បាទ/ចាស លុប');
                const hint = trigger.getAttribute('data-confirm-hint') || '';

                window.openAdminConfirm({
                    title,
                    message,
                    hint,
                    confirmText,
                    variant,
                    onConfirm: () => {
                        form.dataset.confirmed = '1';
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit(submitter || undefined);
                        } else {
                            form.submit();
                        }
                    }
                });
            }, true);

            // Intercept anchor/link elements with [data-confirm]
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[data-confirm]');
                if (!link) return;
                e.preventDefault();
                const href = link.getAttribute('href');
                const variant = link.getAttribute('data-confirm-variant') || 'danger';
                window.openAdminConfirm({
                    title: link.getAttribute('data-confirm-title') || 'បញ្ជាក់សកម្មភាព',
                    message: link.getAttribute('data-confirm') || 'តើអ្នកពិតជាចង់បន្តមែនទេ?',
                    hint: link.getAttribute('data-confirm-hint') || '',
                    confirmText: link.getAttribute('data-confirm-ok') || 'បន្ត',
                    variant,
                    onConfirm: () => {
                        if (href) window.location.href = href;
                    }
                });
            });
        })();
    </script>
</body>
</html>
