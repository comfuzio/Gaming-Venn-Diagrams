<?php
// 1. SETUP & DATA LOADING
// ---------------------------------------------------------
$files = [
    'me'      => 'games_i_like.txt',
    'friends' => 'games_friends_like.txt',
    'hate'    => 'games_i_dislike.txt'
];

function get_clean_list($path) {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_values(array_unique(array_map('trim', $lines)));
}

$raw_me = get_clean_list($files['me']);
$raw_friends = get_clean_list($files['friends']);
$raw_hate = get_clean_list($files['hate']);

// Map for case-insensitive comparison
function map_list($arr) {
    $map = [];
    foreach ($arr as $item) $map[mb_strtolower($item)] = $item;
    return $map;
}

$map_me = map_list($raw_me);
$map_friends = map_list($raw_friends);
$map_hate = map_list($raw_hate);

// 2. LOGIC PROCESSING
// ---------------------------------------------------------

// A. Clean "Me" list (Remove anything I hate from my own list, just in case)
foreach ($map_hate as $k => $v) {
    if (isset($map_me[$k])) unset($map_me[$k]);
}

// B. Calculate "Mutual" (Me + Friends)
$mutual = [];
foreach ($map_me as $k => $v) {
    if (isset($map_friends[$k])) {
        $mutual[] = $v;
        unset($map_me[$k]);
        unset($map_friends[$k]);
    }
}

// C. Calculate "Conflict" (Friends + Hate)
// "Games you like but I dislike"
$conflict = [];
foreach ($map_hate as $k => $v) {
    if (isset($map_friends[$k])) {
        $conflict[] = $v;
        unset($map_hate[$k]); // Remove from pure hate
        unset($map_friends[$k]); // Remove from pure friends (if not already gone)
    }
}

// 3. FINALIZE ARRAYS
$list_me = array_values($map_me);         // "Games I enjoy..."
$list_mutual = array_values($mutual);     // "Games we can all play..."
$list_friends = array_values($map_friends);// "Games you like but I haven't checked..."
$list_conflict = array_values($conflict); // "Games you like but I dislike"
$list_hate = array_values($map_hate);     // "Games I absolutely dislike..."

