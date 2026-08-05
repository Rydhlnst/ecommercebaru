#!/bin/bash
set -e

echo "==> Pulling latest code..."
git pull

echo "==> Rebuilding Docker image (no cache)..."
docker compose build --no-cache app

echo "==> Restarting containers..."
docker compose up -d

echo "==> Done. Site should be live."
