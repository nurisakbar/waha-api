#!/bin/bash

# Script untuk menghentikan WAHA via Docker Compose
# Usage: ./waha-stop.sh

echo "🛑 Stopping WAHA API..."

# Navigate to project directory
cd "$(dirname "$0")"

# Stop WAHA container
if docker compose version &> /dev/null; then
    docker compose stop waha
else
    docker-compose stop waha
fi

echo "✅ WAHA API stopped."

