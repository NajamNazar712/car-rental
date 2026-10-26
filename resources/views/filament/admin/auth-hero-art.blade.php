{{--
    Animated car outline for the admin login hero: the outline draws itself, headlights glow,
    wheels turn and speed lines stream past. Line colours follow the admin primary colour.
    All motion stops for users with prefers-reduced-motion (see styles.blade.php).
--}}
@php
    // [y, length, delay] for the background speed lines.
    $speedLines = [[560, 260, '0s'], [610, 180, '0.9s'], [655, 320, '1.7s'], [905, 240, '0.5s'], [945, 360, '1.3s'], [985, 200, '2.1s']];
    // [path, extra class, animation delay]: body first, then wheels, then details.
    $lines = [
        ['M150 790 L150 735 Q152 700 190 690 L300 668 Q390 590 520 572 L700 568 Q800 570 880 640 L1000 668 Q1048 678 1052 720 L1052 790 L958 790 A88 88 0 0 0 782 790 L418 790 A88 88 0 0 0 242 790 Z', '', '0s'],
        ['M330 820 m-70 0 a70 70 0 1 0 140 0 a70 70 0 1 0 -140 0', '', '0.6s'],
        ['M870 820 m-70 0 a70 70 0 1 0 140 0 a70 70 0 1 0 -140 0', '', '0.6s'],
        ['M335 670 Q410 606 520 592 L610 590 L610 670 Z', 'fi-auth-art-detail', '1.1s'],
        ['M630 590 L700 590 Q780 594 840 652 L845 670 L630 670 Z', 'fi-auth-art-detail', '1.2s'],
        ['M170 735 L1040 735', 'fi-auth-art-detail', '1.4s'],
        ['M620 680 L620 785', 'fi-auth-art-detail', '1.5s'],
        ['M845 662 L872 654 L876 670 Z', 'fi-auth-art-detail', '1.6s'],
        ['M80 900 L1120 900', 'fi-auth-art-detail', '0.3s'],
    ];
@endphp

<svg class="fi-auth-art" viewBox="0 0 1200 1600" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="fi-art-bg" x1="0" y1="0" x2="0.3" y2="1">
            <stop offset="0" stop-color="#050d1a"/>
            <stop offset="0.55" stop-color="#0a2340"/>
            <stop offset="1" stop-color="#071628"/>
        </linearGradient>
        <radialGradient id="fi-art-halo" cx="0.5" cy="0.5" r="0.5">
            <stop offset="0" stop-color="#1d5f99" stop-opacity="0.55"/>
            <stop offset="1" stop-color="#1d5f99" stop-opacity="0"/>
        </radialGradient>
        <filter id="fi-art-glow" x="-10%" y="-10%" width="120%" height="120%">
            <feGaussianBlur stdDeviation="5" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>

    <rect width="1200" height="1600" fill="url(#fi-art-bg)"/>
    <ellipse cx="600" cy="740" rx="620" ry="380" fill="url(#fi-art-halo)"/>

    {{-- Scene shifted down and enlarged slightly so the car sits closer to the headline. --}}
    <g transform="translate(600 800) scale(1.1) translate(-600 -735)">
        {{-- Speed lines streaming past behind the car --}}
        @foreach ($speedLines as [$y, $length, $delay])
            <line class="fi-auth-art-speed" x1="900" y1="{{ $y }}" x2="{{ 900 + $length }}" y2="{{ $y }}" style="animation-delay: {{ $delay }}"/>
        @endforeach

        {{-- Car outline, drawn line by line --}}
        <g filter="url(#fi-art-glow)">
            @foreach ($lines as [$d, $class, $delay])
                <path class="fi-auth-art-line {{ $class }}" d="{{ $d }}" pathLength="1" style="animation-delay: {{ $delay }}"/>
            @endforeach

            {{-- Wheels: inner rim ring and hub, plus evenly spaced spokes between them that turn. --}}
            @foreach ([330, 870] as $cx)
                <circle class="fi-auth-art-line fi-auth-art-detail" cx="{{ $cx }}" cy="820" r="50" pathLength="1" style="animation-delay: 0.8s"/>
                <circle class="fi-auth-art-line fi-auth-art-detail" cx="{{ $cx }}" cy="820" r="11" pathLength="1" style="animation-delay: 0.9s"/>
                <g class="fi-auth-art-spin">
                    @foreach ([0, 60, 120, 180, 240, 300] as $angle)
                        <line class="fi-auth-art-line fi-auth-art-detail" x1="{{ $cx }}" y1="806" x2="{{ $cx }}" y2="773" pathLength="1"
                              transform="rotate({{ $angle }} {{ $cx }} 820)" style="animation-delay: 1s"/>
                    @endforeach
                </g>
            @endforeach

            {{-- Headlight and taillight glow once the drawing completes --}}
            <path class="fi-auth-art-light" d="M1004 690 L1046 700 L1040 712 L1002 704 Z"/>
            <path class="fi-auth-art-light" d="M154 703 L186 696 L188 708 L156 714 Z" style="fill: #f87171"/>
        </g>
    </g>
</svg>
