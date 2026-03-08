<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('IJRPR')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Content Management',
                'System',
            ])
            ->renderHook(
                'panels::styles.after',
                fn () => '<style>
                    /* Sidebar background color */
                    .fi-sidebar {
                        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
                        border-right: 1px solid #e2e8f0 !important;
                    }
                    
                    /* Dark theme sidebar */
                    .dark .fi-sidebar {
                        background: linear-gradient(180deg, #1f2937 0%, #111827 100%) !important;
                        border-right: 1px solid #374151 !important;
                    }
                    
                    /* Sidebar navigation items */
                    .fi-sidebar-nav {
                        background: transparent !important;
                    }
                    
                    /* Sidebar text color */
                    .fi-sidebar-item-label,
                    .fi-sidebar-item-button {
                        color: #374151 !important;
                    }
                    
                    /* Dark theme sidebar text */
                    .dark .fi-sidebar-item-label,
                    .dark .fi-sidebar-item-button {
                        color: #d1d5db !important;
                    }
                    
                    /* Sidebar icons */
                    .fi-sidebar-item-icon {
                        color: #6b7280 !important;
                    }
                    
                    /* Dark theme sidebar icons */
                    .dark .fi-sidebar-item-icon {
                        color: #9ca3af !important;
                    }
                    
                    /* Active navigation item */
                    .fi-sidebar-item-active {
                        background: #fef3c7 !important;
                        border-left: 3px solid #f59e0b !important;
                    }
                    
                    /* Dark theme active item */
                    .dark .fi-sidebar-item-active {
                        background: #1e3a8a !important;
                        border-left: 3px solid #3b82f6 !important;
                    }
                    
                    /* Dark theme active item text */
                    .dark .fi-sidebar-item-active .fi-sidebar-item-label,
                    .dark .fi-sidebar-item-active .fi-sidebar-item-button {
                        color: white !important;
                    }
                    
                    /* Header/Topbar background */
                    .fi-topbar {
                        background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%) !important;
                        border-bottom: 2px solid #1e3a8a !important;
                    }
                    
                    /* Header text color */
                    .fi-topbar nav {
                        color: white !important;
                    }
                    
                    /* Brand name in header */
                    .fi-topbar .fi-logo {
                        color: white !important;
                        font-weight: bold !important;
                    }
                    
                    /* User menu button */
                    .fi-topbar button {
                        color: white !important;
                    }
                    
                    /* User dropdown menu */
                    .fi-dropdown-panel,
                    .fi-user-menu-dropdown {
                        background: white !important;
                        color: #1f2937 !important;
                    }
                    
                    /* Dropdown menu items */
                    .fi-dropdown-list-item,
                    .fi-user-menu-item {
                        color: #1f2937 !important;
                    }
                    
                    /* Dropdown menu item hover */
                    .fi-dropdown-list-item:hover,
                    .fi-user-menu-item:hover {
                        background: #f3f4f6 !important;
                        color: #1f2937 !important;
                    }
                    
                    /* Dropdown menu links */
                    .fi-dropdown-list-item a,
                    .fi-user-menu-item a {
                        color: #1f2937 !important;
                    }
                    
                    /* Dropdown menu icons - colorful */
                    .fi-dropdown-list-item svg,
                    .fi-user-menu-item svg,
                    .fi-dropdown-list-item-icon,
                    .fi-user-menu-item-icon {
                        color: #f59e0b !important;
                        fill: currentColor !important;
                    }
                    
                    /* Theme toggle icons - force yellow */
                    .fi-dropdown-list-item svg[data-slot="icon"],
                    .fi-dropdown-list-item .fi-icon,
                    button[x-on\\:click*="theme"] svg,
                    [x-on\\:click*="theme"] svg {
                        color: #fbbf24 !important;
                        fill: #fbbf24 !important;
                    }
                    
                    /* All dropdown icons force yellow */
                    .fi-dropdown-panel svg,
                    .fi-user-menu svg {
                        color: #f59e0b !important;
                        fill: #f59e0b !important;
                        stroke: #f59e0b !important;
                    }
                    
                    /* Dark theme toggle icon */
                    .fi-dropdown-list-item:has([x-on\\:click*="theme"]) svg {
                        color: #fbbf24 !important;
                        fill: #fbbf24 !important;
                    }
                    
                    /* Sign out icon */
                    .fi-dropdown-list-item:has([href*="logout"]) svg,
                    .fi-dropdown-list-item:has([wire\\:click*="logout"]) svg {
                        color: #f59e0b !important;
                        fill: #f59e0b !important;
                    }
                    
                    /* Body and wrapper - no padding */
                    .fi-body,
                    .fi-layout {
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    
                    /* Main content area - full width, no padding */
                    .fi-main {
                        width: 100% !important;
                        max-width: 100% !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    
                    /* Content container - 5px left/right padding */
                    .fi-main-ctn {
                        max-width: 100% !important;
                        width: 100% !important;
                        padding: 0 5px !important;
                        margin: 0 !important;
                    }
                    
                    /* Page wrapper - 5px left/right padding */
                    .fi-page {
                        max-width: 100% !important;
                        width: 100% !important;
                        padding: 0.75rem 5px !important;
                        margin: 0 !important;
                    }
                    
                    /* Page header - 5px left/right padding */
                    .fi-page-header {
                        padding-left: 5px !important;
                        padding-right: 5px !important;
                    }
                    
                    /* Page content inner - full width */
                    .fi-page-content {
                        max-width: 100% !important;
                        width: 100% !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    
                    /* Section container - 5px left/right padding */
                    .fi-section {
                        max-width: 100% !important;
                        padding-left: 5px !important;
                        padding-right: 5px !important;
                    }
                    
                    /* Table wrapper - full width */
                    .fi-ta-ctn {
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    
                    /* Table container - full width */
                    .fi-ta {
                        width: 100% !important;
                        max-width: 100% !important;
                        overflow-x: auto !important;
                    }
                    
                    /* Widget grid - full width */
                    .fi-wi-stats-overview-stat {
                        width: 100% !important;
                    }
                    
                    /* Form section - 5px left/right padding */
                    .fi-fo-section {
                        padding-left: 5px !important;
                        padding-right: 5px !important;
                    }
                    
                    /* Container - 5px left/right padding */
                    .container,
                    .fi-container {
                        max-width: 100% !important;
                        padding-left: 5px !important;
                        padding-right: 5px !important;
                    }
                    
                    /* Form wrapper - centered with border */
                    .fi-fo,
                    .fi-form,
                    form[wire\\:submit] {
                        max-width: 1200px !important;
                        margin: 0 auto !important;
                        padding: 24px !important;
                        background: white !important;
                        border: 1px solid #e5e7eb !important;
                        border-radius: 12px !important;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
                    }
                    
                    /* Dark theme - form background black, text white */
                    .dark .fi-fo,
                    .dark .fi-form,
                    .dark form[wire\\:submit] {
                        background: #1f2937 !important;
                        border-color: #374151 !important;
                    }
                    
                    /* Dark theme - text white */
                    .dark .fi-fo *,
                    .dark .fi-form * {
                        color: white !important;
                    }
                    
                    /* Dark theme - inputs dark background */
                    .dark .fi-fo input,
                    .dark .fi-fo select,
                    .dark .fi-fo textarea,
                    .dark .fi-form input,
                    .dark .fi-form select,
                    .dark .fi-form textarea {
                        background: #111827 !important;
                        color: white !important;
                        border-color: #374151 !important;
                    }
                    
                    /* Dark theme - page background */
                    .dark .fi-page {
                        background: #0f172a !important;
                    }
                    
                    /* File upload - show default Filament drag & drop */
                    .fi-fo-file-upload,
                    .fi-file-upload {
                        min-height: 120px !important;
                    }
                    
                    /* Show all drag & drop elements */
                    .fi-file-upload-dropzone,
                    .fi-file-upload-dropzone-content,
                    .fi-file-upload-hint,
                    .fi-file-upload-actions {
                        display: block !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                    }
                </style>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\LatestPapers::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureUserIsAdmin::class,
            ])
            ->authGuard('web');
    }
}
