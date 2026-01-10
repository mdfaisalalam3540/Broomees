#!/bin/bash

echo "🚀 Setting up test environment..."

# Create test database using XAMPP MySQL
mysql -u root -pAafzaf123@44 --socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock -e "CREATE DATABASE IF NOT EXISTS broomies_test;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Test database created successfully"
else
    echo "⚠️  Could not create test database. Make sure MySQL is running."
    echo "   Start XAMPP MySQL with: sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start"
    exit 1
fi

echo "📦 Running migrations..."
php artisan migrate:fresh --env=testing

echo "🧪 Running tests..."

echo ""
echo "1. Reputation Score Calculation Tests..."
php artisan test --filter ReputationServiceTest

echo ""
echo "2. Rate Limiting Logic Tests..."
php artisan test --filter RateLimitTest

echo ""
echo "3. Optimistic Locking Conflict Tests..."
php artisan test --filter OptimisticLockingTest

echo ""
echo "4. Relationship Concurrency Tests..."
php artisan test --filter RelationshipConcurrencyTest

echo ""
echo "🎯 Summary: All mandatory tests completed!"
