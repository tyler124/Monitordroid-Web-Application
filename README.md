Monitordroid-Web-Application
============================

Server Application for Monitordroid


The goal of this project is to give users the ability to control their Android mobile devices from any web browser. 

Previously it was a major networking challenge to send packets to a device on a 3G/4G data connection due to mobile
firewalls, but Monitordroid navigates around this by using Google's Cloud Messaging API to send remote commands to the
device. 

I own currently own a paid hosting service at http://www.monitordroid.com/ which is a platform for sending commands 
to the device. A Premium Account can currently be purchased for only $9.99 and will allow you to easily connect to your devices without having to worry about creating a database or doing any PHP coding. However, the Monitordroid mobile application can still be controlled for free by the Monitordroid-Web-Application if the proper settings in the code are changed. A guide on setting up a GCM-Capable server can be found at: http://www.androidhive.info/2012/10/android-push-notifications-using-google-cloud-messaging-gcm-php-and-mysql/

After your PHP/MySQL servers are setup, copy the Monitordroid-Web-Application source-code into your web directory and change the db_functions.php and config.php files on the server side to connect to your database. On the mobile-side, change The CommonUtilities class to set your device up to register to your server (http://yourpublicipaddress/register.php). You will also have to change the postData method in each class that posts information to the server (LocationService, Contact, CallLogGetter, etc.) to point towards your public server url or ip address and the respective post-receiving file. 


The reason I decided to make Monitordroid open-source is that I believe it has the potential to become a very useful,
powerful tool if talented developers are able to create new features themselves. The networking and basic feature foundation has been laid and is very stable, giving open-source contributors the freedom to create features for Monitordroid that will truly allow users to control their devices, anywhere. 
