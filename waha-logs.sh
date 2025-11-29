#!/bin/bash

# Script untuk melihat logs WAHA
# Usage: ./waha-logs.sh [--follow]

echo "📋 WAHA API Logs..."

# Navigate to project directory
cd "$(dirname "$0")"

# Check if --follow flag is provided
if [ "$1" == "--follow" ] || [ "$1" == "-f" ]; then
    echo "Following logs (Press Ctrl+C to exit)..."
    docker logs -f waha-api
else
    docker logs --tail 100 waha-api
fi

