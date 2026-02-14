FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive
ENV SCREEN_WIDTH=1280
ENV SCREEN_HEIGHT=720
ENV GAME_URL=http://fv-replowed/game
ENV MOZ_PLUGIN_PATH=/usr/lib/mozilla/plugins

RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    xz-utils \
    tar \
    unzip \
    xvfb \
    fluxbox \
    x11vnc \
    novnc \
    websockify \
    dbus-x11 \
    libgtk-3-0 \
    libgtk2.0-0 \
    libdbus-glib-1-2 \
    libnss3 \
    libxss1 \
    libxt6 \
    libasound2 \
    libxrender1 \
    libxi6 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libxcursor1 \
    libxfixes3 \
    libxext6 \
    libx11-6 \
    libglib2.0-0 \
    libpango-1.0-0 \
    libcairo2 \
    libatk1.0-0 \
    libgdk-pixbuf2.0-0 \
    fonts-dejavu \
    && rm -rf /var/lib/apt/lists/*

ARG BASILISK_LINK_BASE=https://dl.basilisk-browser.org
ARG BASILISK_LINK_FILE=basilisk-20260122233106.linux-x86_64-gtk3.tar.xz

ARG FLASH_LINK_BASE=https://github.com/darktohka/clean-flash-builds/releases/download/v1.7
ARG FLASH_LINK_FILE=flash_player_patched_npapi_linux.x86_64.tar.gz

RUN mkdir -p /opt/basilisk /usr/lib/mozilla/plugins

RUN curl -fsSL "$BASILISK_LINK_BASE/$BASILISK_LINK_FILE" -o /tmp/basilisk.tar.xz \
    && tar -xJf /tmp/basilisk.tar.xz -C /opt/basilisk --strip-components=1 \
    && rm /tmp/basilisk.tar.xz

RUN curl -fsSL "$FLASH_LINK_BASE/$FLASH_LINK_FILE" -o /tmp/flash.tar.gz \
    && tar -xzf /tmp/flash.tar.gz -C /usr/lib/mozilla/plugins libflashplayer.so \
    && rm /tmp/flash.tar.gz

COPY docker/flash/start.sh /usr/local/bin/start-flash.sh
RUN chmod +x /usr/local/bin/start-flash.sh

EXPOSE 6080
CMD ["/usr/local/bin/start-flash.sh"]
