# 🎮 Game Preference Firewall

Tired of “friends” nagging you to play games you absolutely hate? This tiny PHP app lets you publish three simple text lists: games you like, games you dislike (hard block list) and games others like. Host it on your own server and just send them the link.

## Features

- Three clear categories: Like, Dislike (block list), Others like
- Simple text files, one game per line, no database
- Clean responsive UI, works great on mobile and desktop
- Safe to put behind reverse proxies (e.g. Nginx Proxy Manager)
- Auto-sorted lists with live view on every refresh

## How it works

The app reads three `.txt` files in the same directory as `index.php`:

- `games_i_like.txt`
- `games_i_dislike.txt`
- `games_others_like.txt`

Each non-empty line is treated as one game title and is shown in the corresponding column.

## Installation

1. Copy `index.php` to a directory served by your web server (e.g. `public_html/games/` on myVestaCP).
2. In the same directory create the three list files:

   ```bash
   touch games_i_like.txt
   touch games_i_dislike.txt
   touch games_others_like.txt
   chmod 644 *.txt index.php
