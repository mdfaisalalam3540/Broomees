<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).












---
---









  # 🧹 Broomees Backend API

  **Broomees** is a backend-only RESTful API built using **Laravel (PHP)**.
  It manages users, relationships, hobbies, and a reputation scoring system
  while following production-grade backend engineering practices.

  ---

  ## ✨ Features

  - Token-based Authentication
  - Optimistic Locking for concurrency handling
  - API Rate Limiting
  - Reputation Score System
  - PHPUnit Test Coverage
  - Clean Layered Architecture
  - Secure and scalable backend design

  ---

  ## 🚀 Quick Start

  ### Prerequisites

  | Tool | Version |
  |-----|--------|
  |PHP | >= 8.0 |
  | Composer | Latest |
  | MySQL | >= 5.7 |
  | Git | Installed |

  ---

  ### Installation

  #### Clone Repository

      git clone https://github.com/yourusername/broomies-backend.git
      cd broomies-backend

  #### Install Dependencies

      composer install

  #### Environment Setup

      cp .env.example .env
      php artisan key:generate

  Environment Variables:

      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=broomies
      DB_USERNAME=root
      DB_PASSWORD=

  #### Run Migrations

      php artisan migrate

  #### Start Server

      php artisan serve

  ---

  ## 🏗️ Architecture

  ### Folder Structure

      app/
      ├── Http/
      │   ├── Controllers/Api/
      │   └── Middleware/
      ├── Models/
      ├── Services/
      └── Providers/

      database/
      ├── migrations/
      └── seeders/

  ### Layered Architecture

  | Layer | Location | Responsibility |
  |------|----------|----------------|
  | Controllers | app/Http/Controllers | Handle HTTP requests |
  | Services | app/Services | Business logic |
  | Models | app/Models | Database entities |
  | Middleware | app/Http/Middleware | Authentication, rate limiting |

  ---

  ## 🔐 Authentication

  - Token-based authentication system
  - Token sent in request header:

      Authorization: Bearer <token>

  ### Token Security

  - Tokens are hashed
  - Token expiration supported
  - Token revocation available

  ---

  ## 🚦 Rate Limiting

  | Method | Limit | Window |
  |--------|-------|--------|
  | GET | 120 | per minute |
  | POST | 30 | per minute |
  | PUT | 30 | per minute |
  | DELETE | 30 | per minute |

  - Per-token throttling
  - IP-based fallback
  - HTTP 429 returned on limit exceed

  ---

  ## ⚡ Concurrency Handling

  ### Optimistic Locking

      Each record has a version field
      Client must send the current version
      Conflict returns HTTP 409

  - Atomic database transactions
  - Unique database constraints

  ---

  ## 📊 Reputation Score System

  ### Formula

      reputationScore =
        uniqueFriends
        + (sharedHobbies × 0.5)
        + min(accountAgeInDays ÷ 30, 3)
        - blockedUsers

  ---

  ## 🧪 Testing

  ### Setup

      cp .env.testing.example .env.testing
      php artisan key:generate --env=testing
      php artisan migrate:fresh --env=testing
      php artisan test

  ---

 ## 📖 API Reference

  ### Base URL

      http://localhost:8000/api

  ---

  ## 🔓 Public APIs (No Authentication Required)

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | POST | http://localhost:8000/api/auth/register | Register a new user |
  | POST | http://localhost:8000/api/auth/token | Issue access token |

  ---

  ## 🔐 Protected APIs  
  *(Requires Authorization: Bearer <token>)*  
  *(Rate limited + authenticated)*

  ---

  ### 👤 User APIs

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | GET | http://localhost:8000/api/users | List all users |
  | GET | http://localhost:8000/api/users/{id} | Get user by ID |
  | POST | http://localhost:8000/api/users | Create user |
  | PUT | http://localhost:8000/api/users/{id} | Update user (optimistic locking) |
  | DELETE | http://localhost:8000/api/users/{id} | Delete user |

  ---

  ### 🤝 Relationship APIs

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | POST | http://localhost:8000/api/users/{id}/relationships | Add relationship |
  | DELETE | http://localhost:8000/api/users/{id}/relationships | Remove relationship |

  ---

  ### 🎯 Hobby APIs

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | POST | http://localhost:8000/api/users/{id}/hobbies | Add hobby to user |
  | DELETE | http://localhost:8000/api/users/{id}/hobbies | Remove hobby from user |

  ---

  ### 📊 Metrics APIs

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | GET | http://localhost:8000/api/metrics/reputation | Get reputation metrics |

  ---

  ### 🔑 Token Management

  | Method | Endpoint | Description |
  |------|---------|-------------|
  | POST | http://localhost:8000/api/auth/revoke | Revoke access token |

  ---

  ## 🗄️ Database Schema

 readme: |
  ## 📦 Data Models

  ### 👤 User

      {
        "id": "uuid",
        "username": "string (unique)",
        "password": "string",
        "age": "integer",
        "reputationScore": "float",
        "createdAt": "datetime",
        "updatedAt": "datetime",
        "version": "integer (optimistic locking)"
      }

  ---

  ### 🤝 Relationship

      {
        "user_id": "uuid",
        "friend_id": "uuid",
        "createdAt": "datetime"
      }

  ---

  ### 🎯 Hobby

      {
        "id": "uuid",
        "name": "string (unique)"
      }


  ---

  ## 📖 Deplyed API Reference

  ### 🌐 Base URL (Production)

      https://broomees-ck16.onrender.com/api

  ---

  ## 🔓 Public APIs (No Authentication Required)

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | POST | https://broomees-ck16.onrender.com/api/auth/register | Register a new user |
  | POST | https://broomees-ck16.onrender.com/api/auth/token | Issue access token (login) |

  ---

  ## 🔐 Protected APIs  
  *(Production environment)*  
  *(Requires Authorization: Bearer `<ACCESS_TOKEN>`)*  
  *(Rate limited + authenticated)*

  ---

  ### 👤 User APIs

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | GET | https://broomees-ck16.onrender.com/api/users | List all users |
  | GET | https://broomees-ck16.onrender.com/api/users/{id} | Get user by ID |
  | POST | https://broomees-ck16.onrender.com/api/users | Create user |
  | PUT | https://broomees-ck16.onrender.com/api/users/{id} | Update user (optimistic locking) |
  | DELETE | https://broomees-ck16.onrender.com/api/users/{id} | Delete user |

  ---

  ### 🤝 Relationship APIs

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | POST | https://broomees-ck16.onrender.com/api/users/{id}/relationships | Add relationship |
  | DELETE | https://broomees-ck16.onrender.com/api/users/{id}/relationships | Remove relationship |

  ---

  ### 🎯 Hobby APIs

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | POST | https://broomees-ck16.onrender.com/api/users/{id}/hobbies | Add hobby to user |
  | DELETE | https://broomees-ck16.onrender.com/api/users/{id}/hobbies | Remove hobby from user |

  ---

  ### 📊 Metrics APIs

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | GET | https://broomees-ck16.onrender.com/api/metrics/reputation | Get reputation metrics |

  ---

  ### 🔑 Token Management

  | Method | Endpoint | Description |
  |--------|----------|-------------|
  | POST | https://broomees-ck16.onrender.com/api/auth/revoke | Revoke access token (logout) |

  ---

  ### 🧪 Authentication Example (Postman)

  **Header**

      Authorization: Bearer <your_token_here>

