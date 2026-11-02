@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-950 text-gray-100 -m-6 p-6 md:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-gray-800/80 pb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-lg theme-shadow-glow-sm">
                        <i class="fas fa-sliders-h text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">System Settings & Appearance</h1>
                        <p class="text-gray-400 text-sm mt-0.5">Customize your admin workspace theme, layout preferences, and notifications</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-900 border border-gray-800 text-gray-300">
                    <span class="w-2 h-2 rounded-full theme-bg-color animate-pulse"></span>
                    Theme Accent: <strong class="capitalize theme-text-color ml-1" id="header-theme-label">{{ $settings->color_theme ?? 'cyan' }}</strong>
                </span>
            </div>
        </div>

        <!-- Notifications Alerts -->
        @if (session('success'))
            <div class="bg-emerald-950/60 border border-emerald-500/40 text-emerald-200 p-4 mb-6 rounded-xl flex items-center justify-between shadow-lg shadow-emerald-950/30 backdrop-blur-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-check-circle text-base"></i>
                    </div>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-950/60 border border-rose-500/40 text-rose-200 p-4 mb-6 rounded-xl shadow-lg backdrop-blur-sm">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-exclamation-triangle text-rose-400 text-lg"></i>
                    <span class="font-semibold text-sm">Please correct the following errors:</span>
                </div>
                <ul class="ml-8 list-disc text-xs space-y-1 text-rose-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Settings Navigation Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Navigation Menu Card -->
                <div class="bg-gray-900/80 rounded-2xl border border-gray-800 shadow-xl overflow-hidden backdrop-blur-sm">
                    <div class="p-4 border-b border-gray-800/80 bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-compass theme-text-color"></i>
                            Navigation Menu
                        </h3>
                    </div>
                    <div class="p-2">
                        <nav class="space-y-1">
                            <a href="#appearance" class="settings-nav-item active flex items-center px-3.5 py-2.5 text-gray-200 hover:bg-gray-800/80 rounded-xl transition-all group font-medium text-sm">
                                <span class="w-7 h-7 rounded-lg bg-gray-800 group-hover:bg-gray-750 flex items-center justify-center mr-3 text-gray-400 group-hover:text-white transition-colors">
                                    <i class="fas fa-palette theme-text-color"></i>
                                </span>
                                Appearance & Themes
                                <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 font-semibold border border-cyan-500/20">Active</span>
                            </a>
                            <a href="#notifications" class="settings-nav-item flex items-center px-3.5 py-2.5 text-gray-300 hover:bg-gray-800/80 rounded-xl transition-all group font-medium text-sm">
                                <span class="w-7 h-7 rounded-lg bg-gray-800 group-hover:bg-gray-750 flex items-center justify-center mr-3 text-gray-400 group-hover:text-white transition-colors">
                                    <i class="fas fa-bell"></i>
                                </span>
                                Notifications
                            </a>
                            <a href="#general" class="settings-nav-item flex items-center px-3.5 py-2.5 text-gray-300 hover:bg-gray-800/80 rounded-xl transition-all group font-medium text-sm">
                                <span class="w-7 h-7 rounded-lg bg-gray-800 group-hover:bg-gray-750 flex items-center justify-center mr-3 text-gray-400 group-hover:text-white transition-colors">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                Security & Privacy
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Account Summary Card -->
                <div class="bg-gray-900/80 rounded-2xl border border-gray-800 shadow-xl overflow-hidden backdrop-blur-sm">
                    <div class="p-4 border-b border-gray-800/80 bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-circle theme-text-color"></i>
                            Administrator Account
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl theme-bg-gradient flex items-center justify-center text-white font-bold text-2xl shadow-lg theme-shadow-glow-sm">
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="text-white font-semibold text-base truncate">{{ auth()->user()->name ?? 'Administrator' }}</h4>
                                <p class="text-gray-400 text-xs truncate mt-0.5">{{ auth()->user()->email ?? 'admin@deliciouseats.com' }}</p>
                                <span class="inline-block mt-2 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Admin Role
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="lg:col-span-8">
                <form action="{{ route('settings.update') }}" method="POST" id="settings-form">
                    @csrf
                    @method('PUT')

                    <!-- Appearance Preferences Section -->
                    <div class="bg-gray-900/90 rounded-2xl border border-gray-800 shadow-2xl overflow-hidden mb-8 backdrop-blur-md" id="appearance">
                        <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gradient-to-r from-gray-900 via-gray-850 to-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-paint-brush text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg md:text-xl font-bold text-white">Appearance & Theme Customization</h2>
                                    <p class="text-gray-400 text-xs mt-0.5">Customize color palettes, dark mode styling, and dashboard density</p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 border border-gray-700 text-gray-300">
                                <i class="fas fa-sparkles theme-text-color"></i> Live UI Preview
                            </span>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Layout & Display Toggles -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Dark Mode Card -->
                                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 hover:border-gray-700 transition-all flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gray-800 flex items-center justify-center text-amber-400">
                                            <i class="fas fa-moon text-base"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-white text-sm font-semibold">Dark Mode</h3>
                                            <p class="text-gray-400 text-xs mt-0.5">High-contrast dark canvas</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="dark_mode" id="settings-dark-mode" class="sr-only peer" {{ old('dark_mode', $settings->dark_mode ?? true) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>

                                <!-- Compact Mode Card -->
                                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 hover:border-gray-700 transition-all flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gray-800 flex items-center justify-center text-sky-400">
                                            <i class="fas fa-compress-arrows-alt text-base"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-white text-sm font-semibold">Compact Layout</h3>
                                            <p class="text-gray-400 text-xs mt-0.5">High data density display</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="compact_mode" id="settings-compact-mode" class="sr-only peer" {{ old('compact_mode', $settings->compact_mode ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Color Theme Selector Section -->
                            <div class="pt-4 border-t border-gray-800">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                    <div>
                                        <h3 class="text-white font-semibold text-base flex items-center gap-2">
                                            <span>Color Themes</span>
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 border border-gray-700 text-gray-300 font-normal">
                                                6 Premium Presets
                                            </span>
                                        </h3>
                                        <p class="text-gray-400 text-xs mt-0.5">Click any theme card below to instantly preview the live changes on your admin interface</p>
                                    </div>
                                    <div class="text-xs text-gray-400 bg-gray-950/80 px-3 py-1.5 rounded-lg border border-gray-800 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full theme-bg-gradient"></span>
                                        <span>Current: <strong class="text-white capitalize" id="current-active-theme-text">{{ $settings->color_theme ?? 'cyan' }}</strong></span>
                                    </div>
                                </div>

                                <!-- Theme Showcase Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="theme-cards-grid">
                                    
                                    <!-- 1. Ocean Cyan -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="cyan">
                                        <input type="radio" name="color_theme" value="cyan" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'cyan' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-cyan-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <!-- Top glow effect -->
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <!-- Card Top: Chips & Badge -->
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-cyan-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-blue-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-sky-300 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                                            Popular
                                                        </span>
                                                    </div>

                                                    <!-- Checkmark Badge -->
                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-cyan-500 text-white flex items-center justify-center shadow-lg shadow-cyan-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <!-- Miniature UI Mockup -->
                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <!-- Mini Sidebar -->
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-cyan-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-cyan-500/20 border-l-2 border-cyan-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <!-- Mini Main Content -->
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-850">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-cyan-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-cyan-400">+38%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-blue-500" style="width: 75%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Theme Card Footer -->
                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-cyan-300 transition-colors">Ocean Cyan</h4>
                                                    <p class="text-gray-400 text-xs">Modern & Cyber Clean</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- 2. Cyber Purple -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="purple">
                                        <input type="radio" name="color_theme" value="purple" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'purple' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-purple-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-purple-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-pink-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-fuchsia-300 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                                            Creative
                                                        </span>
                                                    </div>

                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-purple-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-purple-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-purple-500/20 border-l-2 border-purple-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-855">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-purple-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-purple-400">+52%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-purple-500 to-pink-500" style="width: 82%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-purple-300 transition-colors">Cyber Purple</h4>
                                                    <p class="text-gray-400 text-xs">Vibrant & Futuristic Neon</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- 3. Emerald Mint -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="emerald">
                                        <input type="radio" name="color_theme" value="emerald" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'emerald' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-emerald-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-emerald-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-teal-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-green-300 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                            Eco Fresh
                                                        </span>
                                                    </div>

                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-emerald-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-emerald-500/20 border-l-2 border-emerald-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-850">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-emerald-400">+44%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: 68%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-emerald-300 transition-colors">Emerald Mint</h4>
                                                    <p class="text-gray-400 text-xs">Natural, Fresh & Growth</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- 4. Sunset Amber -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="amber">
                                        <input type="radio" name="color_theme" value="amber" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'amber' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-amber-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-amber-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-orange-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-yellow-300 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                            Warm Gold
                                                        </span>
                                                    </div>

                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-amber-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-amber-500/20 border-l-2 border-amber-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-850">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-amber-400">+61%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-500" style="width: 88%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-amber-300 transition-colors">Sunset Amber</h4>
                                                    <p class="text-gray-400 text-xs">Warmth, Luxury & Energy</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- 5. Ruby Rose -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="rose">
                                        <input type="radio" name="color_theme" value="rose" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'rose' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-rose-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-rose-500 to-red-500 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-rose-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-red-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-pink-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                            High Energy
                                                        </span>
                                                    </div>

                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-rose-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-rose-500/20 border-l-2 border-rose-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-850">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-rose-400">+73%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-red-500" style="width: 90%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-rose-300 transition-colors">Ruby Rose</h4>
                                                    <p class="text-gray-400 text-xs">Bold, Passionate & Vivid</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-rose-500 to-red-500 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- 6. Electric Indigo -->
                                    <label class="theme-card-option relative block cursor-pointer group" data-theme-id="indigo">
                                        <input type="radio" name="color_theme" value="indigo" class="sr-only theme-radio-input" {{ old('color_theme', $settings->color_theme ?? 'cyan') == 'indigo' ? 'checked' : '' }}>
                                        <div class="theme-card-box h-full rounded-2xl bg-gray-950/60 border-2 border-gray-800 p-4 transition-all duration-200 group-hover:border-indigo-500/60 group-hover:bg-gray-900/80 relative overflow-hidden flex flex-col justify-between">
                                            
                                            <div class="absolute -top-12 -right-12 w-28 h-28 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full blur-2xl opacity-10 group-hover:opacity-30 transition-opacity pointer-events-none"></div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3 relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center -space-x-1.5">
                                                            <span class="w-5 h-5 rounded-full bg-indigo-400 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-blue-500 ring-2 ring-gray-900 shadow-sm"></span>
                                                            <span class="w-5 h-5 rounded-full bg-indigo-300 ring-2 ring-gray-900 shadow-sm"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                            Executive
                                                        </span>
                                                    </div>

                                                    <div class="theme-check-badge w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/50 transform scale-0 transition-all duration-200">
                                                        <i class="fas fa-check text-xs"></i>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl bg-gray-900/90 border border-gray-800 p-2.5 mb-3 relative overflow-hidden shadow-inner">
                                                    <div class="flex gap-2">
                                                        <div class="w-1/3 rounded-lg bg-gray-950 p-1.5 space-y-1.5 border border-gray-850">
                                                            <div class="w-full h-1.5 rounded-full bg-indigo-500/80"></div>
                                                            <div class="w-full h-3 rounded-md bg-indigo-500/20 border-l-2 border-indigo-400 flex items-center px-1">
                                                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                                            </div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                            <div class="w-full h-2 rounded-md bg-gray-800"></div>
                                                        </div>
                                                        <div class="w-2/3 space-y-1.5">
                                                            <div class="flex justify-between items-center bg-gray-950 px-2 py-1 rounded-md border border-gray-850">
                                                                <div class="w-10 h-1.5 rounded-full bg-gray-700"></div>
                                                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-400"></div>
                                                            </div>
                                                            <div class="bg-gray-950 p-1.5 rounded-lg border border-gray-850">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <div class="w-8 h-1.5 rounded-full bg-gray-700"></div>
                                                                    <span class="text-[8px] font-bold text-indigo-400">+67%</span>
                                                                </div>
                                                                <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                                                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-blue-600" style="width: 85%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-1">
                                                <div>
                                                    <h4 class="text-white font-semibold text-sm group-hover:text-indigo-300 transition-colors">Electric Indigo</h4>
                                                    <p class="text-gray-400 text-xs">Royal, Deep & Sophisticated</p>
                                                </div>
                                                <div class="w-7 h-7 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 shadow-sm flex items-center justify-center text-white">
                                                    <i class="fas fa-palette text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Preferences Section -->
                    <div class="bg-gray-900/90 rounded-2xl border border-gray-800 shadow-2xl overflow-hidden mb-8 backdrop-blur-md" id="notifications">
                        <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gradient-to-r from-gray-900 via-gray-850 to-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl theme-bg-gradient flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-bell text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg md:text-xl font-bold text-white">Notification Channels</h2>
                                    <p class="text-gray-400 text-xs mt-0.5">Control where and how you receive system alerts and order updates</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Email -->
                            <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 flex items-center justify-between hover:border-gray-700 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-blue-400">
                                        <i class="fas fa-envelope text-base"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-white text-sm font-semibold">Email Notifications</h3>
                                        <p class="text-gray-400 text-xs mt-0.5">Receive summary digest and critical alerts via email</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notifications" class="sr-only peer" {{ old('email_notifications', $settings->email_notifications ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                            <!-- SMS -->
                            <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 flex items-center justify-between hover:border-gray-700 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-emerald-400">
                                        <i class="fas fa-sms text-base"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-white text-sm font-semibold">SMS Alerts</h3>
                                        <p class="text-gray-400 text-xs mt-0.5">Instant SMS for urgent order statuses</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ old('sms_notifications', $settings->sms_notifications ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                            <!-- Push -->
                            <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 flex items-center justify-between hover:border-gray-700 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-purple-400">
                                        <i class="fas fa-broadcast-tower text-base"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-white text-sm font-semibold">In-Browser Push Alerts</h3>
                                        <p class="text-gray-400 text-xs mt-0.5">Real-time desktop alerts when new orders arrive</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="push_notifications" class="sr-only peer" {{ old('push_notifications', $settings->push_notifications ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions Sticky Bar -->
                    <div class="sticky bottom-4 z-20 flex justify-end">
                        <div class="bg-gray-900/90 p-2.5 rounded-2xl border border-gray-700/80 shadow-2xl backdrop-blur-md flex items-center gap-3">
                            <span class="text-xs text-gray-400 hidden sm:inline pl-2">Save changes to keep preferences permanent</span>
                            <button type="submit" id="save-settings-btn" class="theme-btn-gradient font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 flex items-center gap-2 text-sm shadow-lg">
                                <i class="fas fa-save text-sm"></i>
                                <span>Save Settings</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styling for active card states */
.theme-card-option.is-selected .theme-card-box {
    border-color: var(--card-accent-color, #06b6d4) !important;
    background-color: rgba(17, 24, 39, 0.95) !important;
    box-shadow: 0 10px 25px -5px var(--card-accent-glow, rgba(6, 182, 212, 0.35)) !important;
}

.theme-card-option.is-selected .theme-check-badge {
    transform: scale(1) !important;
}

.theme-card-option:hover .theme-card-box {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeCards = document.querySelectorAll('.theme-card-option');
    const headerThemeLabel = document.getElementById('header-theme-label');
    const currentActiveThemeText = document.getElementById('current-active-theme-text');

    // Theme color mappings for card styling
    const themeVisualStyles = {
        cyan: { border: '#06b6d4', glow: 'rgba(6, 182, 212, 0.35)' },
        purple: { border: '#a855f7', glow: 'rgba(168, 85, 247, 0.35)' },
        emerald: { border: '#10b981', glow: 'rgba(16, 185, 129, 0.35)' },
        amber: { border: '#f59e0b', glow: 'rgba(245, 158, 11, 0.35)' },
        rose: { border: '#f43f5e', glow: 'rgba(244, 63, 94, 0.35)' },
        indigo: { border: '#6366f1', glow: 'rgba(99, 102, 241, 0.35)' }
    };

    function updateActiveCardVisuals(themeId) {
        themeCards.forEach(card => {
            const cardTheme = card.getAttribute('data-theme-id');
            const radio = card.querySelector('.theme-radio-input');
            const style = themeVisualStyles[cardTheme] || themeVisualStyles.cyan;

            if (cardTheme === themeId) {
                card.classList.add('is-selected');
                card.style.setProperty('--card-accent-color', style.border);
                card.style.setProperty('--card-accent-glow', style.glow);
                if (radio) radio.checked = true;
            } else {
                card.classList.remove('is-selected');
                card.style.removeProperty('--card-accent-color');
                card.style.removeProperty('--card-accent-glow');
                if (radio) radio.checked = false;
            }
        });

        if (headerThemeLabel) headerThemeLabel.textContent = themeId;
        if (currentActiveThemeText) currentActiveThemeText.textContent = themeId;
    }

    // Attach click listeners to cards
    themeCards.forEach(card => {
        card.addEventListener('click', function(e) {
            const themeId = this.getAttribute('data-theme-id');
            updateActiveCardVisuals(themeId);
            if (window.setAdminTheme) {
                window.setAdminTheme(themeId);
            }
        });
    });

    // Listen for theme changes from navbar dropdown
    window.addEventListener('adminColorThemeChanged', function(e) {
        if (e.detail && e.detail.theme) {
            updateActiveCardVisuals(e.detail.theme);
        }
    });

    // Initialize initial active card based on current localStorage or checked radio
    const activeRadio = document.querySelector('.theme-radio-input:checked');
    const initialTheme = localStorage.getItem('admin_color_theme') || (activeRadio ? activeRadio.value : 'cyan');
    updateActiveCardVisuals(initialTheme);

    // Smooth navigation links
    document.querySelectorAll('.settings-nav-item').forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                e.preventDefault();
                document.querySelectorAll('.settings-nav-item').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Sync Dark Mode Switch in Settings with Global Dark Mode
    const darkModeCheckbox = document.getElementById('settings-dark-mode');
    if (darkModeCheckbox) {
        // Init state matching current document class
        darkModeCheckbox.checked = document.documentElement.classList.contains('dark');
        
        darkModeCheckbox.addEventListener('change', function() {
            const isDark = this.checked;
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }

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
                    body: JSON.stringify({ dark_mode: isDark })
                }).catch(err => console.debug('Theme sync error:', err));
            }
        });

        // Listen for navbar theme toggle event to keep switch in sync
        window.addEventListener('adminDarkModeChanged', function(e) {
            if (e.detail && typeof e.detail.isDark !== 'undefined') {
                darkModeCheckbox.checked = e.detail.isDark;
            }
        });
    }

    // Ensure form submit keeps localStorage in sync with selected dark_mode checkbox
    const settingsForm = document.getElementById('settings-form');
    if (settingsForm && darkModeCheckbox) {
        settingsForm.addEventListener('submit', function() {
            localStorage.setItem('theme', darkModeCheckbox.checked ? 'dark' : 'light');
        });
    }
});
</script>
@endsection
