#!/bin/bash

# Script untuk restart WAHA via Docker Compose
# Usage: ./waha-restart.sh

echo "🔄 Restarting WAHA API..."

# Navigate to project directory
cd "$(dirname "$0")"

# Restart WAHA container
if docker compose version &> /dev/null; then
    docker compose restart waha
else
    docker-compose restart waha
fi

sleep 2

# Check if container is running
if docker ps | grep -q waha-api; then
    echo "✅ WAHA API restarted successfully!"
    echo "📍 API URL: http://localhost:3000"
else
    echo "❌ Error: WAHA container failed to restart."
    echo "Check logs with: docker logs waha-api"
    exit 1
fi