---

  ## 🧪 Test Instructions

  **Testing Requirements (Mandatory)**  
  All tests must be **automated** and executable locally using **PHPUnit or Pest**.

  ---

  ### ⚙️ Test Environment Setup

      cp .env.testing.example .env.testing
      php artisan key:generate --env=testing
      php artisan migrate:fresh --env=testing

  ---

  ### ▶️ Run All Tests

      php artisan test

  ---

  ## ✅ Mandatory Test Cases & Commands

  ---

  ### 1️⃣ Reputation Score Calculation Test

  **Purpose:**  
  Verify correctness of reputation score business logic.

  **Test Command:**

      php artisan test tests/Unit/Services/ReputationServiceTest.php

  **Validates:**
  - Friend count impact
  - Shared hobbies weight
  - Account age bonus cap
  - Blocked relationship penalty
  - Edge case handling

  ---

  ### 2️⃣ Rate Limiting Logic Test

  **Purpose:**  
  Ensure API rate limiting correctly blocks excessive requests.

  **Test Command:**

      php artisan test tests/Feature/RateLimitTest.php

  **Validates:**
  - Requests within limit succeed
  - Exceeded requests return HTTP 429
  - Token-based throttling
  - IP-based fallback behavior

  ---

  ### 3️⃣ Optimistic Locking Conflict Test

  **Purpose:**  
  Prevent stale updates caused by concurrent modifications.

  **Test Command:**

      php artisan test tests/Feature/OptimisticLockingTest.php

  **Validates:**
  - Version mismatch detection
  - HTTP 409 Conflict response
  - Data integrity protection
  - Correct error messaging

  ---

  ### 4️⃣ Relationship Uniqueness Under Concurrency (Mocked)

  **Purpose:**  
  Ensure duplicate relationships cannot be created under concurrent access.

  **Test Command:**

      php artisan test tests/Unit/Concurrency/RelationshipConcurrencyTest.php

  **Validates:**
  - Concurrent insert simulation
  - Single relationship creation
  - Database uniqueness enforcement
  - Duplicate request rejection

  ---

  
  ## ⚙️ Environment Configuration

  ### 📄 .env.example

      APP_NAME=Laravel
      APP_ENV=local
      APP_KEY=
      APP_DEBUG=true
      APP_URL=http://localhost

      APP_LOCALE=en
      APP_FALLBACK_LOCALE=en
      APP_FAKER_LOCALE=en_US

      APP_MAINTENANCE_DRIVER=file
      BCRYPT_ROUNDS=12

      LOG_CHANNEL=stack
      LOG_STACK=single
      LOG_DEPRECATIONS_CHANNEL=null
      LOG_LEVEL=debug

      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=broomies
      DB_USERNAME=root
      DB_PASSWORD=

      SESSION_DRIVER=database
      SESSION_LIFETIME=120
      SESSION_ENCRYPT=false
      SESSION_PATH=/
      SESSION_DOMAIN=null

      BROADCAST_CONNECTION=log
      FILESYSTEM_DISK=local
      QUEUE_CONNECTION=database

      CACHE_STORE=database

      REDIS_CLIENT=phpredis
      REDIS_HOST=127.0.0.1
      REDIS_PASSWORD=null
      REDIS_PORT=6379

      MAIL_MAILER=log
      MAIL_HOST=127.0.0.1
      MAIL_PORT=2525
      MAIL_USERNAME=null
      MAIL_PASSWORD=null
      MAIL_FROM_ADDRESS=hello@example.com
      MAIL_FROM_NAME=Laravel

  ---

  ### 🧪 .env.testing.example

      APP_ENV=testing
      APP_KEY=base64:G1k5npxTsESinRohDFuVIO3V/BHYrvW2qbz0n/mwqeg=
      APP_DEBUG=true

      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=broomies_test
      DB_USERNAME=root
      DB_PASSWORD=

      CACHE_STORE=array
      SESSION_DRIVER=array
      QUEUE_CONNECTION=sync


  ---

  ## 📄 License

  MIT License

  ---

  Made with ❤️ using Laravel & PHP






