#!/bin/sh
set -e

DISPLAY=:0
export DISPLAY

# Clean up any stale X locks from prior crashes.
rm -f /tmp/.X0-lock /tmp/.X11-unix/X0

# Start a fresh Xvfb session.
Xvfb :0 -screen 0 "${SCREEN_WIDTH}x${SCREEN_HEIGHT}x24" -ac -nolisten tcp -noreset &

# Create an Xauthority cookie so x11vnc can attach.
XAUTH=/root/.Xauthority
touch "$XAUTH"
xauth -f "$XAUTH" add :0 . "$(mcookie)"
export XAUTHORITY="$XAUTH"

fluxbox &

# Allow x11vnc to find the X auth cookie automatically.
x11vnc -display :0 -auth "$XAUTH" -forever -shared -nopw -rfbport 5900 -bg

websockify --web=/usr/share/novnc 0.0.0.0:6080 localhost:5900 &

PROFILE=/root/.basilisk-dev/basilisk/fvprofile
mkdir -p "$PROFILE"
cat > "$PROFILE/user.js" <<'EOF'
user_pref("plugin.state.flash", 2);
user_pref("plugin.default.state", 2);
user_pref("plugins.click_to_play", false);
EOF

/opt/basilisk/basilisk -profile "$PROFILE" "$GAME_URL" &

wait
