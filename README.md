# 🎫 PHP Ticketing App

A modern, responsive ticketing and chat support system built with PHP, MySQL, Bootstrap, JavaScript, and Pusher. This project is designed to streamline customer support through a centralized platform.

---

## 🧩 Features

- Customer Ticketing Portal
- Admin Dashboard for Ticket Management
- Live Chat Interface (Pusher-powered real-time updates)
- Chatbot Integration to Auto-Generate Tickets
- Ticket Status Management (New, In Progress, Closed)
- Notifications via Pusher (Real-time alerts)
- Responsive UI using Bootstrap 5
- AJAX-powered interactions with JavaScript

---

## 🛠️ Tech Stack

- **Backend**: PHP 8+, MySQL
- **Frontend**: HTML5, Bootstrap 5, JavaScript (jQuery/AJAX)
- **Real-Time Engine**: Pusher
- **Optional**: Select2, Choices.js, or custom plugins

---

## ⚙️ Installation

### Requirements
- PHP 8.0 or above
- MySQL 5.7+ / MariaDB
- Apache/Nginx
- Pusher Account (for real-time chat)
- Composer (if using dependencies)

### Steps

1. Clone the repo:
   git clone https://github.com/XtechGlobal-Dev/saassupport.git
   
2. Configure the environment:

/script/config.php

Set your database and Pusher credentials

3. Import the database:

mysql -u root -p your_db_name < import.sql

Run your local server (XAMPP, Laragon, Valet, etc.)
