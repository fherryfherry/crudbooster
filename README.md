# CRUDBooster

## Getting Started

Thank you for choosing CRUDBooster. Check it out https://github.com/fherryfherry/crudbooster

### Overview

CRUDBooster uses the following dependencies:
- [Laravel Framework](https://laravel.com/) (PHP Framework)
- [Laravel Livewire](https://livewire.laravel.com/) (Full-stack Framework)
- [Alpine JS](https://alpinejs.dev/) (JavaScript Framework)
- [Tailwind CSS](https://tailwindcss.com/) (CSS Framework)
- [Laravel Excel](https://laravel-excel.com/) (Export & Import Excel)
- [DomPDF](https://github.com/dompdf/dompdf) (Export PDF)

### Prerequisites

- PHP >= 8.2 (PHP >= 8.3 if you're on Laravel 13.x)
- Laravel 11.x, 12.x, or 13.x
- Composer
- Git
- PHP extensions: Ctype, cURL, DOM, Fileinfo, Filter, GD, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer, XML, Zip
- A supported database: MySQL 5.7.7+, MariaDB 10.2.2+, PostgreSQL, SQLite, or SQL Server

### Quick Install

```bash
composer create-project --prefer-dist laravel/laravel:^12.0 your-project-name
cd your-project-name

composer config repositories.crudbooster vcs https://github.com/fherryfherry/crudbooster.git
composer require crudbooster/crudbooster:^8.0

php artisan cb:install
php artisan serve
```

`cb:install` is interactive — it will ask for your app name, admin credentials, and database
configuration. Full details (system requirements, Docker setup, troubleshooting) are in the
[Usage Documentation](doc_cb.md).

### Documentation Index

- [Codebase Documentation](CODEBASE_DOCUMENTATION.md)
- [Usage Documentation](doc_cb.md)
- [Changelog](CHANGELOG.md)
