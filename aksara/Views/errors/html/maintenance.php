<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= phrase('Aksara Maintenance') ?>
</title>
        <script>
            (() => {
                const storedTheme = window.localStorage.getItem('aksara-maintenance-theme');
                const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.dataset.theme = storedTheme || preferredTheme;
            })();
        </script>
        <style>
            :root {
                color-scheme: light;
                --ink: #172033;
                --muted: #667085;
                --line: rgba(0,0,0,0.1);
                --panel: #ffffff;
                --page: #f4f7fb;
                --accent: #0f766e;
                --accent-soft: #dff7f3;
                --accent-ink: #134e4a;
                --board: #dbe3ee;
                --button-primary: #172033;
                --button-primary-ink: #ffffff;
                --danger: #dc2626;
                --tile-empty: rgba(255, 255, 255, .54);
                --tile: #eef2f7;
                --tile-2: #dff7f3;
                --tile-4: #d9eaf7;
                --tile-8: #fde8c7;
                --tile-16: #fed7aa;
                --tile-32: #fecaca;
                --tile-64: #fca5a5;
                --tile-high: #0f766e;
            }
            html[data-theme="dark"] {
                color-scheme: dark;
                --ink: #f4f7fb;
                --muted: #9ba8bd;
                --line: rgba(255,255,255,0.12);
                --panel: #111827;
                --page: #070b13;
                --accent: #5eead4;
                --accent-soft: #123d3b;
                --accent-ink: #8ff7e8;
                --board: #0b1220;
                --button-primary: #5eead4;
                --button-primary-ink: #042f2e;
                --tile-empty: #1b2433;
                --tile: #1f2937;
                --tile-2: #143c3a;
                --tile-4: #17324a;
                --tile-8: #4a3217;
                --tile-16: #5a2f16;
                --tile-32: #5f2128;
                --tile-64: #7f1d1d;
                --tile-high: #0f766e;
            }
            * {
                box-sizing: border-box;
            }
            html,
            body {
                min-height: 100%;
                margin: 0;
            }
            body {
                min-height: 100vh;
                background:
                    linear-gradient(135deg, rgba(15, 118, 110, .08), transparent 34%),
                    linear-gradient(315deg, rgba(180, 83, 9, .08), transparent 32%),
                    var(--page);
                color: var(--ink);
                font-family: Inter, "Helvetica Neue", Helvetica, Arial, sans-serif;
                display: grid;
                place-items: center;
                padding: 1.25rem;
            }
            .shell {
                width: min(1120px, 100%);
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
                align-items: stretch;
                background: var(--panel);
                border-radius: 1.25rem;
                overflow: hidden;
            }
            .notice,
            .game {
                background: var(--panel);
                min-height: 100%;
            }
            .notice {
                align-content: center;
                display: grid;
                padding: clamp(1.5rem, 4vw, 3rem);
            }
            .logo-container {
                margin-bottom: 3rem;
            }
            .logo-container img {
                max-width: 200px;
                max-height: 48px;
            }
            .eyebrow {
                color: var(--accent);
                font-size: .8125rem;
                font-weight: 700;
                letter-spacing: .08em;
                margin: 0 0 .75rem;
                text-transform: uppercase;
            }
            h1 {
                font-size: clamp(2rem, 6vw, 4.75rem);
                line-height: .96;
                letter-spacing: 0;
                margin: 0 0 1rem;
            }
            .message {
                color: var(--muted);
                font-size: 1.0625rem;
                line-height: 1.65;
                max-width: 36rem;
                margin: 0;
            }
            .meta {
                display: flex;
                flex-wrap: wrap;
                gap: .5rem;
                margin-top: 1.5rem;
            }
            .pill {
                border: 1px solid var(--line);
                border-radius: 999px;
                color: var(--muted);
                font-size: .875rem;
                padding: .45rem .8rem;
            }
            .game {
                border-left: 1px solid var(--line);
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 1rem;
            }
            .game-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: .75rem;
            }
            .game-title {
                font-size: 1rem;
                font-weight: 800;
                margin: 0;
            }
            .status {
                min-height: 2.5rem;
                border-radius: .5rem;
                border: 1px solid var(--line);
                text-align: center;
                color: var(--accent-ink);
                padding: .6rem .75rem;
                font-size: .925rem;
                line-height: 1.25;
                margin-bottom: .85rem;
            }
            .board {
                position: relative;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-template-rows: repeat(4, 1fr);
                gap: .5rem;
                width: 100%;
                aspect-ratio: 1;
                background: var(--board);
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: .5rem;
                touch-action: none;
            }
            .board:focus,
            .board:focus-visible {
                outline: none;
            }
            .tile {
                border-radius: 8px;
                display: grid;
                font-size: clamp(1.35rem, 6vw, 2.15rem);
                font-weight: 900;
                height: 100%;
                line-height: 1;
                min-height: 0;
                place-items: center;
                min-width: 0;
                user-select: none;
                will-change: transform;
            }
            .tile:not(:empty) {
                transform: scale(.98);
            }
            .tile.hidden-during-move {
                color: transparent;
            }
            .tile.spawned {
                animation: tile-zoom .28s cubic-bezier(.2, .85, .2, 1);
            }
            .tile-ghost {
                border-radius: 8px;
                display: grid;
                font-size: clamp(1.35rem, 6vw, 2.15rem);
                font-weight: 900;
                line-height: 1;
                place-items: center;
                pointer-events: none;
                position: absolute;
                transform: translate3d(0, 0, 0) scale(.98);
                transition: transform .32s cubic-bezier(.2, .8, .2, 1);
                user-select: none;
                z-index: 5;
            }
            .tile-0 {
                background: var(--tile-empty);
            }
            .tile-2 {
                background: var(--tile-2);
            }
            .tile-4 {
                background: var(--tile-4);
            }
            .tile-8 {
                background: var(--tile-8);
            }
            .tile-16 {
                background: var(--tile-16);
            }
            .tile-32 {
                background: var(--tile-32);
            }
            .tile-64 {
                background: var(--tile-64);
            }
            .tile-high {
                background: var(--tile-high);
                color: #fff;
                font-size: clamp(1.05rem, 5vw, 1.75rem);
            }
            @keyframes tile-zoom {
                from {
                    transform: scale(.82);
                }
                to {
                    transform: scale(.98);
                }
            }
            .btn {
                appearance: none;
                border: 0;
                border-radius: .5rem;
                background: var(--button-primary);
                color: var(--button-primary-ink);
                cursor: pointer;
                font: inherit;
                font-size: .925rem;
                font-weight: 700;
                min-height: 2.5rem;
                padding: .65rem 1rem;
                width: 100%;
            }
            .btn.secondary {
                background: var(--panel);
                border: 1px solid var(--line);
                color: var(--ink);
            }
            .theme-toggle {
                align-items: center;
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: 999px;
                color: var(--ink);
                cursor: pointer;
                display: inline-flex;
                font: inherit;
                font-size: .875rem;
                font-weight: 700;
                gap: .5rem;
                min-height: 2.5rem;
                padding: .5rem .85rem;
                position: fixed;
                right: 1rem;
                top: 1rem;
                z-index: 45;
            }
            .theme-toggle-icon {
                display: inline-grid;
                font-size: 1rem;
                height: 1.25rem;
                place-items: center;
                width: 1.25rem;
            }
            .actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: .5rem;
                margin-top: .85rem;
            }
            .score {
                color: var(--muted);
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: .5rem;
                margin-bottom: .85rem;
                text-align: center;
            }
            .score span {
                border: 1px solid var(--line);
                border-radius: .5rem;
                padding: .5rem;
            }
            .score b {
                color: var(--ink);
                display: block;
                font-size: 1.25rem;
            }
            .hint {
                color: var(--muted);
                font-size: .825rem;
                line-height: 1.45;
                margin: .85rem 0 0;
                text-align: center;
            }
            .ready-alert {
                align-items: center;
                background: #ecfdf3;
                border: 1px solid #86efac;
                border-radius: 1rem;
                box-shadow: 0 18px 50px rgba(23, 32, 51, .12);
                color: #14532d;
                display: none;
                gap: .75rem;
                justify-content: space-between;
                left: 50%;
                max-width: min(520px, calc(100% - 2rem));
                padding: .85rem;
                position: fixed;
                bottom: 1rem;
                transform: translateX(-50%);
                width: 100%;
                z-index: 50;
            }
            .ready-alert.visible {
                display: flex;
            }
            .ready-alert p {
                font-size: .95rem;
                font-weight: 700;
                line-height: 1.35;
                margin: 0;
            }
            .ready-alert .btn {
                flex: 0 0 auto;
                min-height: 2.25rem;
                padding: .45rem .85rem;
                width: auto;
            }
            @media (max-width: 760px) {
                body {
                    display: block;
                }
                .shell {
                    grid-template-columns: 1fr;
                    margin: 0 auto;
                }
                .game {
                    border-left: 0;
                    border-top: 1px solid var(--line);
                }
            }
        </style>
    </head>
    <body>
        <button class="theme-toggle" type="button" id="theme-toggle">
            <span class="theme-toggle-icon" id="theme-toggle-icon">◐</span>
            <span id="theme-toggle-label"><?= phrase('Dark mode') ?></span>
        </button>
        <div class="ready-alert" id="ready-alert" role="alert">
            <p><?= phrase('The website is ready to access again.') ?></p>
            <button class="btn" type="button" id="refresh-page"><?= phrase('Refresh') ?></button>
        </div>
        <main class="shell">
            <section class="notice" aria-labelledby="maintenance-title">
                <div class="logo-container">
                    <img src="<?= base_url(UPLOAD_PATH . '/settings/' . get_setting('app_logo')) ?>" alt="Logo" />
                </div>
                <p class="eyebrow"><?= phrase('Under Maintenance') ?></p>
                <h1 id="maintenance-title"><?= phrase('We will be right back.') ?></h1>
                <p class="message">
                    <?= phrase('We\'re currently performing maintenance.') ?> <?= phrase('While the system is being tuned up, you can pass the time by sliding a few tiles into place.') ?>
                </p>
                <div class="meta" aria-label="<?= phrase('Maintenance details') ?>">
                    <span class="pill">HTTP 503</span>
                    <span class="pill"><?= phrase('No data is affected.') ?></span>
                    <span class="pill"><?= phrase('Refresh later') ?></span>
                </div>
            </section>

            <section class="game" aria-labelledby="game-title">
                <div class="game-head">
                    <h2 class="game-title" id="game-title">2048 Mini</h2>
                    <span class="pill"><?= phrase('Reach 2048') ?></span>
                </div>
                <div class="score" aria-label="<?= phrase('Score') ?>">
                    <span><?= phrase('Score') ?><b id="score">0</b></span>
                    <span><?= phrase('Best') ?><b id="best">0</b></span>
                </div>
                <div class="status" id="status" role="status" aria-live="polite"><?= phrase('Use arrow keys or swipe.') ?></div>
                <div class="board" id="board" aria-label="<?= phrase('2048 board') ?>" tabindex="0"></div>
                <div class="actions">
                    <button class="btn" type="button" id="reset"><?= phrase('New game') ?></button>
                    <button class="btn secondary" type="button" id="robot"><?= phrase('Watch robot') ?></button>
                </div>
                <p class="hint"><?= phrase('Merge matching tiles. The board works with keyboard arrows and touch swipe.') ?></p>
            </section>
        </main>

        <script>
            (() => {
                const i18n = <?= json_encode(
                    [
                      'emptyTile' => phrase('Tile {{number}}, empty'),
                      'darkMode' => phrase('Dark mode'),
                      'keepSliding' => phrase('Keep sliding.'),
                      'lightMode' => phrase('Light mode'),
                      'mergedPoints' => phrase('Merged {{points}} points.'),
                      'movesLeft' => phrase('No moves left. New game?'),
                      'paused' => phrase('Robot paused. Your move.'),
                      'playing' => phrase('Robot is playing...'),
                      'ranOut' => phrase('Robot ran out of moves. New game?'),
                      'stopRobot' => phrase('Stop robot'),
                      'tileValue' => phrase('Tile {{number}}, {{value}}'),
                      'useArrows' => phrase('Use arrow keys or swipe.'),
                      'watchRobot' => phrase('Watch robot'),
                      'won' => phrase('2048 reached. Maintenance may now end with dignity.'),
                    ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ) ?>;
                const translate = (key, params = {}) => Object.keys(params).reduce(
                    (text, name) => text.replaceAll(`{{${name}}}`, params[name]),
                    i18n[key] || key
                );
                const boardElement = document.getElementById('board');
                const statusElement = document.getElementById('status');
                const resetButton = document.getElementById('reset');
                const robotButton = document.getElementById('robot');
                const themeToggle = document.getElementById('theme-toggle');
                const themeToggleIcon = document.getElementById('theme-toggle-icon');
                const themeToggleLabel = document.getElementById('theme-toggle-label');
                const scoreElement = document.getElementById('score');
                const bestElement = document.getElementById('best');
                const readyAlert = document.getElementById('ready-alert');
                const refreshButton = document.getElementById('refresh-page');
                const size = 4;
                const storageKey = 'aksara-maintenance-2048-best';
                let cells = [];
                let board = [];
                let score = 0;
                let best = Number(window.localStorage.getItem(storageKey) || 0);
                let won = false;
                let touchStart = null;
                let robotTimer = null;
                let isAnimating = false;

                const applyThemeLabel = () => {
                    const isDark = document.documentElement.dataset.theme === 'dark';
                    themeToggleIcon.textContent = isDark ? '☀' : '☾';
                    themeToggleLabel.textContent = isDark ? translate('lightMode') : translate('darkMode');
                };

                const toggleTheme = () => {
                    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                    document.documentElement.dataset.theme = nextTheme;
                    window.localStorage.setItem('aksara-maintenance-theme', nextTheme);
                    applyThemeLabel();
                };

                const createCells = () => {
                    boardElement.innerHTML = '';
                    cells = Array.from({ length: size * size }, (_, index) => {
                        const tile = document.createElement('div');
                        tile.className = 'tile tile-0';
                        tile.setAttribute('role', 'gridcell');
                        tile.setAttribute('aria-label', translate('emptyTile', { number: index + 1 }));
                        boardElement.appendChild(tile);

                        return tile;
                    });
                };

                const emptyIndexes = () => board
                    .map((value, index) => value ? null : index)
                    .filter((index) => index !== null);

                const addTile = () => {
                    return addTileTo(board);
                };

                const addTileTo = (state) => {
                    const empty = emptyIndexesFor(state);

                    if (! empty.length) {
                        return null;
                    }

                    const index = empty[Math.floor(Math.random() * empty.length)];
                    state[index] = Math.random() < .9 ? 2 : 4;

                    return index;
                };

                const getPositions = (direction, index) => {
                    if (direction === 'left') {
                        return [0, 1, 2, 3].map((offset) => index * size + offset);
                    }

                    if (direction === 'right') {
                        return [3, 2, 1, 0].map((offset) => index * size + offset);
                    }

                    if (direction === 'up') {
                        return [0, 1, 2, 3].map((offset) => offset * size + index);
                    }

                    return [3, 2, 1, 0].map((offset) => offset * size + index);
                };

                const getLine = (state, direction, index) => {
                    return getPositions(direction, index).map((position) => state[position]);
                };

                const previewMove = (state, direction) => {
                    const next = Array(size * size).fill(0);
                    let gained = 0;
                    const movements = [];

                    for (let index = 0; index < size; index++) {
                        const positions = getPositions(direction, index);
                        const compact = positions
                            .map((position) => ({ position, value: state[position] }))
                            .filter((entry) => entry.value);
                        const output = [];

                        for (let offset = 0; offset < compact.length; offset++) {
                            if (compact[offset + 1] && compact[offset].value === compact[offset + 1].value) {
                                const value = compact[offset].value * 2;
                                output.push({
                                    value,
                                    sources: [compact[offset], compact[offset + 1]],
                                });
                                gained += value;
                                offset++;
                            } else {
                                output.push({
                                    value: compact[offset].value,
                                    sources: [compact[offset]],
                                });
                            }
                        }

                        output.forEach((entry, offset) => {
                            const target = positions[offset];
                            next[target] = entry.value;

                            if (entry.sources.length > 1) {
                                const source = entry.sources
                                    .filter((item) => item.position !== target)
                                    .pop() ?? entry.sources[0];

                                movements.push({
                                    from: source.position,
                                    to: target,
                                    value: entry.value,
                                    hide: entry.sources.map((item) => item.position),
                                });
                            } else {
                                entry.sources.forEach((source) => {
                                    if (source.position !== target) {
                                        movements.push({
                                            from: source.position,
                                            to: target,
                                            value: source.value,
                                            hide: [source.position],
                                        });
                                    }
                                });
                            }
                        });
                    }

                    return {
                        changed: next.join(',') !== state.join(','),
                        gained,
                        movements,
                        state: next
                    };
                };

                const emptyIndexesFor = (state) => state
                    .map((value, index) => value ? null : index)
                    .filter((index) => index !== null);

                const canMoveState = (state) => {
                    if (emptyIndexesFor(state).length) {
                        return true;
                    }

                    for (let row = 0; row < size; row++) {
                        for (let col = 0; col < size; col++) {
                            const value = state[row * size + col];
                            const right = col < size - 1 ? state[row * size + col + 1] : null;
                            const down = row < size - 1 ? state[(row + 1) * size + col] : null;

                            if (value === right || value === down) {
                                return true;
                            }
                        }
                    }

                    return false;
                };

                const canMove = () => canMoveState(board);

                const move = (direction) => {
                    if (isAnimating) {
                        return false;
                    }

                    const next = previewMove(board, direction);

                    if (! next.changed) {
                        return false;
                    }

                    const gained = next.gained;
                    score += gained;
                    best = Math.max(best, score);
                    window.localStorage.setItem(storageKey, String(best));
                    board = next.state;
                    const spawnedIndex = addTileTo(board);

                    if (! won && board.some((value) => value >= 2048)) {
                        won = true;
                        statusElement.textContent = translate('won');
                    } else if (! canMove()) {
                        statusElement.textContent = translate('movesLeft');
                    } else {
                        statusElement.textContent = gained ? translate('mergedPoints', { points: gained }) : translate('keepSliding');
                    }

                    render(next.movements.map((movement) => movement.to), spawnedIndex);
                    animateMovements(next.movements, () => {
                        render();
                    });

                    return true;
                };

                const evaluateBoard = (state) => {
                    const corners = [0, 3, 12, 15];
                    const snakeWeights = [
                        65536, 32768, 16384, 8192,
                        512, 1024, 2048, 4096,
                        256, 128, 64, 32,
                        2, 4, 8, 16
                    ];
                    const empty = emptyIndexesFor(state).length;
                    const maxTile = Math.max(...state);
                    const cornerBonus = corners.some((index) => state[index] === maxTile) ? maxTile * 8 : 0;
                    const weighted = state.reduce((total, value, index) => total + value * snakeWeights[index], 0);
                    let smoothness = 0;
                    let merges = 0;

                    for (let row = 0; row < size; row++) {
                        for (let col = 0; col < size; col++) {
                            const value = state[row * size + col];

                            if (! value) {
                                continue;
                            }

                            const right = col < size - 1 ? state[row * size + col + 1] : 0;
                            const down = row < size - 1 ? state[(row + 1) * size + col] : 0;

                            if (right) {
                                smoothness -= Math.abs(Math.log2(value) - Math.log2(right));
                            }

                            if (down) {
                                smoothness -= Math.abs(Math.log2(value) - Math.log2(down));
                            }

                            if (value === right) {
                                merges++;
                            }

                            if (value === down) {
                                merges++;
                            }
                        }
                    }

                    return weighted / 256
                        + empty * empty * 900
                        + merges * 700
                        + cornerBonus
                        + smoothness * 80
                        + maxTile * 12;
                };

                const robotSearch = (state, depth, isPlayerTurn) => {
                    if (depth <= 0 || ! canMoveState(state)) {
                        return evaluateBoard(state);
                    }

                    const directions = ['left', 'up', 'right', 'down'];

                    if (isPlayerTurn) {
                        let bestScore = -Infinity;

                        directions.forEach((direction) => {
                            const next = previewMove(state, direction);

                            if (! next.changed) {
                                return;
                            }

                            bestScore = Math.max(bestScore, next.gained * 6 + robotSearch(next.state, depth - 1, false));
                        });

                        return bestScore === -Infinity ? evaluateBoard(state) : bestScore;
                    }

                    const empty = emptyIndexesFor(state);

                    if (! empty.length) {
                        return robotSearch(state, depth - 1, true);
                    }

                    let sampled = empty;

                    if (sampled.length > 6) {
                        sampled = sampled
                            .map((index) => ({ index, risk: evaluateBoard([...state.slice(0, index), 2, ...state.slice(index + 1)]) }))
                            .sort((a, b) => a.risk - b.risk)
                            .slice(0, 6)
                            .map((item) => item.index);
                    }

                    let total = 0;

                    sampled.forEach((index) => {
                        const withTwo = [...state];
                        const withFour = [...state];
                        withTwo[index] = 2;
                        withFour[index] = 4;
                        total += robotSearch(withTwo, depth - 1, true) * .9;
                        total += robotSearch(withFour, depth - 1, true) * .1;
                    });

                    return total / sampled.length;
                };

                const chooseRobotMove = () => {
                    const directions = ['left', 'up', 'right', 'down'];
                    const depth = emptyIndexes().length > 7 ? 3 : 4;
                    let bestMove = null;

                    directions.forEach((direction) => {
                        const next = previewMove(board, direction);

                        if (! next.changed) {
                            return;
                        }

                        const rating = next.gained * 8 + robotSearch(next.state, depth, false);

                        if (! bestMove || rating > bestMove.rating) {
                            bestMove = { direction, rating };
                        }
                    });

                    return bestMove ? bestMove.direction : null;
                };

                const getTileClass = (value) => value >= 128 ? 'tile-high' : `tile-${value}`;

                const getCellMetrics = () => {
                    const styles = window.getComputedStyle(boardElement);
                    const paddingLeft = parseFloat(styles.paddingLeft) || 0;
                    const paddingTop = parseFloat(styles.paddingTop) || 0;
                    const gap = parseFloat(styles.columnGap || styles.gap) || 0;
                    const innerWidth = boardElement.clientWidth - paddingLeft - (parseFloat(styles.paddingRight) || 0);
                    const innerHeight = boardElement.clientHeight - paddingTop - (parseFloat(styles.paddingBottom) || 0);
                    const cellWidth = (innerWidth - gap * (size - 1)) / size;
                    const cellHeight = (innerHeight - gap * (size - 1)) / size;

                    return {
                        cellHeight,
                        cellWidth,
                        gap,
                        paddingLeft,
                        paddingTop,
                    };
                };

                const getCellPosition = (index, metrics) => {
                    const row = Math.floor(index / size);
                    const col = index % size;

                    return {
                        x: metrics.paddingLeft + col * (metrics.cellWidth + metrics.gap),
                        y: metrics.paddingTop + row * (metrics.cellHeight + metrics.gap),
                    };
                };

                const animateMovements = (movements, onComplete) => {
                    if (! movements.length) {
                        onComplete();

                        return;
                    }

                    isAnimating = true;
                    const metrics = getCellMetrics();
                    const ghosts = movements.map((movement) => {
                        const from = getCellPosition(movement.from, metrics);
                        const to = getCellPosition(movement.to, metrics);
                        const ghost = document.createElement('div');
                        ghost.className = `tile-ghost ${getTileClass(movement.value)}`;
                        ghost.textContent = movement.value;
                        ghost.style.left = `${from.x}px`;
                        ghost.style.top = `${from.y}px`;
                        ghost.style.width = `${metrics.cellWidth}px`;
                        ghost.style.height = `${metrics.cellHeight}px`;
                        boardElement.appendChild(ghost);

                        window.requestAnimationFrame(() => {
                            ghost.style.transform = `translate3d(${to.x - from.x}px, ${to.y - from.y}px, 0) scale(.98)`;
                        });

                        return ghost;
                    });

                    window.setTimeout(() => {
                        onComplete();
                        window.requestAnimationFrame(() => {
                            ghosts.forEach((ghost) => ghost.remove());
                            isAnimating = false;
                        });
                    }, 340);
                };

                const render = (hiddenIndexes = [], spawnedIndex = null) => {
                    const hidden = new Set(hiddenIndexes);

                    board.forEach((value, index) => {
                        const className = getTileClass(value);
                        const animationClass = value
                            ? (hidden.has(index) ? ' hidden-during-move' : (index === spawnedIndex ? ' spawned' : ''))
                            : '';
                        if (index === spawnedIndex) {
                            cells[index].className = 'tile';
                            void cells[index].offsetWidth;
                        }
                        cells[index].className = `tile ${className}${animationClass}`;
                        cells[index].textContent = value || '';
                        cells[index].setAttribute('aria-label', value ? translate('tileValue', { number: index + 1, value }) : translate('emptyTile', { number: index + 1 }));
                    });

                    scoreElement.textContent = score;
                    bestElement.textContent = best;
                    robotButton.textContent = robotTimer ? translate('stopRobot') : translate('watchRobot');
                };

                const stopRobot = () => {
                    if (robotTimer) {
                        window.clearInterval(robotTimer);
                        robotTimer = null;
                        statusElement.textContent = translate('paused');
                        render();
                    }
                };

                const toggleRobot = () => {
                    if (robotTimer) {
                        stopRobot();

                        return;
                    }

                    statusElement.textContent = translate('playing');
                    robotTimer = window.setInterval(() => {
                        if (isAnimating) {
                            return;
                        }

                        if (! canMove()) {
                            stopRobot();
                            statusElement.textContent = translate('ranOut');

                            return;
                        }

                        const direction = chooseRobotMove();

                        if (! direction || ! move(direction)) {
                            if (! isAnimating) {
                                stopRobot();
                            }
                        }
                    }, 100);
                    render();
                };

                const reset = () => {
                    stopRobot();
                    board = Array(size * size).fill(0);
                    score = 0;
                    won = false;
                    statusElement.textContent = translate('useArrows');
                    addTile();
                    addTile();
                    render();
                    boardElement.focus({ preventScroll: true });
                };

                resetButton.addEventListener('click', reset);
                robotButton.addEventListener('click', toggleRobot);
                themeToggle.addEventListener('click', toggleTheme);
                refreshButton.addEventListener('click', () => {
                    window.location.reload();
                });
                window.addEventListener('keydown', (event) => {
                    const direction = {
                        ArrowLeft: 'left',
                        ArrowRight: 'right',
                        ArrowUp: 'up',
                        ArrowDown: 'down'
                    }[event.key];

                    if (! direction) {
                        return;
                    }

                    event.preventDefault();
                    stopRobot();
                    move(direction);
                });
                boardElement.addEventListener('touchstart', (event) => {
                    const touch = event.changedTouches[0];
                    touchStart = { x: touch.clientX, y: touch.clientY };
                }, { passive: true });
                boardElement.addEventListener('touchend', (event) => {
                    if (! touchStart) {
                        return;
                    }

                    const touch = event.changedTouches[0];
                    const dx = touch.clientX - touchStart.x;
                    const dy = touch.clientY - touchStart.y;
                    touchStart = null;

                    if (Math.max(Math.abs(dx), Math.abs(dy)) < 24) {
                        return;
                    }

                    stopRobot();
                    move(Math.abs(dx) > Math.abs(dy)
                        ? (dx > 0 ? 'right' : 'left')
                        : (dy > 0 ? 'down' : 'up'));
                }, { passive: true });

                const checkMaintenanceStatus = () => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('maintenance_status', '1');
                    url.searchParams.set('_', Date.now().toString());

                    window.fetch(url.toString(), {
                        cache: 'no-store',
                        headers: {
                            Accept: 'application/json',
                        },
                    }).then((response) => {
                        readyAlert.classList.toggle('visible', response.status !== 503);
                    }).catch(() => {
                        /*
                         * Network checks are intentionally quiet.
                         */
                    });
                };

                createCells();
                applyThemeLabel();
                reset();
                window.setInterval(checkMaintenanceStatus, 15000);
                window.setTimeout(checkMaintenanceStatus, 3000);
            })();
        </script>
    </body>
</html>
