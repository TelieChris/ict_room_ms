# ICT Room Asset Management System (GS Remera TSS)

Web-based ICT room asset management system built with **PHP + MySQL + Bootstrap 5**.

## Requirements

- XAMPP / LAMP shared hosting (cPanel)
- PHP 8.x recommended (works on PHP 7.4+ with minor adjustments)
- MySQL / MariaDB

## Setup (XAMPP)

1. Create a database (example: `ict_room_ms`)
2. Import schema:
   - Run `database/schema.sql` in phpMyAdmin
3. Configure DB:
   - Edit `config/config.php` (DB host/user/pass/name)
4. Login:
   - Run `database/seed_admin.php` once to create/update the admin account
   - Username: `admin`
   - Password: `Admin@12345` (change after first login)

## Suggested Folder Structure (already scaffolded)

```
/admin
  /assets
  /audit
/assets
  /css
  /js
  /images
    /uploads
/auth
/config
/database
/includes
/reports
/teacher
  /assignments
  /maintenance
/views
  /errors
```

## Notes for Shared Hosting (cPanel)

- Put project under `public_html/` (or subfolder).
- Update DB credentials in `config/config.php`.
- Ensure `assets/images/uploads/` is writable.

## Performance / Low-end PCs

- Server-side rendering (no heavy JS frameworks)
- Bootstrap 5 with a tiny custom CSS file
- Small, paginated tables and indexed DB queries

## Security Notes (important)

- Passwords stored using `password_hash()` / `password_verify()`
- Role-based access (`require_role()`) + protected routes (`require_login()`)
- Uploads restricted by size and extension (JPG/PNG/WEBP)



