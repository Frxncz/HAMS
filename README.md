# HAMS: Healthcare Appointment Management System using PHP, MySQL
![](screenshots/system-overview.png)

[HAMS](https://github.com/Frxncz/HAMS) is a web-based project designed for small healthcare clinics to streamline online appointment bookings for patients and manage schedules for doctors. This system uses **PHP**, **HTML**, **CSS**, and **JavaScript** to deliver a reliable and efficient appointment system. Patients can submit appointment requests, while doctors can manage their schedules and patient records through specific, role-defined dashboards. As the repository primarily focuses on healthcare management, this project features a clean design prioritizing simplicity and functionality for small clinics.

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

| Admin Dashboard | Doctor Dashboard | Patient Dashboard |
| -------| -------| -------|
| Email: `admin@hams.com` | Email: `doctor@hams.com` |   Email: `patient@hams.com` | 
| Password: `admin123` | Password: `doctor123` | Password: `patient123` |
| ![](screenshots/admin-dashboard.png)| ![](screenshots/doctor-dashboard.png) | ![](screenshots/patient-dashboard.png) |

---

## GET STARTED

1. Open your XAMPP Control Panel and start **Apache** and **MySQL**.
2. Download the **HAMS** source code and extract the zip file.
3. Copy the extracted folder and paste it into the **htdocs** directory in XAMPP.
4. Browse to [PHPMyAdmin](http://localhost/phpmyadmin) using your browser.
5. Create a new database named `hams`.
6. Import the provided `hams.sql` database file from the source code directory.
7. Start the application by visiting [http://localhost/hams-main](http://localhost/hams-main) in your browser.

---

## Screenshots

| ![](screenshots/login-page.png) | ![](screenshots/admin-dashboard.png)| ![](screenshots/doctor-dashboard.png)| ![](screenshots/patient-dashboard.png)|
|--------------|--------------|--------------|--------------|
| ![](screenshots/appointment-form.png)| ![](screenshots/session-schedule.png)| ![](screenshots/patient-details.png)| ![](screenshots/booking-history.png)|

---

### Development Environment

The platform was developed and tested using the following:
- **Apache Version**: `2.4.39`
- **PHP Version**: `7.3.5`
- **Server Software**: `Apache/2.4.39 (Win64) PHP/7.3.5`
- **MySQL Version**: `5.7.26`

---

## Demo Video:
[Watch the HAMS demo video here! 🎥](https://drive.google.com/file/d/1vB1SSUDrZSAg5rF4-R_DN4LtHqfMZOCO/view?usp=sharing)

---

We hope **HAMS** simplifies clinic appointment scheduling, making healthcare management more efficient for your team.
