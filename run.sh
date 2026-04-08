#!/usr/bin/env bash

set -e

if ! command -v docker >/dev/null 2>&1; then
  echo "Docker is not installed or not available in PATH."
  exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
  echo "'docker compose' (Compose v2 plugin) is required."
  echo "Please install/upgrade Docker Compose plugin and try again."
  exit 1
fi

# Ensure we start from a clean state.
docker compose down --remove-orphans >/dev/null 2>&1 || true
docker compose up --build
