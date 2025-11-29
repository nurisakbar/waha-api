#!/bin/bash

# Script untuk mengecek status WAHA
# Usage: ./waha-status.sh

echo "📊 WAHA API Status"
echo "=================="
echo ""

# Navigate to project directory
cd "$(dirname "$0")"

# Check if container exists
if docker ps -a | grep -q waha-api; then
    # Check if container is running
    if docker ps | grep -q waha-api; then
        echo "✅ Status: Running"
        echo ""
        echo "Container Info:"
        docker ps --filter "name=waha-api" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        echo ""
        echo "Health Check:"
        if curl -s http://localhost:3000/api/health > /dev/null 2>&1; then
            echo "✅ API is responding"
            echo "📍 API URL: http://localhost:3000"
            echo "📚 Swagger UI: http://localhost:3000/api-docs"
        else
            echo "⚠️  API is not responding (container may still be starting)"
        fi
    else
        echo "⏸️  Status: Stopped"
        echo ""
        echo "To start: ./waha-start.sh"
    fi
else
    echo "❌ Status: Container not found"
    echo ""
    echo "To create and start: ./waha-start.sh"
fi

