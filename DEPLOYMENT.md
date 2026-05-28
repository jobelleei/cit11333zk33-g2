# InfinityFree Deployment Guide

Follow these step-by-step instructions to upload your student dashboard project to **InfinityFree** and make it live.

---

## 1. Register & Create a Hosting Account on InfinityFree

1. Go to [InfinityFree](https://www.infinityfree.com/) and register for a free account.
2. Once logged in, click **Create Account**.
3. Choose **Custom Domain** or **Free Subdomain**:
   - Select the subdomain extension: `infinityfreeapp.com`
   - Set the subdomain name: **`cit11333zk33-group2`**
   - The final URL will be: **`cit11333zk33-group2.infinityfreeapp.com`**
4. Complete the account setup and wait a few minutes for the status to change to **Active**.

---

## 2. Set Up the Database

1. From the InfinityFree client area, click **Manage** next to your hosting account, then open the **Control Panel** (vPanel).
2. Search for and click on **MySQL Databases** under the Database section.
3. Create a new database named `students_db`. The system will automatically prepend your username, creating a database like `epiz_XXXXXXXX_students_db`.
4. Note down your database credentials from the **MySQL Databases** page:
   - **MySQL Host Name** (e.g., `sql309.infinityfree.com`)
   - **MySQL User Name** (e.g., `epiz_31234567`)
   - **MySQL Database Name** (e.g., `epiz_31234567_students_db`)
   - **MySQL Password** (Your InfinityFree account/vPanel password, which can be found in your InfinityFree client area under Account Details).

---

## 3. Import the Database Schema

1. In the Control Panel, go to **phpMyAdmin** and click **Connect** next to your newly created database.
2. Select your database name on the left sidebar.
3. Click the **Import** tab at the top menu.
4. Click **Choose File** and select the [schema.sql](schema.sql) file located in the root of your project folder.
5. Click **Go** at the bottom of the page to run the script. This creates the tables (`users`, `subjects`, `grades`) and inserts the default student account.

---

## 4. Configure database credentials in `config.php`

The [config.php](config.php) file has been modified to automatically detect whether you are running locally (XAMPP) or on the live server. You only need to configure the live server credentials in the `else` block:

1. Open [config.php](config.php).
2. Locate the `else` block (lines 20–29):
   ```php
   } else {
       // InfinityFree Live Server Database Configuration
       // Replace these values with your actual database details from the InfinityFree Control Panel
       $config = [
           'host'     => 'sqlXXX.infinityfree.com', // E.g., sql309.infinityfree.com
           'dbname'   => 'epiz_XXX_students_db',    // E.g., epiz_31234567_students_db
           'username' => 'epiz_XXX',                // E.g., epiz_31234567
           'password' => 'your_epiz_password',      // Your InfinityFree Client Area Password
       ];
   }
   ```
3. Update these placeholders with the live database credentials you noted down in Step 2.
4. Save the file.

---

## 5. Upload Files via FTP or File Manager

1. Open the **Online File Manager** in the InfinityFree client area (or use an FTP client like **FileZilla** using the FTP credentials shown in your account details).
2. Navigate to the **`htdocs/`** directory.
3. Upload all the files and folders of your project directly inside the `htdocs/` folder.
   - **Important:** Make sure `index.php` is directly in the `htdocs/` directory, not in a nested folder (e.g. `htdocs/index.php` is correct; `htdocs/student-dashboard-phpbuild-main/index.php` is incorrect).
   - Upload the following structure into `htdocs/`:
     ```text
     htdocs/
     ├── admin/
     │   ├── auth.php
     │   ├── footer.php
     │   ├── grades.php
     │   ├── header.php
     │   ├── index.php
     │   ├── logout.php
     │   └── subjects.php
     ├── classes/
     │   ├── BaseModel.php
     │   ├── Database.php
     │   ├── QueryBuilder.php
     │   └── User.php
     ├── src/
     │   ├── assets/
     │   │   └── images/
     │   │       └── hiro-avatar.png
     │   └── style/
     │       ├── admin-styles.css
     │       ├── global.css
     │       └── login-styles.css
     ├── config.php
     ├── index.php
     └── schema.sql
     ```

---

## 6. Access and Verify the Site

1. Open your browser and navigate to:
   `http://cit11333zk33-group2.infinityfreeapp.com`
2. Test signing in with the default credentials:
   - **Username:** `admin`
   - **Password:** `admin123`
3. Verify that the dashboard, personal profile details, subjects, and grades load correctly.
