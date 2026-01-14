<?php
// Game List Viewer - Display games from three categories
// Compatible with PHP 8.4+

$games_i_like = file_exists('games_i_like.txt') ? file('games_i_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_i_dislike = file_exists('games_i_dislike.txt') ? file('games_i_dislike.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$games_others_like = file_exists('games_others_like.txt') ? file('games_others_like.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// Remove empty entries and trim whitespace
$games_i_like = array_filter(array_map('trim', $games_i_like));
$games_i_dislike = array_filter(array_map('trim', $games_i_dislike));
$games_others_like = array_filter(array_map('trim', $games_others_like));

// Sort alphabetically
sort($games_i_like, SORT_NATURAL | SORT_FLAG_CASE);
sort($games_i_dislike, SORT_NATURAL | SORT_FLAG_CASE);
sort($games_others_like, SORT_NATURAL | SORT_FLAG_CASE);
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Preferences</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h2 {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid;
            font-size: 1.5em;
        }
        
        .card.like h2 {
            color: #10b981;
            border-color: #10b981;
        }
        
        .card.dislike h2 {
            color: #ef4444;
            border-color: #ef4444;
        }
        
        .card.others h2 {
            color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .game-list {
            list-style: none;
        }
        
        .game-list li {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .card.like .game-list li {
            background: #d1fae5;
            border-left: 4px solid #10b981;
        }
        
        .card.dislike .game-list li {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
        }
        
        .card.others .game-list li {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
        }
        
        .game-list li:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .count {
            display: inline-block;
            background: rgba(0,0,0,0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-left: 10px;
        }
        
        .empty-message {
            color: #6b7280;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        .footer {
            text-align: center;
            color: white;
            margin-top: 30px;
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Game Preferences</h1>
        
        <div class="grid">
            <!-- Games I Like -->
            <div class="card like">
                <h2>✅ Games I Like <span class="count"><?= count($games_i_like) ?></span></h2>
                <?php if (empty($games_i_like)): ?>
                    <p class="empty-message">No games added yet</p>
                <?php else: ?>
                    <ul class="game-list">
                        <?php foreach ($games_i_like as $game): ?>
                            <li><?= htmlspecialchars($game) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Games I Dislike -->
            <div class="card dislike">
                <h2>❌ Games I Dislike <span class="count"><?= count($games_i_dislike) ?></span></h2>
                <?php if (empty($games_i_dislike)): ?>
                    <p class="empty-message">No games added yet</p>
                <?php else: ?>
                    <ul class="game-list">
                        <?php foreach ($games_i_dislike as $game): ?>
                            <li><?= htmlspecialchars($game) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Games Others Like -->
            <div class="card others">
                <h2>👥 Games Others Like <span class="count"><?= count($games_others_like) ?></span></h2>
                <?php if (empty($games_others_like)): ?>
                    <p class="empty-message">No games added yet</p>
                <?php else: ?>
                    <ul class="game-list">
                        <?php foreach ($games_others_like as $game): ?>
                            <li><?= htmlspecialchars($game) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>📝 Edit the .txt files to update the lists</p>
            <p style="margin-top: 10px; font-size: 0.9em;">games_i_like.txt | games_i_dislike.txt | games_others_like.txt</p>
        </div>
    </div>
</body>
</html>
