# KindWave-web-app

Getting Started
Prerequisites
Before you begin, ensure you have the following installed:

WAMP or XAMPP: A local server environment to run PHP applications.
Git: To clone the repository.
Cloning the Repository
Open your terminal (or Git Bash).
Run the following command to clone the repository:

git clone https://github.com/yourusername/KindWave-web-app.git
Replace yourusername with your GitHub username.
Setting Up the Environment
Navigate to the cloned directory:

cd KindWave-web-app
Move the cloned folder to your WAMP or XAMPP www directory:

For WAMP: C:\wamp64\www\
For XAMPP: C:\xampp\htdocs\
Create a MySQL database:

Open phpMyAdmin by navigating to http://localhost/phpmyadmin.
Create a new database named kindwave_db.
Import the database structure:

In phpMyAdmin, select the kindwave_db database.
Click on the "Import" tab.
Choose the db.sql file from the cloned repository and click "Go" to import the database structure.
Update the database configuration:

Open the config.php file in the project directory.
Modify the database connection settings:

$dbHost = 'localhost';
$dbUsername = 'your_db_username';
$dbPassword = 'your_db_password';
$dbName = 'kindwave_db';

Running the Application
Start WAMP or XAMPP:

Launch the WAMP or XAMPP control panel and ensure the servers (Apache and MySQL) are running.
Open your web browser and navigate to:

http://localhost/KindWave-web-app/
You should see the home page of the KindWave application.


KindWave is a web application for the Rotaract Charity Club, facilitating donations and community support. It connects donors with recipients, allowing easy posting and claiming of essential items like food and clothing. The app includes delivery tracking for donations, chat features for user engagement, and a rating system.

Project Overview: Donation Application for Rotaract Charity Club
Introduction
This project aims to create a comprehensive web application for the Rotaract Charity Club to facilitate donations, reduce food waste, and streamline the connection between donors and recipients. The application serves as a centralized platform where users can easily donate items, request donations, and manage their interactions with the charity club, ultimately enhancing the overall experience for all parties involved.

Key Features
User Authentication

Login/Registration: Users can easily register and log in to their accounts. The application supports both donor and recipient roles during registration.
Profile Management: Users can upload a profile picture and update their personal information.
Dashboard for Different User Roles

Donor Dashboard:

Home: Personalized greeting and navigation links.
Add Donations: A simple form to add donation items, including the ability to upload images.
Live Donations: Display all current donations with options to edit or delete them.
Requests: Manage requests for donations and provide ratings for recipients.
Donation History: View all past donations.
Chat Functionality: Communicate with other users within the platform.
Recipient Dashboard:

View Donations: Browse available donations with filtering options.
My Requests: Track all requests made for donations.
Chat Functionality: Communicate with donors.
Admin Dashboard:

Manage Donations: Oversee and approve donation requests.
View Complaints: Handle user complaints efficiently.
Manage Users: Edit user roles and manage user accounts.
Add Notices: Inform users about important updates.
Delivery Man Dashboard:

Deliveries: Manage all delivery requests and mark them as delivered.
Delivery History: Track past deliveries.
Database Design

The application utilizes a robust relational database to manage user data, donations, requests, and interactions. Key tables include users, donations, requests, deliveries, complaints, and notices.
Interactive User Experience

All interactions, such as switching between login and registration forms, are handled seamlessly using PHP, ensuring a smooth user experience.
Alert messages are used to provide immediate feedback to users, enhancing usability.
Security Measures

User passwords are securely hashed and stored.
Role-based access control ensures that users can only access features relevant to their roles.
Scalability and Extensibility

The application is designed with scalability in mind, allowing for additional features to be added in the future, such as a mobile app or integration with payment gateways.
Technical Specifications
Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Hosting: Can be deployed on platforms such as Heroku, DigitalOcean, or a local server for demonstration purposes.
How to Use the Application
Accessing the Application: Navigate to the homepage (index.html).
User Registration: Click on "Register" to create a new account.
Logging In: Enter credentials to access the donor or recipient dashboard.
Navigating the Dashboard: Use the navigation links to explore features like adding donations or viewing requests.
Managing Donations: Donors can easily add, edit, and delete their donations through the designated pages.
Viewing Donations: Recipients can filter and request donations as needed.
Conclusion
This donation application not only addresses the immediate needs of the Rotaract Charity Club but also contributes to the larger goal of reducing food waste and connecting those in need with generous donors. By providing a user-friendly platform, this application will facilitate an increase in donations and improve overall community engagement.

GitHub Repository
The complete codebase for this project, including all frontend and backend components
