# HLP-Nebula

**HLP-Nebula** is a **FreeSpace 2** mod repository manager.

This project has two main goals :
* Provide a **release management tool** for **modders**
* Make **mod download and install** process **easier to players**

**HLP-Nebula** is mostly written in **PHP**, as a [**Symfony2**](http://symfony.com/) framework bundle.

## Dependencies

To run **HLP-Nebula**, the first thing you need is a working **LAMP environment**.
On **Ubuntu**, use command :  
```sudo apt-get install apache2 php5 mysql-server libapache2-mod-php5 php5-mysql php5-gd```

## Install

1. If you have not done it yet, **download** the [latest version of **HLP-Nebula**](https://github.com/Hellzed/hlp-nebula).
2. **Move** its root ```Symfony``` folder directly to your **Apache2 host folder**, and select ```Symfony``` as your **current working directory**.
3. **Rename** ```app/config/parameters.yml.dist``` to ```app/config/parameters.yml```
4. **Edit** ```app/config/parameters.yml``` and change these settings :
   ```yaml
   database_host: 127.0.0.1
   database_port: null
   database_name: symfony
   database_user: root
   database_password: yourpassword
   ```
   
5. **Install Symfony "vendors"** (you may need to use ```sudo``` on Ubuntu desktop) :
   ```bash
   php composer.phar install
   ```
6. **Allow Symfony to write** to its ```app/cache``` and ```app/logs``` :
   ```bash
   sudo chmod 777 -R app/cache
   sudo chmod 777 -R app/logs
   ```
   
7. **Initialise Doctrine2 ORM**, **Symfony2**'s database component (and register HLP-Nebula objects in Doctrine2) :
   ```bash
   php app/console doctrine:database:create
   php app/console doctrine:schema:update --force
   ```

If **Symfony2** is installed at your **Apache2 host folder root**, this link should now work :
[localhost/Symfony/web/app_dev.php/nebula](http://localhost/Symfony/web/app_dev.php/nebula/)

## Quick start

1. **Register** on this page : [localhost/Symfony/web/app_dev.php/nebula/register](http://localhost/Symfony/web/app_dev.php/nebula/register)
2. Once connected, access your **personal mod repository** by clicking the link on the right side of the nav bar.

## Client (FreeSpace 2 mod downloader/installer/launcher)

**HLP-Nebula** is developed jointly with [**Knossos**](https://github.com/ngld/knossos), but any FreeSpace 2 mod downloader/installer implementing this [schema](https://github.com/ngld/knossos/blob/develop/converter/schema.txt) can become a client ([**ALPHA**](http://www.hard-light.net/forums/index.php?topic=88119.0) FreeSpace 2 launcher developer contributed to the JSON format).

**HLP-Nebula** could add ome _limited_ support for legacy TXT mod repository configuration files, if needed.

## Development

**HLP-Nebula** is in _active development_. It is still missing a lot of features.
The official [development thread](http://www.hard-light.net/forums/index.php?topic=86364) is found on [**Hard Light Productions**](www.hard-light.com), FreeSpace 2 community.

###To-do list :###

**Modders site :**
* User management : DONE
* Mod/branch/build registration : DONE
* JSON repository config file generation : DONE
* User/mod/branch/build detail pages and activity log : WIP
* JSON repository validation (to enable md5sum checks on client side) : WIP
* Main pages (getting started, mods/modders global list...) : NOT STARTED

**Players site :**
* Everything : NOT STARTED

## License

**HLP-Nebula** is licensed under the [European Union Public License, Version 1.1](LICENSE).  
**Symfony** is licensed under the [MIT License](LICENSE).
