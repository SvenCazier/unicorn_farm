#!/bin/bash

# Drop database if exists
echo "Banishing the old unicorn realm..."
php bin/console doctrine:database:drop --force --if-exists

# Create database
echo "Simulating unicorn magic..."
php bin/console doctrine:database:create

# Update schema
echo "Sprinkling fairy dust on the database..."
php bin/console doctrine:schema:update --force

# Load fixtures
echo "Gathering enchanted tales..."
php bin/console doctrine:fixtures:load --no-interaction

echo "Unicorns are now frolicking happily in the database!"