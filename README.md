## Village Hall
A php application to help with Village Hall bookings.

### Configuration
The config file is at the root of the application, called `villagehall-config.php`.

### Database
MySql and SqLite are supported, set the correct one in the config file.

If SqLite is used, then the database file is setup in the config file.  Suggest it is placed into a directory at the root
of the application, this directory will need to be writeable by the webserver process `www-data` on debian based distros.
i.e. if your username is fred then create the directory before accessing the application for the first time and issue
```
sudo chown fred:www-data /home/fred/villagehall/db
```
substituting the correct path to the directory.

### Logging
This application includes a logging facility.  It can either log to syslog, or direct to a file. This can be setup in the config file.

Set the minimum level for logging (default is INFO).

For DEBUG level logging a stack trace level can also be set, see the config file for details.

For logging direct to a file, the same procedure as used for setting up an SqLite database will need to be followed
i.e. create a `log` directory and give the web-server permission to write to it.
```
mkdir /home/fred/villagehall/log
sudo chown fred:www-data /home/fred/villagehall/log
```
Log file rotation is setup in the config file.