// Sort
sort($list_me, SORT_NATURAL | SORT_FLAG_CASE);
sort($list_mutual, SORT_NATURAL | SORT_FLAG_CASE);
sort($list_friends, SORT_NATURAL | SORT_FLAG_CASE);
sort($list_conflict, SORT_NATURAL | SORT_FLAG_CASE);
sort($list_hate, SORT_NATURAL | SORT_FLAG_CASE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Compatibility Venn</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --text-main: #e6edf3;
            --text-sub: #8b949e;
            
            /* Venn Colors */
            --c-me: rgba(46, 160, 67, 0.6);      /* Green with opacity */
            --c-friends: rgba(56, 139, 253, 0.6); /* Blue with opacity */
            --c-overlap: rgba(46, 160, 67, 0.2);  /* Fallback color */
            
            /* Warning Colors */
            --c-conflict: rgba(210, 153, 34, 0.15);
            --b-conflict: #d29922;
            
            --c-hate: rgba(248, 81, 73, 0.15);
            --b-hate: #f85149;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 { margin-bottom: 50px; text-transform: uppercase; letter-spacing: 2px; font-weight: 300; }
        h2 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; color: var(--text-sub); min-height: 35px; display:flex; align-items:center; justify-content:center;}
        
        ul { list-style: none; padding: 0; margin: 0; width: 100%; }
        li { padding: 6px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; text-align: center; }
        li:last-child { border-bottom: none; }
        li.empty { font-style: italic; opacity: 0.5; font-size: 0.85rem; }

        /* --- THE VENN SECTION --- */
        .venn-wrapper {
            display: flex;
            justify-content: center;
            align-items: stretch; /* Stretch to same height */
            width: 100%;
            max-width: 1200px;
            margin-bottom: 80px;
            position: relative;
        }

        .venn-circle {
            flex: 1;
            min-width: 300px;
            border-radius: 50%; /* Make them circles visually */
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            /* This blend mode creates the real color mixing effect */
            background-blend-mode: screen; 
            transition: transform 0.3s ease;
        }

        /* 1. LEFT: ME */
        .circle-me {
            background-color: var(--c-me);
            box-shadow: 0 0 50px var(--c-me);
            margin-right: -60px; /* Force overlap */
            z-index: 1;
            border: 2px solid rgba(255,255,255,0.1);
        }

        /* 2. MIDDLE: MUTUAL */
        /* We fake the lens shape by using a rectangle that sits on top */
        .circle-mutual {
            flex: 0.8; /* Slightly narrower */
            z-index: 10; /* On top */
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px); /* Blurs the layers behind it */
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
            margin-top: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        /* 3. RIGHT: FRIENDS */
        .circle-friends {
            background-color: var(--c-friends);
            box-shadow: 0 0 50px var(--c-friends);
            margin-left: -60px; /* Force overlap */
            z-index: 1;
            border: 2px solid rgba(255,255,255,0.1);
        }
        
        /* Titles specific colors */
        .circle-me h2 { color: #7ee787; }
        .circle-mutual h2 { color: #fff; text-shadow: 0 0 10px rgba(255,255,255,0.5); }
        .circle-friends h2 { color: #79c0ff; }


        /* --- THE WARNING SECTION --- */
        .warning-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            width: 100%;
            max-width: 1000px;
            margin-top: 20px;
        }

        .warn-box {
            background: #161b22;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #30363d;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .box-conflict { border-top: 4px solid var(--b-conflict); background: linear-gradient(180deg, var(--c-conflict) 0%, transparent 100%); }
        .box-conflict h2 { color: var(--b-conflict); }

        .box-hate { border-top: 4px solid var(--b-hate); background: linear-gradient(180deg, var(--c-hate) 0%, transparent 100%); }
        .box-hate h2 { color: var(--b-hate); }

        /* SCROLLBARS FOR LISTS */
        .list-container {
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .list-container::-webkit-scrollbar { width: 6px; }
        .list-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .venn-wrapper { flex-direction: column; align-items: center; margin-bottom: 40px; }
            .venn-circle { width: 100%; margin: 0; border-radius: 20px; margin-bottom: 20px; }
            .circle-mutual { margin: 0 0 20px 0; z-index: 1; }
            .warning-section { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <h1>Gaming Compatibility Engine</h1>

    <div class="venn-wrapper">
        
        <div class="venn-circle circle-me">
            <h2>Games I enjoy and can play</h2>
            <div class="list-container">
                <ul>
                    <?php if(empty($list_me)) echo "<li class='empty'>No unique games</li>"; ?>
                    <?php foreach($list_me as $g) echo "<li>$g</li>"; ?>
                </ul>
            </div>
        </div>

        <div class="venn-circle circle-mutual">
            <h2>Games we can all play and enjoy together</h2>
            <div class="list-container">
                <ul>
                    <?php if(empty($list_mutual)) echo "<li class='empty'>No matches found</li>"; ?>
                    <?php foreach($list_mutual as $g) echo "<li><strong>$g</strong></li>"; ?>
                </ul>
            </div>
        </div>

        <div class="venn-circle circle-friends">
            <h2>Games you like but I haven't checked yet</h2>
            <div class="list-container">
                <ul>
                    <?php if(empty($list_friends)) echo "<li class='empty'>No unique games</li>"; ?>
                    <?php foreach($list_friends as $g) echo "<li>$g</li>"; ?>
                </ul>
            </div>
        </div>

    </div>

    <div class="warning-section">
        
        <div class="warn-box box-conflict">
            <h2>Games you like but I dislike</h2>
            <div class="list-container">
                <ul>
                    <?php if(empty($list_conflict)) echo "<li class='empty'>Peaceful gaming...</li>"; ?>
                    <?php foreach($list_conflict as $g) echo "<li>$g</li>"; ?>
                </ul>
            </div>
        </div>

        <div class="warn-box box-hate">
            <h2>Games I absolutely dislike and don't want to play</h2>
            <div class="list-container">
                <ul>
                    <?php if(empty($list_hate)) echo "<li class='empty'>No hated games</li>"; ?>
                    <?php foreach($list_hate as $g) echo "<li>$g</li>"; ?>
                </ul>
            </div>
        </div>

    </div>

</body>
</html>
