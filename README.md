# Registration App

A course registration system built with PHP and MySQL.

## Features

- User Authentication (Register/Login)
- Course Registration
- Waitlist Management
- Profile Management
- Course Management
- Responsive Design

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Composer (optional)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/DavidRutledge037/registration-app.git
```

2. Create a MySQL database named `registration_app`

3. Import the database schema:
```bash
mysql -u root -p registration_app < database/schema.sql
```

4. Configure the database connection:
```bash
cp config/database.example.php config/database.php
```
Then edit `config/database.php` with your database credentials.

## Directory Structure

```
registration-app/
├── assets/
│   ├── css/
│   └── js/
├── classes/
├── config/
├── includes/
└── database/
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.
