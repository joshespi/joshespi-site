#!/usr/bin/env bash
# Sourced by backup.sh and restore.sh — do not execute directly.

load_env() {
    local env_file="$ROOT_DIR/.env"
    if [ ! -f "$env_file" ]; then
        echo "ERROR: .env not found in $ROOT_DIR" >&2
        exit 1
    fi
    set -a
    # shellcheck disable=SC1090
    . "$env_file"
    set +a
}
