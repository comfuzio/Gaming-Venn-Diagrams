<?php
// Game List Viewer - Venn Diagram Style
// Compatible with PHP 8.4+

$games_i_like = file_exists('games_i_like.txt') ? file('games_i_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_i_dislike = file_exists('games_i_dislike.txt') ? file('games_i_dislike.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_others_like = file_exists('games_others_like.txt') ? file('games_others_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// Clean up whitespace
$games_i_like = array_filter(array_map('trim', $games_i_like));
$games_i_dislike = array_filter(array_map('trim', $games_i_dislike));
$games_others_like = array_filter(array_map('trim', $games_others_like));
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Preferences Venn</title>
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
            justify-content:center;
            padding:20px;
        }

        h1 { margin-bottom: 20px; text-align:center; }
        .subtitle { margin-bottom: 30px; text-align:center; font-size:0.9rem; color:#9ca3af; }

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

        .circle h2 { font-size: 1rem; margin-bottom: 8px; }
        .circle ul { list-style: none; font-size: 0.8rem; }
        .circle ul li { margin-bottom: 4px; }

        .circle-like { top: 20%; left: 5%; background: rgba(16,185,129,0.35); border: 2px solid rgba(16,185,129,0.8); }
        .circle-others { top: 20%; right: 5%; background: rgba(59,130,246,0.35); border: 2px solid rgba(59,130,246,0.8); }
        .circle-dislike { bottom: 5%; left: 50%; transform: translateX(-50%); background: rgba(239,68,68,0.35); border: 2px solid rgba(239,68,68,0.8); }

        .footer { margin-top: 20px; font-size: 0.8rem; color:#9ca3af; text-align:center; }

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
</body>
</html>
