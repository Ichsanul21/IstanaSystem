# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within Istana Laundry System, please send an email to the development team at **security@alk-tech.my.id**. All security vulnerabilities will be promptly addressed.

Please do not disclose security-related issues publicly until a fix has been announced.

## Security Best Practices

### Authentication & Authorization
- All passwords are hashed using Laravel's Bcrypt algorithm
- Role-based access control via Spatie Permission (8 defined roles)
- Developer account is protected from deactivation
- Self-edit restricted to password and photo only
- Session management with secure HTTP-only cookies

### API Security
- API routes are protected by Sanctum token authentication
- Rate limiting applied to all public endpoints
- CORS configured per-environment
- Input validation on all endpoints using Form Requests

### Data Protection
- All sensitive data encrypted at rest
- Customer phone numbers masked in logs
- Database credentials stored in environment variables (`.env`)
- SQL injection prevention via Eloquent ORM

### Branch Isolation
- Global `branch_id` scope via `SetBranchContext` middleware
- `HasBranchScope` trait ensures data isolation between branches
- Workshop data isolated per branch association

### Audit & Monitoring
- All CRUD operations logged in database `activity_logs` table
- Daily archive of activity logs to file
- Backup via Spatie Backup (manual trigger)

### Payment Security
- Midtrans Snap API handles PCI compliance (no raw card data stored)
- Webhook signature verification on all payment callbacks
- Sensitive gateway credentials stored in encrypted settings

### Frontend Security
- CSRF protection on all POST routes
- XSS prevention via Blade's automatic escaping
- Content Security Policy headers
