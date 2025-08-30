<div align="center">

# 🏢 FilamentHRM - Complete HR Management System

A comprehensive Human Resource Management System built with **Laravel 12** and **Filament v3** admin panel, featuring complete employee lifecycle management, attendance tracking, recruitment, and much more.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3-F59E0B?style=flat&logo=php)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql)](https://mysql.com)

---

</div>

## 🌟 Key Features

### 👨‍💼 **Employee Management**
- **Employee Profiles** - Complete employee information with documents
- **Employee Levels** - Internship, Entry, Junior, Mid, Senior, Lead, Manager
- **Department & Position Management** - Organizational structure
- **Manager-Employee Relationships** - Reporting hierarchies
- **Employee Status Management** - Active/Inactive status tracking

### ⏰ **Attendance & Time Tracking**
- **Real-time Check-in/Check-out** - Interactive dashboard widgets
- **Live Duration Tracking** - Real-time work duration counter
- **Work Plans** - Flexible working schedules with working days
- **Permission Minutes** - Grace period before deductions
- **Attendance Reports** - Comprehensive attendance analytics
- **Missing Hours Calculation** - Automatic working hours tracking
- **Late Arrival Tracking** - Based on work plan permissions

### 📝 **Request Management**
- **Leave Requests** - Vacation, sick leave, personal time
- **Attendance Requests** - Manual attendance adjustments
- **Document Requests** - Certificate, salary certificate, etc.
- **Approval Workflows** - Manager approval system
- **Request History** - Complete audit trail

### 🎯 **Recruitment & Hiring**
- **Job Posting Management** - Create and manage job openings
- **Public Career Portal** - External job application page
- **Application Tracking** - Complete applicant management
- **Custom Questions** - Dynamic job application forms
- **Job Stages** - Track application progress
- **Job Categories** - Organize positions by category

### 📊 **Admin Dashboard Features**
- **Multi-Panel System** - Separate Admin & Employee panels
- **Role-Based Access Control** - Spatie Permissions integration
- **Comprehensive Reports** - Attendance, employee statistics
- **Settings Management** - System configurations
- **Notification System** - Internal notifications
- **Activity Logging** - Complete audit trail

### 🎨 **User Experience**
- **Responsive Design** - Mobile-first approach
- **Dark Mode Support** - Built-in dark/light theme
- **Real-time Updates** - Live data without refresh
- **Interactive Widgets** - Dashboard components
- **Multi-language Support** - Arabic & English ready

## 🚀 Getting Started

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/hazem-hammad/filament-hrm.git
cd filament-hrm
```

### 2️⃣ Build and Start Docker Containers

```bash
docker compose up --build -d
```

💡 **Note:** The first time you start the project, it will:

- Install dependencies (`composer install`)
- Set up the database (`php artisan migrate --seed`)
- Create super admin (`php artisan shield:super-admin`)
- Run background services

📌 **If you see a `502 Bad Gateway` error,** wait for a few seconds until everything initializes.

### 3️⃣ Manual Installation (Alternative)

If you prefer manual installation:

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed
php artisan shield:super-admin

# Build assets
npm run build

# Start development server
php artisan serve
```

---

## 📌 **Available Services & URLs**

### 🖥️ **Application Access**

- **Main Website:** [http://localhost:8080](http://localhost:8080)
- **Admin Panel:** [http://localhost:8080/admin/login](http://localhost:8080/admin/login)  
  📧 **Email:** admin@example.com | 🔐 **Password:** admin123
- **Employee Panel:** [http://localhost:8080/employee/login](http://localhost:8080/employee/login)
- **Career Portal:** [http://localhost:8080/careers](http://localhost:8080/careers)

### 🛠️ **Development Tools**

- **PHPMyAdmin:** [http://localhost:8081](http://localhost:8081) _(Database Management)_
- **Mailpit:** [http://localhost:8082](http://localhost:8082) _(Email Testing)_
- **RedisInsight:** [http://localhost:8083](http://localhost:8083) _(Redis Management)_

**RedisInsight Setup:**
- Host: `redis`
- Port: `6379`
- User: `default`
- Password: _(leave empty)_

---

## 🏗️ **System Architecture**

### **Admin Panel Features**
- ✅ Employee Management (CRUD + Status Management)
- ✅ Department & Position Management
- ✅ Work Plan Management with Schedule Setup
- ✅ Attendance Management & Reports
- ✅ Request Management & Approvals
- ✅ Job Posting & Recruitment Management
- ✅ Role & Permission Management
- ✅ System Settings & Configurations
- ✅ Notification Management
- ✅ Document Type Management
- ✅ Vacation & Attendance Type Management

### **Employee Panel Features**
- ✅ Personal Dashboard with Widgets
- ✅ Interactive Check-in/Check-out Widget
- ✅ Real-time Attendance Duration Tracking
- ✅ Personal Attendance History (Last 7 Days)
- ✅ Request Submission (Leave, Documents, etc.)
- ✅ Request Status Tracking
- ✅ Profile Management

### **Public Features**
- ✅ Career Portal with Job Listings
- ✅ Job Application System
- ✅ Dynamic Job Application Forms
- ✅ Responsive Design for All Devices

---

## 🗄️ **Database Schema**

### **Core Tables**
- `admins` - Admin user accounts
- `employees` - Employee records with full HR data
- `departments` - Organizational departments
- `positions` - Job positions within departments
- `work_plans` - Working schedules and time configurations

### **Attendance System**
- `attendances` - Daily attendance records
- `attendance_types` - Types of attendance (regular, overtime, etc.)
- `requests` - Leave and other employee requests
- `vacation_types` - Different types of leave

### **Recruitment System**
- `jobs` - Job postings and requirements
- `job_categories` - Job classification
- `job_stages` - Application process stages
- `job_applications` - Submitted applications
- `custom_questions` - Dynamic application questions
- `job_application_answers` - Applicant responses

### **System Tables**
- `settings` - System configurations
- `notifications` - Internal notification system
- `document_types` - Available document types
- `roles` & `permissions` - Access control system

---

## 🔧 **Technology Stack**

### **Backend**
- **Laravel 12** - Latest PHP framework
- **PHP 8.3+** - Modern PHP features
- **MySQL 8.0+** - Database management
- **Redis** - Caching and sessions
- **Sanctum** - API authentication

### **Frontend**
- **Filament v3** - Admin panel framework
- **Alpine.js** - Reactive frontend components
- **Tailwind CSS** - Utility-first styling
- **Livewire** - Dynamic PHP components

### **Key Packages**
- **Spatie Media Library** - File management
- **Filament Shield** - Role & permission management
- **Spatie Activity Log** - Audit trails
- **Laravel Telescope** - Debugging & monitoring
- **Filament Google Maps** - Location services

---

## 📱 **API Endpoints**

### **Authentication**
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `POST /api/v1/auth/reset-password` - Password reset
- `POST /api/v1/auth/send-otp` - Send OTP verification
- `POST /api/v1/auth/verify` - Verify OTP code

### **User Management**
- `GET /api/v1/profile` - Get user profile
- `PUT /api/v1/profile` - Update profile
- `POST /api/v1/profile/change-password` - Change password
- `POST /api/v1/profile/change-phone` - Update phone number

### **Common Services**
- `GET /api/v1/configurations` - Get app configurations
- `PUT /api/v1/device-token` - Update FCM token
- `POST /api/v1/upload` - File upload service
- `GET /api/v1/notifications` - Get user notifications

### **Content Management**
- `GET /api/v1/articles` - Get articles/news
- `GET /api/v1/banners` - Get banner slides
- `GET /api/v1/faqs` - Get FAQ entries
- `GET /api/v1/pages/{slug}` - Get static pages

### **Employee Services**
- `GET /employee/attendance/duration` - Real-time attendance duration

---

## 🎯 **Features Highlights**

### **✨ Interactive Dashboard Widgets**
- Real-time check-in/check-out buttons with status indicators
- Live duration counter showing working hours
- Recent attendance table with last 7 days data
- Employee statistics and analytics

### **⚡ Real-time Updates**
- Live attendance duration tracking using Alpine.js
- Automatic page refresh after check-in/out actions
- Real-time status updates without page reload

### **🔐 Advanced Security**
- Role-based access control with Spatie Permissions
- Multi-panel authentication (Admin/Employee)
- API authentication with Laravel Sanctum
- Activity logging for all user actions

### **📊 Comprehensive Reporting**
- Attendance reports with missing hours calculation
- Employee performance analytics
- Late arrival tracking with permission minutes
- Request approval workflows and history

### **🎨 Modern UI/UX**
- Clean, responsive design with Tailwind CSS
- Dark mode support throughout the application
- Interactive components with smooth animations
- Mobile-first responsive layout

---

## 🚦 **Development Status**

### **✅ Completed Features**
- [x] Complete Employee Management System
- [x] Advanced Attendance Tracking with Real-time Updates
- [x] Request Management with Approval Workflows  
- [x] Full Recruitment & Job Application System
- [x] Multi-panel Admin System (Admin/Employee)
- [x] Role & Permission Management
- [x] Interactive Dashboard Widgets
- [x] Public Career Portal
- [x] API Authentication & Endpoints
- [x] File Management with Media Library

### **🔄 Upcoming Features**
- [ ] Mobile Application (React Native/Flutter)
- [ ] Advanced Analytics Dashboard
- [ ] Payroll Management Integration
- [ ] Performance Review System
- [ ] Training & Development Module
- [ ] Asset Management System

---

## 🤝 **Contributing**

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### **Development Guidelines**
- Follow Laravel coding standards
- Write tests for new features
- Update documentation for API changes
- Use conventional commit messages

---

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🆘 **Support & Documentation**

### **Getting Help**
- 📚 [Laravel Documentation](https://laravel.com/docs)
- 🎯 [Filament Documentation](https://filamentphp.com/docs)
- 💬 [GitHub Issues](https://github.com/hazem-hammad/filament-hrm/issues)

### **Contact**
- 👨‍💻 **Developer:** Hazem Hamqad
- 📧 **Email:** hazem.hamaad@outlook.com
- 🐙 **GitHub:** [@hazem-hammad](https://github.com/hazem-hammad)

---

<div align="center">

### 🎉 **Enjoy and Happy Coding!** 🚀🔥

**Built with ❤️ using Laravel & Filament**

</div>