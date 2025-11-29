#!/bin/bash

# Script untuk menjalankan WAHA via Docker Compose
# Usage: ./waha-start.sh

echo "🚀 Starting WAHA API via Docker Compose..."

# Check if docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Error: docker-compose is not installed."
    exit 1
fi

# Navigate to project directory
cd "$(dirname "$0")"

# Start WAHA container
if docker compose version &> /dev/null; then
    docker compose up -d waha
else
    docker-compose up -d waha
fi

# Wait a few seconds for container to start
sleep 3

# Check if container is running
if docker ps | grep -q waha-api; then
    echo "✅ WAHA API is running!"
    echo "📍 API URL: http://localhost:3000"
    echo "📚 Swagger UI: http://localhost:3000/api-docs"
    echo ""
    echo "To view logs: docker logs -f waha-api"
    echo "To stop: ./waha-stop.sh"
else
    echo "❌ Error: WAHA container failed to start."
    echo "Check logs with: docker logs waha-api"
    exit 1
fi

