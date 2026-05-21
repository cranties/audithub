# AuditHub - Dynamic Survey & Checklist Portal

A modern, fast, and highly performant web application built with **Laravel 11**, **Livewire 3**, and **Alpine.js**. 

AuditHub allows administrators to visually design custom surveys and checklists through a dynamic form builder, publish them via secure public URLs, and collect responses. Built with scalability in mind, it utilizes high-performance JSON storage and asynchronous background queues to generate PDF reports without blocking the user experience.

---

## 🚀 Key Features

* 🛠️ **Dynamic Form Builder:** An intuitive, modern administrative interface to create custom forms including text inputs, dates, times, file/photo uploads, boolean toggles, and select dropdowns.
* 🔗 **Secure Public Links:** Automatically generates cryptographically secure, non-enumerable URLs (64-character tokens) for form submissions, requiring no user registration.
* 🔒 **Form Versioning & Data Integrity:** Once a survey is published, its schema is strictly locked. This prevents breaking changes and preserves the historical integrity of the collected data.
* 💾 **Optimized JSON Storage:** Eliminates the classic EAV (Entity-Attribute-Value) anti-pattern. Form schemas and user submissions are stored natively in MySQL JSON columns, ensuring rapid querying and preventing database bloat.
* 📦 **Private & Secure Uploads:** User-uploaded files and photos are kept strictly in isolated, private storage. Access is granted exclusively to authenticated administrators via protected routes.
* 📄 **Asynchronous PDF Generation:** PDF report rendering is entirely decoupled from the HTTP request lifecycle. Handled by Laravel Queues, it guarantees sub-100ms response times for the end-user submitting the form.
* 🌍 **Full English UI:** The entire platform (Frontpage, Auth, Admin Dashboard, and Public Surveys) is standardized in English for international use.

---

## 🛠️ Tech Stack

* **Core Framework:** Laravel 13 (PHP 8.2+)
* **Reactive UI:** Livewire 3 & Alpine.js
* **Styling:** Tailwind CSS (Modern, minimalist design)
* **Database:** MySQL 8.0+ (Leveraging native JSON support)
* **Background Jobs:** Redis / Database driver
* **PDF Engine:** DomPDF / Browsershot (Depending on configuration)

---

## ⚙️ Installation & Setup

### 1. Clone the repository
`git clone https://github.com/cranties/audithub.git`
`cd audithub`

### 2. Install PHP and Node dependencies
`composer install`
`npm install`
`npm run build`

### 3. Environment Configuration
Duplicate the example environment file and generate the application key.
`cp .env.example .env`
`php artisan key:generate`

Configure your database credentials in the `.env` file:
`DB_CONNECTION=mysql`
`DB_HOST=127.0.0.1`
`DB_PORT=3306`
`DB_DATABASE=audithub`
`DB_USERNAME=root`
`DB_PASSWORD=`

### 4. Database Migrations
Run the migrations to create the JSON-supported surveys and submissions tables.
`php artisan migrate`

### 5. Storage Link (Optional but recommended)
While uploads are stored privately, linking the public storage is good practice for general assets.
`php artisan storage:link`

---

## 🚦 Queue & Background Jobs Configuration

AuditHub relies heavily on Laravel Queues to process PDF generation asynchronously. If the queue worker is not running, PDFs will not be generated.

### Local Development Setup
In your `.env` file, set the queue connection. For local development, the database driver is easiest:
`QUEUE_CONNECTION=database`

Start the local worker:
`php artisan queue:work`

### Production Setup (Supervisor)
For production environments, you should use Redis for better performance and Supervisor to keep the queue worker alive permanently.

1. Set your `.env` for Redis:
`QUEUE_CONNECTION=redis`

2. Install Supervisor on your Linux server (e.g., Ubuntu):
`sudo apt-get install supervisor`

3. Create a configuration file `/etc/supervisor/conf.d/audithub-worker.conf`:
`[program:audithub-worker]`
`process_name=%(program_name)s_%(process_num)02d`
`command=php /path-to-your-project/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600`
`autostart=true`
`autorestart=true`
`stopasgroup=true`
`killasgroup=true`
`user=www-data`
`numprocs=8`
`redirect_stderr=true`
`stdout_logfile=/path-to-your-project/storage/logs/worker.log`
`stopwaitsecs=3600`

4. Start Supervisor:
`sudo supervisorctl reread`
`sudo supervisorctl update`
`sudo supervisorctl start audithub-worker:*`

---

## 🏃‍♂️ Running the Application locally

Once everything is configured, start the Laravel development server:
`php artisan serve`

Start the Vite development server for hot-reloading Tailwind and JS:
`npm run dev`

Your application will be available at `http://localhost:8000`.

---

## 📝 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
