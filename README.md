Project Setup Instructions
1. Import the Database
To get the project running, you need to import the provided database file into phpMyAdmin:

Open phpMyAdmin in your browser.
Select or create a database.
Use the Import feature and upload the menodarbai.sql file located in the /database folder.
2. User Information
In the file inserted_users.sql, you will find several users with different privileges. Choose the user you want and use their credentials to log in.

3. Accessing the Application
To log in to the application:

Go to localhost/artworks/login.php.
The login form also contains a registration tab where new users can sign up.
Once you register, you will see the new user added to the database.
4. User Roles
There are several primary roles in the system:

Reader: Can view artworks and manage their shopping cart.
Editor: Can modify artworks and also perform reader tasks.
Administrator: Has full control over the system, including managing users and artworks.
Special Roles:
Artist: An artist is both a reader and can act as an author. An author can edit their own work, but readers can only purchase artworks and manage their shopping cart.
Admin: Admins have the highest privileges and can manage everything in the system.
This README should help users get started with the project, covering both the database setup and user roles.
