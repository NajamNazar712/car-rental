{{--
    Admin panel style tweaks, injected via the HEAD_END render hook (AdminPanelProvider).
    Colours use Filament's --primary-* variables, so they follow Settings → General → Admin primary colour.
--}}
<style>
    /* White sidebar with a thin divider, standing out from the light gray page background. */
    @media (min-width: 1024px) {
        .fi-sidebar {
            background-color: #fff;
            border-inline-end: 1px solid rgb(15 23 42 / 0.08);
        }

        .dark .fi-sidebar {
            background-color: var(--gray-900);
            border-inline-end-color: rgb(255 255 255 / 0.08);
        }
    }

    /* Tighter sidebar spacing (Filament defaults: 1.75rem between groups, 2rem top/bottom padding). */
    .fi-sidebar-nav {
        padding-block: 1.25rem;
        row-gap: 1rem;
    }

    .fi-sidebar-nav-groups {
        row-gap: 1rem;
    }

    /* Slim sidebar scrollbar, only visible while hovering the menu. */
    .fi-sidebar-nav {
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
    }

    .fi-sidebar-nav:hover {
        scrollbar-color: rgb(15 23 42 / 0.25) transparent;
    }

    .dark .fi-sidebar-nav:hover {
        scrollbar-color: rgb(255 255 255 / 0.25) transparent;
    }

    /* Selected sidebar item: soft tint of the primary colour, accent bar, bolder label. */
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: var(--primary-50);
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn::before {
        content: '';
        position: absolute;
        inset-block: 0.375rem;
        inset-inline-start: 0;
        width: 3px;
        border-radius: 9999px;
        background-color: var(--primary-600);
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label {
        font-weight: 600;
    }

    /* Hover on other items: very light tint instead of gray. */
    .fi-sidebar-item.fi-sidebar-item-has-url:not(.fi-active) > .fi-sidebar-item-btn:hover,
    .fi-sidebar-item.fi-sidebar-item-has-url:not(.fi-active) > .fi-sidebar-item-btn:focus-visible {
        background-color: color-mix(in oklab, var(--primary-50) 60%, transparent);
    }

    /* Dark mode equivalents. */
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: color-mix(in oklab, var(--primary-400) 15%, transparent);
    }

    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn::before {
        background-color: var(--primary-400);
    }

    .dark .fi-sidebar-item.fi-sidebar-item-has-url:not(.fi-active) > .fi-sidebar-item-btn:hover,
    .dark .fi-sidebar-item.fi-sidebar-item-has-url:not(.fi-active) > .fi-sidebar-item-btn:focus-visible {
        background-color: color-mix(in oklab, var(--primary-400) 8%, transparent);
    }

    /*
     * Login / password reset: two columns on large screens — image panel (auth-hero view) on the
     * start side, form on the end side. On small screens only the form is shown.
     */
    .fi-auth-hero {
        display: none;
        position: relative;
        overflow: hidden;
        flex-direction: column;
        justify-content: flex-end;
        min-height: 100dvh;
        padding: 3.5rem;
        color: #fff;
        background-color: #0b2a4a;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .fi-auth-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgb(3 7 13 / 0.85) 0%, rgb(3 7 13 / 0.35) 35%, transparent 60%);
    }

    .fi-auth-hero-content {
        position: relative;
        z-index: 1;
        max-width: 32rem;
    }

    /* Self-drawing car outline (auth-hero-art view). Line colour follows the admin primary colour. */
    .fi-auth-art {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .fi-auth-art-line {
        fill: none;
        stroke: var(--primary-300);
        stroke-width: 4;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 1;
        stroke-dashoffset: 1;
        animation: fi-art-draw 10s ease-in-out infinite;
    }

    .fi-auth-art-line.fi-auth-art-detail {
        stroke: var(--primary-200);
        stroke-width: 2.5;
        opacity: 0.85;
    }

    .fi-auth-art-light {
        fill: var(--primary-200);
        opacity: 0;
        animation: fi-art-light 10s ease-in-out infinite;
    }

    .fi-auth-art-spin {
        transform-box: fill-box;
        transform-origin: center;
        animation: fi-art-spin 1.6s linear infinite;
    }

    .fi-auth-art-speed {
        stroke: var(--primary-300);
        stroke-width: 2;
        stroke-linecap: round;
        opacity: 0;
        animation: fi-art-speed 2.8s linear infinite;
    }

    @keyframes fi-art-draw {
        0% { stroke-dashoffset: 1; opacity: 1; }
        35% { stroke-dashoffset: 0; }
        80% { stroke-dashoffset: 0; opacity: 1; }
        92%, 100% { stroke-dashoffset: 0; opacity: 0; }
    }

    @keyframes fi-art-light {
        0%, 38% { opacity: 0; }
        45%, 80% { opacity: 1; }
        92%, 100% { opacity: 0; }
    }

    @keyframes fi-art-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes fi-art-speed {
        0% { transform: translateX(260px); opacity: 0; }
        20% { opacity: 0.5; }
        100% { transform: translateX(-720px); opacity: 0; }
    }

    /* Respect "reduce motion": show the finished drawing, no movement. */
    @media (prefers-reduced-motion: reduce) {
        .fi-auth-art * { animation: none !important; }
        .fi-auth-art-line { stroke-dashoffset: 0; opacity: 1; }
        .fi-auth-art-light { opacity: 1; }
        .fi-auth-art-speed { display: none; }
    }

    .fi-auth-hero-brand {
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--primary-300);
    }

    .fi-auth-hero-title {
        margin-top: 0.75rem;
        font-size: 2.5rem;
        line-height: 1.15;
        font-weight: 700;
    }

    .fi-auth-hero-text {
        margin-top: 1rem;
        font-size: 1.125rem;
        color: rgb(255 255 255 / 0.8);
    }

    @media (min-width: 1024px) {
        .fi-simple-layout:has(> .fi-auth-hero) {
            flex-direction: row;
            align-items: stretch;
        }

        .fi-simple-layout:has(> .fi-auth-hero) > .fi-auth-hero {
            display: flex;
            flex: 0 0 55%;
            /* Stretch to the layout height instead of forcing a second full-screen height (avoids a scrollbar). */
            min-height: 0;
        }

        .fi-simple-layout:has(> .fi-auth-hero) .fi-simple-main {
            margin-block: 2rem;
        }

        .fi-simple-layout:has(> .fi-auth-hero) > .fi-simple-main-ctn {
            flex: 1 1 0;
            width: auto;
            padding-inline: 3rem;
            background-color: #fff;
        }

        /* Flat, modern form column instead of a floating card. */
        .fi-simple-layout:has(> .fi-auth-hero) .fi-simple-main {
            max-width: 26rem;
            box-shadow: none;
            --tw-ring-shadow: 0 0 #0000;
            background-color: transparent;
            padding-inline: 0;
        }

        .dark .fi-simple-layout:has(> .fi-auth-hero) > .fi-simple-main-ctn {
            background-color: var(--gray-950);
        }

        /* Soft brand glow in the top corner of the form column. */
        .fi-simple-layout:has(> .fi-auth-hero) > .fi-simple-main-ctn {
            background-image:
                radial-gradient(circle at 100% 0%, color-mix(in oklab, var(--primary-100) 70%, transparent), transparent 42%),
                radial-gradient(circle at 0% 100%, color-mix(in oklab, var(--primary-50) 80%, transparent), transparent 38%);
        }

        .dark .fi-simple-layout:has(> .fi-auth-hero) > .fi-simple-main-ctn {
            background-image: radial-gradient(circle at 100% 0%, color-mix(in oklab, var(--primary-500) 14%, transparent), transparent 45%);
        }
    }

    /* ---- Sign-in form (all auth pages, every screen size) ---- */

    .fi-simple-main {
        animation: fi-auth-fade-up 0.6s ease-out both;
    }

    @keyframes fi-auth-fade-up {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: none; }
    }

    /* Header: left-aligned logo, larger heading. */
    .fi-simple-main .fi-simple-header {
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .fi-simple-main .fi-simple-header .fi-logo {
        margin-bottom: 2rem;
        text-align: start;
    }

    .fi-simple-main .fi-simple-header-heading {
        text-align: start;
        font-size: 1.875rem;
        line-height: 2.25rem;
        letter-spacing: -0.02em;
    }

    .fi-simple-main .fi-simple-header-subheading {
        text-align: start;
        font-size: 1rem;
        margin-top: 0.5rem;
    }

    /* Labels. */
    .fi-simple-main .fi-fo-field-label-content {
        font-weight: 600;
        color: var(--gray-700);
    }

    .dark .fi-simple-main .fi-fo-field-label-content {
        color: var(--gray-200);
    }

    /* Inputs: white fields with a clear border (so they never look disabled) and a brand ring on focus. */
    .fi-simple-main .fi-input-wrp {
        border-radius: 0.75rem;
        background-color: #fff;
        box-shadow:
            0 0 0 1px rgb(15 23 42 / 0.18),
            0 1px 2px rgb(15 23 42 / 0.06) !important;
        transition: box-shadow 0.15s;
    }

    .fi-simple-main .fi-input-wrp:hover {
        box-shadow:
            0 0 0 1px rgb(15 23 42 / 0.3),
            0 1px 2px rgb(15 23 42 / 0.06) !important;
    }

    .fi-simple-main .fi-input-wrp:focus-within {
        box-shadow:
            0 0 0 2px var(--primary-500),
            0 8px 20px -12px color-mix(in oklab, var(--primary-600) 60%, transparent) !important;
    }

    .fi-simple-main .fi-input-wrp .fi-input {
        padding-block: 0.75rem;
    }

    .dark .fi-simple-main .fi-input-wrp {
        background-color: rgb(255 255 255 / 0.05);
        box-shadow: 0 0 0 1px rgb(255 255 255 / 0.2) !important;
    }

    .dark .fi-simple-main .fi-input-wrp:hover {
        box-shadow: 0 0 0 1px rgb(255 255 255 / 0.3) !important;
    }

    .dark .fi-simple-main .fi-input-wrp:focus-within {
        background-color: rgb(255 255 255 / 0.06);
        box-shadow: 0 0 0 2px var(--primary-400) !important;
    }

    /* Sign-in button: taller, rounded, soft brand shadow, slight lift on hover. */
    .fi-simple-main .fi-form-actions .fi-btn,
    .fi-simple-main .fi-ac .fi-btn {
        padding-block: 0.8rem;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        font-weight: 600;
        box-shadow: 0 12px 24px -12px color-mix(in oklab, var(--primary-600) 75%, transparent);
        transition: transform 0.15s, box-shadow 0.15s, background-color 0.15s;
    }

    .fi-simple-main .fi-form-actions .fi-btn:hover,
    .fi-simple-main .fi-ac .fi-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 28px -12px color-mix(in oklab, var(--primary-600) 85%, transparent);
    }

    /* Note under the form. */
    .fi-auth-footnote {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        margin-top: 2rem;
        font-size: 0.75rem;
        color: var(--gray-400);
    }

    .fi-auth-footnote-icon {
        width: 0.875rem;
        height: 0.875rem;
    }

    @media (prefers-reduced-motion: reduce) {
        .fi-simple-main { animation: none; }
        .fi-simple-main .fi-btn:hover { transform: none; }
    }
</style>
