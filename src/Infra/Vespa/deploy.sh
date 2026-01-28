#!/bin/bash
set -e

echo "Waiting for Vespa config server..."
until curl -s http://localhost:19071/state/v1/health | grep -q '"UP"'; do
    sleep 2
done

echo "Deploying application package..."
vespa-deploy prepare /app/application
vespa-deploy activate

echo "Waiting for application to be ready..."
until curl -s http://localhost:8080/state/v1/health | grep -q '"UP"'; do
    sleep 2
done

echo "Vespa deployment complete!"
