# Joe's Coaches Admin System

![PHP](https://img.shields.io/badge/PHP-7.4+-purple?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow?logo=javascript&logoColor=black)
![Status](https://img.shields.io/badge/Status-Active-success)
![License](https://img.shields.io/badge/License-MIT-green)

![Repo Size](https://img.shields.io/github/repo-size/ginaisthando/joes-coaches-admin)
![Last Commit](https://img.shields.io/github/last-commit/ginaisthando/joes-coaches-admin)
![Open Issues](https://img.shields.io/github/issues/ginaisthando/joes-coaches-admin)
![Stars](https://img.shields.io/github/stars/ginaisthando/joes-coaches-admin?style=social)

A complete **admin panel** for managing employees and interviews with **secure authentication**, **database persistence**, and a **modern web interface**.  

---

## 🚀 Features

### 🔐 Authentication & Security
- **Secure Login/Logout** - PHP session-based authentication
- **Protected Routes** - All admin pages and API endpoints require authentication
- **Automatic Redirects** - Unauthorized access redirects to login page
- **Session Management** - Secure session handling with timeout

### 👥 Employee Management
- **View Employees** - Dynamic loading from database with profile images
- **Add Employees** - Complete form with validation and auto-generated employee numbers
- **Delete Employees** - Soft delete with confirmation dialogs
- **Department Integration** - Automatic department creation and management

### 📅 Interview Management
- **View Interviews** - Real-time loading of scheduled interviews
- **Add Interviews** - Create new interview opportunities
- **Accept/Reject** - Update interview status with decision tracking
- **Delete Interviews** - Remove interviews with confirmation

### 💾 Database Features
- **Full Persistence** - All data stored in MySQL database
- **Relational Design** - Proper foreign keys and constraints
- **Sample Data** - Pre-loaded with realistic test data
- **Data Integrity** - Validation at both client and server levels

## 📋 Requirements
- **PHP 7.4+** (XAMPP recommended for Windows)
- **MySQL 5.7+** or MariaDB
- **Apache Web Server** (included with XAMPP)
- **Modern Web Browser** with JavaScript enabled

## 📁 Project Structure
```
admin/
├── index.php                    # Protected admin dashboard
├── login.php                    # Authentication login page
├── script.js                    # Frontend JavaScript (CRUD + auth)
├── style.css                    # Complete styling
├── database_schema.sql          # Database schema + sample data
├── system_test.php              # Comprehensive system testing
├── SETUP_INSTRUCTIONS.md        # Detailed setup guide
├── config/
│   ├── database.php             # PDO database connection
│   └── auth.php                 # Authentication helpers
├── api/                         # REST API endpoints
│   ├── login.php                # POST /login - User authentication
│   ├── logout.php               # POST /logout - Session cleanup
│   ├── get_employees.php        # GET /employees - List employees
│   ├── add_employee.php         # POST /employees - Create employee
│   ├── delete_employee.php      # POST /employees/delete - Remove employee
│   ├── get_interviews.php       # GET /interviews - List interviews
│   ├── add_interview.php        # POST /interviews - Create interview
│   ├── delete_interview.php     # POST /interviews/delete - Remove interview
│   └── update_interview_status.php # POST /interviews/status - Accept/reject
└── Pics/                        # Profile images and assets
    ├── icon.png                 # Company logo
    ├── hs1.png - hs5.png        # Employee profile pictures
    └── default.png              # Default profile image
```

## 🚀 Quick Start

### Step 1: Installation
1. **Download/Clone** this project to `C:\xampp\htdocs\admin`
2. **Start XAMPP** - Enable Apache and MySQL services
3. **Create Database**:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Import `database_schema.sql` (creates `joes_coaches_admin` database)

### Step 2: Configuration
1. **Database Settings** (if needed):
   ```php
   // config/database.php
   private $host = 'localhost';
   private $username = 'root';        // Your MySQL username
   private $password = '';            // Your MySQL password
   ```

### Step 3: Testing
1. **System Test**: Visit `http://localhost/admin/system_test.php`
2. **Login**: Go to `http://localhost/admin/login.php`

## 🔑 Default Credentials
- **Username**: `admin`
- **Password**: `admin123`

## 🗄️ Database Schema

### Core Tables
- **`admin_users`** - System administrators and their roles
- **`admin_sessions`** - Active login sessions with expiration
- **`departments`** - Company departments with budgets
- **`employees`** - Complete employee records with all details
- **`interviews`** - Scheduled interviews with candidate information
- **`interview_applications`** - Interview decisions and status tracking

### Sample Data Included
- **4 Employees**: Ashton Jacobs, Pamela Peterson, Joe Adams, Jessica Bloom
- **5 Departments**: Tech, Marketing, Executive, Transport, HR
- **3 Sample Interviews**: Ready for testing accept/reject functionality

## 🔌 API Reference

All API endpoints require authentication (except `/login`). Responses are JSON format.

## 🔧 Troubleshooting

### Common Issues

**❌ "Database connection failed"**
- Ensure MySQL is running in XAMPP
- Verify `database_schema.sql` was imported successfully
- Check credentials in `config/database.php`

**❌ "Authentication required" errors**
- Make sure you're logged in at `login.php`
- Check browser cookies are enabled
- Clear browser cache and try again

**❌ "Cannot access API endpoints"**
- Verify Apache is running in XAMPP
- Check that all files are in the correct directory structure
- Ensure PHP is properly configured

**❌ JavaScript errors in browser console**
- Check browser developer tools (F12)
- Verify all API endpoints are accessible
- Look for network request failures

### Debug Steps
1. **Run System Test**: `http://localhost/admin/system_test.php`
2. **Check Error Logs**: XAMPP Control Panel → Apache → Logs
3. **Verify Database**: phpMyAdmin → `joes_coaches_admin` database
4. **Test API Directly**: Use browser or Postman to test endpoints

## 🔒 Security Features

### Authentication Security
- **Session-based authentication** with secure PHP sessions
- **Password hashing** using PHP's `password_hash()` function
- **Session timeout** automatic cleanup of expired sessions
- **CSRF protection** through proper session handling

### Data Security
- **SQL injection prevention** using PDO prepared statements
- **Input validation** on both client and server sides
- **Soft deletes** for employees (data preservation)
- **Error handling** without exposing sensitive information

## 👥 Support

For issues, questions, or contributions:
1. Check the troubleshooting section above
2. Run the system test to identify specific problems
3. Review the setup instructions for proper configuration
4. Check browser console and server logs for error details
