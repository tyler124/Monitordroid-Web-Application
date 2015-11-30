Monitordroid Web Application
----------------------------
Developers:
Tyler Butler (tyler@monitordroid.com), 
Noah Lowenthal  (noah@monitordroid.com)

Don't want to go through the stress of setting up your own server? Go to https://monitordroid.com and let us do the work for you! 10-day free trial avaliable. 

Requirements: A web server with at least PHP 5.0 and MySQL. We recommend using XAMPP (https://www.apachefriends.org/index.html) to run Monitordroid locally if you don't have your own web server. Ensure that PHP cURL is enabled on your server. 

Instructions:

1) Download the repository and put it into the active public directory on your web server, for example 'htdocs' in XAMPP.

2) Register with Google Cloud Messaging and get your Sender ID and your project API Key (remember these, they will be needed later!). A good tutorial on how to do this can be found in parts 1-4 here: http://www.androidhive.info/2012/10/android-push-notifications-using-google-cloud-messaging-gcm-php-and-mysql/ 

3) Log into your MySQL database and create a database named 'monitordroid'. Select the database and go to the 'Import' tab. Select "Choose file". Go to the 'database' directory of Monitordroid-Web-Application and select the file                     'monitordroidCreateDatabase.sql' and then click 'Ok' to import the SQL file. Make sure that the 'Format' drop-down menu is set to "SQL". Click "Go" at the bottom of the page. This will create two database tables, 'gcm_accounts' to store the accounts used by the web application and their values, and 'gcm_users' which stores all information for registered devices. 

4) Edit the 'config.php' file found in the root directory of Monitordroid-Web-Application. Change the DB_USER and DB_PASSWORD name-value pairs to your MySQL database credentials. Change the GOOGLE_API_KEY name-value pair to the API key you got from Google in step 2. Note: If you choose to name your database something other than 'monitordroid' or changed the 'DB_HOST' value, you will have to change the corresponding values on line 15 in 'db_functions.php'. 

5) Navigate to your server in your web browser. If you're running XAMPP locally, it should be located at: http://localhost/Monitordroid-Web-Application
You will be presented with a login screen if you have setup the web application successfully. The default account is 'admin' with a password of '12345'. 

Now that your web server is ready for devices to be added, go to the README.md for the 'Monitordroid' repository to set up the Monitordroid application on your mobile devices. 

Unfortunately we cannot offer support on our open-source version, but if you believe there is an error in the code or these instructions please let us know by sending us an email at help@monitordroid.com

-Monitordroid Team
