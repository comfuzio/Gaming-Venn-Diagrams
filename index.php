<?php
// Game Preference Venn Viewer
// Reads three TXT files, enforces Like vs Dislike consistency,
// and visualizes them in a Venn-style layout.

// Load lists from text files (one title per line)
$games_i_like = file_exists('games_i_like.txt') ? file('games_i_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_i_dislike = file_exists('games_i_dislike.txt') ? file('games_i_dislike.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_others_like = file_exists('games_others_like.txt') ? file('games_others_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// Normalize: trim, remove empties
$games_i_like      = array_filter(array_map('trim', $games_i_like));
$games_i_dislike   = array_filter(array_map('trim', $games_i_dislike));
$games_others_like = array_filter(array_map('trim', $games_others_like));

// Remove exact duplicates inside each list
$games_i_like      = array_values(array_unique($games_i_like));
$games_i_dislike   = array_values(array_unique($games_i_dislike));
$games_others_like = array_values(array_unique($games_others_like));

// Detect conflicts: same title in Like & Dislike (case-insensitive)
$dislike_map = [];
foreach ($games_i_dislike as $title) {
    // keep original casing as value
    $dislike_map[mb_strtolower($title)] = $title;
}

$conflicts = [];
foreach ($games_i_like as $title) {
    $key = mb_strtolower($title);
    if (isset($dislike_map[$key])) {
        $conflicts[] = $title;
    }
}

// Apply rule: if a title exists in both Like and Dislike,
// keep it only in Dislike, remove from Like, but show warning.
if (!empty($conflicts)) {
    $conflict_lookup = [];
    foreach ($conflicts as $c) {
        $conflict_lookup[mb_strtolower($c)] = true;
    }

    $games_i_like = array_values(array_filter($games_i_like, function ($title) use ($conflict_lookup) {
        return !isset($conflict_lookup[mb_strtolower($title)]);
    }));
}

// Sort lists alphabetically in human-friendly, case-insensitive way
sort($games_i_like, SORT_NATURAL | SORT_FLAG_CASE);
sort($games_i_dislike, SORT_NATURAL | SORT_FLAG_CASE);
sort($games_others_like, SORT_NATURAL | SORT_FLAG_CASE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Preference Venn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            min-height: 100vh;
            display:flex;
            flex-direction:column;
            align-items:center;
            padding:20px;
        }

        h1 {
            margin-bottom: 10px;
            text-align:center;
        }

        .subtitle {
            margin-bottom: 30px;
            text-align:center;
            font-size:0.9rem;
            color:#9ca3af;
        }

        .venn-wrapper {
            position: relative;
            width: 600px;
            max-width: 100%;
            aspect-ratio: 1 / 1;
        }

        .circle {
            position: absolute;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            padding: 20px;
            overflow: auto;
            backdrop-filter: blur(4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }

        .circle h2 {
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .circle ul {
            list-style: none;
            font-size: 0.8rem;
        }

        .circle ul li {
            margin-bottom: 4px;
        }

        /* Left circle – I Like */
        .circle-like {
            top: 20%;
            left: 5%;
            background: rgba(16, 185, 129, 0.35); /* green */
            border: 2px solid rgba(16,185,129,0.8);
        }

        /* Right circle – Others Like */
        .circle-others {
            top: 20%;
            right: 5%;
            background: rgba(59, 130, 246, 0.35); /* blue */
            border: 2px solid rgba(59,130,246,0.8);
        }

        /* Bottom circle – Dislike */
        .circle-dislike {
            bottom: 5%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(239, 68, 68, 0.35); /* red */
            border: 2px solid rgba(239,68,68,0.8);
        }

        .footer {
            margin-top: 20px;
            font-size: 0.8rem;
            color:#9ca3af;
            text-align:center;
        }

        .conflict-alert {
            margin-top: 24px;
            max-width: 700px;
            padding: 16px 20px;
            border-radius: 10px;
            background: rgba(248, 113, 113, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.6);
            color: #fecaca;
            font-size: 0.9rem;
        }

        .conflict-alert ul {
            margin-top: 8px;
            margin-left: 18px;
        }

        .conflict-alert li {
            margin-bottom: 3px;
        }

        @media (max-width: 640px) {
            .circle { padding: 12px; }
            .circle h2 { font-size: 0.9rem; }
            .circle ul { font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    <h1>🎮 Game Preference Venn</h1>
    <p class="subtitle">
        Green: games you like – Blue: games others like – Red: games you dislike
    </p>

    <div class="venn-wrapper">
        <!-- Games I Like -->
        <div class="circle circle-like">
            <h2>✅ I Like (<?= count($games_i_like) ?>)</h2>
            <?php if (empty($games_i_like)): ?>
                <ul><li><em>No games added</em></li></ul>
            <?php else: ?>
                <ul>
                    <?php foreach ($games_i_like as $game): ?>
                        <li><?= htmlspecialchars($game) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Games Others Like -->
        <div class="circle circle-others">
            <h2>👥 Others Like (<?= count($games_others_like) ?>)</h2>
            <?php if (empty($games_others_like)): ?>
                <ul><li><em>No games added</em></li></ul>
            <?php else: ?>
                <ul>
                    <?php foreach ($games_others_like as $game): ?>
                        <li><?= htmlspecialchars($game) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Games I Dislike -->
        <div class="circle circle-dislike">
            <h2>❌ I Dislike (<?= count($games_i_dislike) ?>)</h2>
            <?php if (empty($games_i_dislike)): ?>
                <ul><li><em>No games added</em></li></ul>
            <?php else: ?>
                <ul>
                    <?php foreach ($games_i_dislike as $game): ?>
                        <li><?= htmlspecialchars($game) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        Edit: games_i_like.txt · games_i_dislike.txt · games_others_like.txt
    </div>

    <?php if (!empty($conflicts)): ?>
        <div class="conflict-alert">
            <strong>⚠ Inconsistent entries detected</strong>
            <p>
                The following titles exist in both <em>Like</em> and <em>Dislike</em> lists.
                They were kept only in <em>Dislike</em>. Please fix your .txt files:
            </p>
            <ul>
                <?php foreach ($conflicts as $c): ?>
                    <li><?= htmlspecialchars($c) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</body>
</html>
