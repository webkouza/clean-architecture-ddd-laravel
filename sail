#!/usr/bin/env bash

# Laravel Sail wrapper script
# This allows you to run "sail" instead of "./vendor/bin/sail"

UNAMEOUT="$(uname -s)"

# Verify operating system is supported...
case "${UNAMEOUT}" in
    Linux*)             MACHINE=linux;;
    Darwin*)            MACHINE=mac;;
    *)                  MACHINE="UNKNOWN"
esac

if [ "$MACHINE" == "UNKNOWN" ]; then
    echo "Unsupported operating system [$(uname -s)]. Laravel Sail supports macOS, Linux, and Windows (WSL2)." >&2
    exit 1
fi

# Define environment variables...
export APP_PORT=${APP_PORT:-80}
export APP_SERVICE=${APP_SERVICE:-"laravel.test"}
export DB_PORT=${DB_PORT:-3306}
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

# Execute the original Sail script
exec ./vendor/bin/sail "$@"
