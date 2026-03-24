<?php

namespace App\Providers\Filament;

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
            ->path(env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42'))
            ->login(false)
            ->brandName('IJRPR')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('220px')
            ->collapsedSidebarWidth('60px')
            ->breadcrumbs(false)
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
                    
                    /* File upload container - show all action buttons */
                    .fi-fo-file-upload-file {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        padding: 12px !important;
                        background: #f3f4f6 !important;
                        border-radius: 8px !important;
                        margin-bottom: 8px !important;
                        gap: 12px !important;
                    }
                    
                    /* Show file name but make it clean */
                    .fi-fo-file-upload-file-name {
                        flex: 1 !important;
                        font-weight: 500 !important;
                        color: #374151 !important;
                        display: block !important;
                    }
                    
                    .dark .fi-fo-file-upload-file-name {
                        color: #d1d5db !important;
                    }
                    
                    /* File upload actions - show all buttons */
                    .fi-fo-file-upload-file-actions {
                        display: flex !important;
                        gap: 8px !important;
                        align-items: center !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                    }
                    
                    /* Force show all action buttons */
                    .fi-fo-file-upload-file-actions > * {
                        display: inline-flex !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                    }
                    
                    /* Make download button visible */
                    .fi-fo-file-upload-file-actions button,
                    .fi-fo-file-upload-file-actions a {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        padding: 8px !important;
                        border-radius: 6px !important;
                        transition: all 0.2s !important;
                        min-width: 32px !important;
                        min-height: 32px !important;
                    }
                    
                    /* Download button - green with icon */
                    button[x-on\:click*="download"],
                    a[download],
                    .fi-fo-file-upload-file-actions button:first-child:not([wire\:click*="remove"]) {
                        background: #10b981 !important;
                        color: white !important;
                        order: 1 !important;
                    }
                    
                    button[x-on\:click*="download"]:hover,
                    a[download]:hover {
                        background: #059669 !important;
                    }
                    
                    /* Delete/Remove button - red */
                    .fi-fo-file-upload-file-actions button[wire\:click*="remove"],
                    .fi-fo-file-upload-file-actions button:last-child {
                        background: #ef4444 !important;
                        color: white !important;
                        order: 2 !important;
                    }
                    
                    .fi-fo-file-upload-file-actions button[wire\:click*="remove"]:hover {
                        background: #dc2626 !important;
                    }
                    
                    /* Show icons in buttons */
                    .fi-fo-file-upload-file-actions svg {
                        width: 18px !important;
                        height: 18px !important;
                        display: block !important;
                        stroke: currentColor !important;
                        fill: none !important;
                    }
                    
                    /* File item container */
                    .fi-fo-file-upload-file {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        padding: 12px !important;
                        background: #f3f4f6 !important;
                        border-radius: 8px !important;
                        gap: 12px !important;
                    }
                    
                    .dark .fi-fo-file-upload-file {
                        background: #374151 !important;
                    }
                    
                    /* Hide all action buttons/icons at the end of file upload */
                    .fi-fo-file-upload-file button,
                    .fi-fo-file-upload-file > button,
                    .fi-fo-file-upload-file-actions,
                    .fi-fo-file-upload-file-actions *,
                    button[wire\:click*="removeUploadedFile"],
                    button[x-on\:click*="download"],
                    .fi-fo-file-upload-file svg,
                    .fi-fo-file-upload-file [role="button"] {
                        display: none !important;
                        visibility: hidden !important;
                        opacity: 0 !important;
                        width: 0 !important;
                        height: 0 !important;
                        pointer-events: none !important;
                    }
                    
                    /* Only show file name */
                    .fi-fo-file-upload-file {
                        pointer-events: none !important;
                    }
                    
                    .fi-fo-file-upload-file-name {
                        pointer-events: auto !important;
                    }
                    
                    /* File upload item - show download button prominently */
                    .fi-fo-file-upload-file {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        padding: 12px !important;
                        background: #f3f4f6 !important;
                        border-radius: 8px !important;
                        margin-bottom: 8px !important;
                    }
                    
                    /* Dark theme file upload item */
                    .dark .fi-fo-file-upload-file {
                        background: #374151 !important;
                    }
                    
                    /* Download button styling */
                    .fi-fo-file-upload-file-actions {
                        display: flex !important;
                        gap: 8px !important;
                    }
                    
                    /* Download button */
                    .fi-fo-file-upload-file-actions button,
                    .fi-fo-file-upload-file-actions a {
                        background: #3b82f6 !important;
                        color: white !important;
                        padding: 8px 16px !important;
                        border-radius: 6px !important;
                        font-weight: 500 !important;
                        text-decoration: none !important;
                        display: inline-flex !important;
                        align-items: center !important;
                    }
                    
                    /* Download button hover */
                    .fi-fo-file-upload-file-actions button:hover,
                    .fi-fo-file-upload-file-actions a:hover {
                        background: #2563eb !important;
                    }
                    
                    /* Remove button (X) */
                    .fi-fo-file-upload-file-actions button[wire\\:click*="removeUploadedFile"] {
                        background: #ef4444 !important;
                    }
                    
                    /* Remove button hover */
                    .fi-fo-file-upload-file-actions button[wire\\:click*="removeUploadedFile"]:hover {
                        background: #dc2626 !important;
                    }
                    
                    /* Add "Download File" text via CSS */
                    .fi-fo-file-upload-file-actions a[download]::before,
                    .fi-fo-file-upload-file-actions button[x-on\\:click*="download"]::before {
                        content: "Download File" !important;
                        margin-right: 4px !important;
                    }
                    
                    /* Hide file size and name info */
                    .fi-fo-file-upload-file-info {
                        display: none !important;
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
                        padding: 0.5rem 5px !important;
                        margin: 0 !important;
                    }
                    
                    /* Page header - 5px left/right padding */
                    .fi-page-header {
                        max-width: 1200px !important;
                        margin: 0 auto !important;
                        padding-left: 5px !important;
                        padding-right: 5px !important;
                    }
                    
                    /* Page header wrapper - center with 1200px */
                    .fi-page-header-wrapper,
                    .fi-page-header-ctn,
                    .fi-page-header > div {
                        max-width: 1200px !important;
                        margin: 0 auto !important;
                        width: 100% !important;
                    }
                    
                    /* Page heading - align with form border (force alignment) */
                    .fi-header-heading,
                    .fi-page-header h1,
                    .fi-page-header-heading,
                    .fi-simple-page h1,
                    h1.text-3xl,
                    .fi-page-header .fi-header-heading,
                    [class*="fi-header"] h1,
                    [class*="fi-page-header"] h1 {
                        padding-left: 20px !important;
                        padding-right: 20px !important;
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                    }
                    
                    /* Force all h1 in page header */
                    .fi-page-header > * > h1,
                    .fi-page-header > div > h1 {
                        padding-left: 20px !important;
                        max-width:1200px !important;
                    }
                    
                    /* Header actions alignment */
                    .fi-page-header-actions {
                        padding-right: 20px !important;
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
                    form[wire\\:submit],
                    .settings-form-wrapper {
                        max-width: 1200px !important;
                        width: 100% !important;
                        margin: 0 auto !important;
                        padding: 20px !important;
                        background: white !important;
                        border: 1px solid #e5e7eb !important;
                        border-radius: 12px !important;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
                    }
                    
                    /* Settings form - ensure full width */
                    .settings-form-wrapper .fi-fo,
                    .settings-form-wrapper .fi-form {
                        max-width: 100% !important;
                        border: none !important;
                        box-shadow: none !important;
                        padding: 0 !important;
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
                    
                    /* Filter dropdown - light theme */
                    .fi-dropdown-panel,
                    .fi-ta-filters-dropdown {
                        background: white !important;
                        color: #1f2937 !important;
                    }
                    
                    /* Filter dropdown items */
                    .fi-dropdown-panel *,
                    .fi-ta-filters-dropdown * {
                        color: #1f2937 !important;
                    }
                    
                    /* Dark theme - filter dropdown */
                    .dark .fi-dropdown-panel,
                    .dark .fi-ta-filters-dropdown {
                        background: #1f2937 !important;
                        border: 1px solid #374151 !important;
                    }
                    
                    /* Dark theme - filter dropdown text */
                    .dark .fi-dropdown-panel *,
                    .dark .fi-ta-filters-dropdown * {
                        color: white !important;
                    }
                    
                    /* Dark theme - filter inputs */
                    .dark .fi-dropdown-panel input,
                    .dark .fi-dropdown-panel select,
                    .dark .fi-ta-filters-dropdown input,
                    .dark .fi-ta-filters-dropdown select {
                        background: #111827 !important;
                        color: white !important;
                        border-color: #374151 !important;
                    }
                    
                    /* Dark theme - filter labels */
                    .dark .fi-dropdown-panel label,
                    .dark .fi-ta-filters-dropdown label {
                        color: #d1d5db !important;
                    }
                    
                    /* Dark theme - filter buttons */
                    .dark .fi-dropdown-panel button,
                    .dark .fi-ta-filters-dropdown button {
                        color: white !important;
                    }
                    
                    /* File upload - show default Filament drag & drop */
                    .fi-fo-file-upload,
                    .fi-file-upload {
                        min-height: 60px !important;
                        margin-bottom: 8px !important;
                    }
                    
                    .fi-header fi-header-heading{
                    max-width : 1200px;
                    }

                    /* Allow text selection in table cells */
                    .fi-ta-cell, .fi-ta-cell * {
                        user-select: text !important;
                        -webkit-user-select: text !important;
                    }



                    /* Reduce file upload field spacing */
                    .fi-fo-field-wrp:has(.fi-fo-file-upload) {
                        margin-bottom: 8px !important;
                    }
                    
                    
                    /* File upload dropzone - smaller */
                    .fi-file-upload-dropzone {
                        //padding: 8px !important;
                        min-height: 50px !important;
                    }
                    
                    /* File upload hint text - smaller */
                    .fi-file-upload-hint {
                        font-size: 0.75rem !important;
                        padding: 4px 0 !important;
                    }
                    
                    /* Comments/Textarea field - smaller height */
                    textarea[id*="comment"],
                    textarea[id*="Comment"],
                    .fi-fo-textarea textarea {
                        min-height: 60px !important;
                        max-height: 100px !important;
                    }
                    
                    /* Reduce spacing between form fields */
                    .fi-fo-field-wrp {
                        margin-bottom: 10px !important;
                    }
                    
                    /* Form component wrapper - reduce gap */
                    .fi-fo-component-ctn {
                        gap: 8px !important;
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
            ->renderHook(
                'panels::body.end',
                fn () => '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.querySelectorAll("form").forEach(f => f.setAttribute("novalidate", true));
                    });
                    document.addEventListener("livewire:navigated", function() {
                        document.querySelectorAll("form").forEach(f => f.setAttribute("novalidate", true));
                    });

                    // Allow text selection in table cells
                    function enableTableTextSelection() {
                        document.querySelectorAll(".fi-ta-cell").forEach(function(cell) {
                            cell.addEventListener("mousedown", function(e) {
                                e.stopPropagation();
                            });
                        });
                    }
                    document.addEventListener("DOMContentLoaded", enableTableTextSelection);
                    document.addEventListener("livewire:navigated", enableTableTextSelection);
                    document.addEventListener("livewire:update", enableTableTextSelection);
                </script>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
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
                \App\Http\Middleware\RedirectIfNotAdmin::class,
            ])
            ->authMiddleware([
                \App\Http\Middleware\RedirectIfNotAdmin::class,
            ])
            ->authGuard('web');
    }
}
