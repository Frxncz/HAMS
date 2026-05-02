# HAMS: Healthcare Appointment Management System

![](screenshots/system-overview.png)

## About the Project

[HAMS](https://github.com/Frxncz/HAMS) is a web-based system for small clinics to manage appointments and schedules. Patients can request bookings, doctors can manage sessions, and admins can oversee users and appointments through role-based dashboards.

## Deployment

- Live link: Not deployed yet. Add your URL here.

## Tech Stack

- PHP
- MySQL
- HTML, CSS, JavaScript
- Apache (XAMPP)

## MVC Structure

This project follows a lightweight MVC-style layout to separate concerns:

- `app/Controllers/` handles request logic for Admin, Doctor, Patient, and Auth flows.
- `app/Models/` contains database connection and data access helpers.
- `app/Views/` contains role-based pages and shared partials.
- `public/` is the web root (entry points, static assets, and routed pages).

### Directory Overview

```
app/
	Controllers/
	Models/
	Views/
public/
	index.php
	assets/
	admin/
	doctor/
	patient/
```

## Key Features

### Admin:

- Add, edit, and delete doctor details
- Schedule new doctor sessions and remove sessions
- View and manage patient details
- Review and confirm patient booking requests

### Doctors:

- View upcoming appointments and patient details
- Manage schedules and sessions
- Edit or delete their account information

### Patients:

- Book appointments online
- Create and manage personal accounts
- View booking history
- Update or delete their accounts

---

## Credentials (Demo)

| Role    | Email              | Password     |
| ------- | ------------------ | ------------ |
| Admin   | `admin@hams.com`   | `admin123`   |
| Doctor  | `doctor@hams.com`  | `doctor123`  |
| Patient | `patient@hams.com` | `patient123` |

## Screenshots

| Login                           | Admin Dashboard                      | Doctor Dashboard                      | Patient Dashboard                      |
| ------------------------------- | ------------------------------------ | ------------------------------------- | -------------------------------------- |
| ![](screenshots/login-page.png) | ![](screenshots/admin-dashboard.png) | ![](screenshots/doctor-dashboard.png) | ![](screenshots/patient-dashboard.png) |

| Appointment Form                      | Session Schedule                      | Patient Details                      | Booking History                      |
| ------------------------------------- | ------------------------------------- | ------------------------------------ | ------------------------------------ |
| ![](screenshots/appointment-form.png) | ![](screenshots/session-schedule.png) | ![](screenshots/patient-details.png) | ![](screenshots/booking-history.png) |

## Demo Project / Video

- Demo video: https://drive.google.com/file/d/1vB1SSUDrZSAg5rF4-R_DN4LtHqfMZOCO/view?usp=sharing

## How to Set Up (Local)

1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Download the source code and extract it.
3. Copy the folder into your XAMPP **htdocs** directory.
4. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser.
5. Create a new database named `hams`.
6. Import the `hams.sql` file from the project root.
7. Visit [http://localhost/HAMS/public](http://localhost/HAMS/public) to open the app.

## Development Environment

- Apache: `2.4.39`
- PHP: `7.3.5`
- MySQL: `5.7.26`

---

We hope **HAMS** simplifies clinic appointment scheduling and makes healthcare management more efficient.
