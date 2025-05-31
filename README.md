# SSComputers
It is a website for users to view and purchase laptops, desktops and accessories. It is developed using html ,css, js and php.
🖥️ Online Computer Store
An online store where users can browse, search, and purchase laptops, desktops, and accessories. This full-stack web application is built using HTML, CSS, and JavaScript for the frontend, PHP for the backend, and MySQL as the database. The project runs locally using XAMPP.

🚀 Features
🛒 Browse and search for laptops, desktops, and accessories

🔍 Product details page

👤 User authentication (sign up, login)

🧾 Add to Wishlist, Remove from Wishlist, Add to cart, Remove from cart and checkout functionality

📦 Order management for users

🛠️ Admin panel to manage products

🔐 Basic security (input validation, password hashing, etc.)

🧑‍💻 Tech Stack
Frontend: HTML, CSS, JavaScript

Backend: PHP

Database: MySQL

Server: XAMPP (Apache, MySQL, PHP)

🛠️ Setup Instructions
Clone the repository

bash
Copy
Edit
git clone https://github.com/yourusername/onlinecomputerstore.git
Move project to XAMPP htdocs folder

bash
Copy
Edit
cp -r onlinecomputerstore /path-to-xampp/htdocs/
Start XAMPP

Start Apache and MySQL

Import the database

Open phpMyAdmin: http://localhost/phpmyadmin

Create a new database (e.g., computer_store)

Import the database.sql file from the project

Run the project

Visit http://localhost/onlinecomputerstore/ in your browser

📁 Project Structure
bash
Copy
Edit
/online-computer-store/
│
├── /assets/           # Images, styles, JS
├── /admin/            # Admin dashboard
├── /includes/         # PHP includes (DB connection, header, etc.)
├── /products/         # Product pages
├── /cart/             # Cart and checkout
├── index.php          # Homepage
├── login.php          # Login form
├── register.php       # Registration form
├── database.sql       # MySQL database schema and sample data
📸 Screenshots
(Optional: Add screenshots showing the homepage, product listing, cart, admin panel, etc.)

📌 Notes
This project is designed to run locally on XAMPP.

It is a basic e-commerce prototype meant for learning and demo purposes.

