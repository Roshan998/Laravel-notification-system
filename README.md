
### Laravel Notification system API using redis
---
## Setup Instructions:
- git clone https://github.com/Roshan998/Laravel-notification-system.git
- cd Laravel-notification-system
- composer install (PHP 8.3)
- cp .env.example .env
- php artisan key:generate
- update in .env
```json
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```
- update Queue Configuration in .env (first you need to install redis server)
```json
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```
-update mail host in .env
```json
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}
```
-run:
```json
php artisan migrate --seed 
install npm && npm run dev (need node version 16)
php artisan serve;
php artisan queue:work;
```


## Overview

This project implements a production-ready **Notification System API** built with Laravel.It is designed to simulate a scalable system similar to AgentCIS CRM, which handles high traffic (50–60 requests per second) across multiple tenants and workflows.

The system demonstrates:

- Asynchronous queue processing
- Rate limiting (10 notifications per user per hour)
- Caching for optimized monitoring endpoints
- Clean layered architecture
- Retry logic with failure handling
- Unit & Feature testing


## Architecture

The application follows a clean layered architecture:
```json
    Controller → Service → Repository → Model → Queue Job
```

## Key Design Goals

- Separation of concerns
- Scalable async processing
- Efficient database usage
- Extensibility for future notification types
- High test coverage
- Production-ready patterns

## Features Implemented

## 1.Publish Notifications

**POST** `/api/notifications`

- Validates request
- Verifies user exists
- Stores notification in database
- Dispatches queue job
- Applies rate limiting (10/hour per user)

## header: 
```json
{
    Authorization:Bearer <token>
    Content-Type:application/json
}
```
## body: (Raw)  
```json 
{
    "type": "email",
    "title": "New notification Alert!!!",
    "message": "This is to infom you that this system works well!!",
    "payload": {
        "extra": "data"
    },
    "user_id":2
}
```

## Queue Processing

- Uses Laravel Queue system (Redis or Database)
- Job: `ProcessNotification`
- Simulates sending via logging
- Updates notification status:
  - `pending`
  - `sent`
  - `failed`

## 3️. Monitoring APIs

## (i).Recent Notifications
**GET** `/api/notifications/recent`

- Returns latest 20 notifications
- Cached for 30 seconds

## header: 
```json
{
    Authorization:Bearer <token>
    Content-Type:application/json
}
```


## (ii).Summary API
**GET** `/api/notifications/summary`

## header: 
```json
{
    Authorization:Bearer <token>
    Content-Type:application/json
}
```

## Returns:
``` json
{
"sent": 120,
"failed": 5,
"pending": 8
}
```

## Api to gettoken
**Post** `/api/login`
## body (form-data)
```json
{
    email:user10@sharklasers.com
    password:password
}
```
## return
```json
{
    token: 1|WRXNJbAzpx18qPeEjFzbAFYPZQMLUCDjhbPM0nc8
}
```
-copy only WRXNJbAzpx18qPeEjFzbAFYPZQMLUCDjhbPM0nc8


## Authentication

-Laravel Sanctum
-All notification endpoints require authentication
-Bearer token required

## Rate Limiting

-10 notifications per user per hour
-Implemented using Laravel throttle middleware
-Returns 429 Too Many Requests if exceeded

# Testing

-Run all tests:
```json
php artisan test
```

## Design Decisions

This section explains the architectural choices and design patterns used in this implementation.

---

## 1️ Why Queue-Based Processing?

The system uses Laravel Queues with Redis to process notifications asynchronously.

### Reason:
- Keeps API responses fast and non-blocking
- Supports high traffic (50–60 RPS scenario)
- Prevents request timeout during external operations
- Improves scalability under load

This aligns with production-grade distributed system design.

---

## 2️ Why Redis?

Redis is used for:

- Queue backend
- Caching
- Rate limiting

### Reason:
- In-memory data store → extremely fast
- Ideal for high-throughput environments
- Reduces database load
- Provides atomic operations (useful for throttling)

Redis ensures performance and scalability.

---

## 3️ Layered Architecture (Controller → Service → Repository → Model)

The project follows a clean separation of concerns:

- **Controller** → Handles HTTP layer
- **Service Layer** → Business logic
- **Repository Layer** → Database access abstraction
- **Model** → Eloquent ORM entity
- **Job** → Async processing

### Benefits:
- Maintainability
- Testability
- Scalability
- Clear responsibility boundaries

This makes the system easier to extend and refactor.

---

## 4️ Caching Strategy

Endpoints:
- `/notifications/recent`
- `/notifications/summary`

are cached for 30 seconds.

### Reason:
- These are read-heavy endpoints
- Prevents repeated expensive database queries
- Reduces database load under traffic spikes
- Improves response time

Cache invalidation is time-based to balance freshness and performance.

---

## 5️ Rate Limiting (10 Notifications per User per Hour)

Implemented using Laravel throttle middleware.

### Reason:
- Prevent abuse or spamming
- Protect system resources
- Simulate production constraints

Redis-backed throttling ensures atomic and fast counting.

---

## 6️ Retry & Failure Handling

Queue jobs support retry attempts.

### Reason:
- Transient failures (network issues, service downtime)
- Improves reliability
- Ensures eventual consistency

Failed jobs are marked in the database for monitoring.

---

## 7️ Status Tracking Design

Notification lifecycle states:

- `pending`
- `sent`
- `failed`

### Reason:
- Enables monitoring
- Makes system observable
- Supports reporting and analytics

---

## 8️ Use of Laravel Sanctum

Authentication is implemented via token-based authentication.

### Reason:
- Stateless API design
- Suitable for SPA or mobile clients
- Secure and lightweight

---

## 9️ Extensibility Considerations

The system is structured to support future extensions:

- Email channel
- SMS channel
- Push notifications
- Webhooks
- Scheduled notifications

The architecture allows easy introduction of Strategy Pattern for channel handling.

---

## Assumptions

The following assumptions were made during development:

---

## 1️ Users Already Exist

The system assumes users are pre-created and stored in the database.

Notification publishing requires a valid `user_id`.

---

## 2️ Notification Sending is Simulated

Actual external email/SMS service integration is not implemented.

Instead:
- Notification sending is simulated using logs
- This keeps focus on architecture and queue design

---

## 3️ Single-Tenant Environment

The current implementation assumes a single-tenant system.

Multi-tenant support can be added by:
- Adding `tenant_id` to notifications
- Scoping queries by tenant

---

## 4️ Rate Limit Scope

Rate limiting is scoped per authenticated user.

Global rate limiting is not implemented.

---

## 5️ Cache Expiration Strategy

Cache uses time-based expiration (30 seconds).

Advanced invalidation strategies (event-driven cache busting) are not implemented for simplicity.

---

## 6️ No Distributed Worker Scaling

The implementation runs a single queue worker locally.

In production:
- Multiple workers
- Supervisor process management
- Horizontal scaling

would be used.

---

## 7️ No Event Sourcing

The system updates notification status directly in the database.

Event sourcing pattern was not implemented but could be added in future.

---

## Conclusion

The architectural decisions prioritize:

- Scalability
- Performance
- Clean code principles
- Maintainability
- Extensibility
- Production-readiness

The system design aligns with modern Laravel best practices and high-traffic CRM requirements.

