#!/bin/sh
set -e

DISPLAY=:0
export DISPLAY

Xvfb :0 -screen 0 "${SCREEN_WIDTH}x${SCREEN_HEIGHT}x24" &

fluxbox &

x11vnc -display :0 -forever -shared -nopw -rfbport 5900 -bg

websockify --web=/usr/share/novnc 0.0.0.0:6080 localhost:5900 &

/opt/basilisk/basilisk "$GAME_URL" &

wait
