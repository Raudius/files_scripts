The File actions app is a scripting tool, which administrators can employ to give users of the Nextcloud instance additional, customised file actions.The actions are accessible straight from the Files app!

**🌕** Scripting in Lua, its simple and has plenty of online resources.  
**⚡** Make hard tasks easy, straight from the Files app.   
**🙋** Are you missing a function in the scripting API? Open an issue on Github!

![Screenshot of Files Scripts Version 1.0.0](https://raw.githubusercontent.com/Raudius/files_scripts/master/screenshots/1.png)


## Installation & Configuration

### The usual suspects:
  * Nextcloud >=23
  * PHP >=7.3

### Optional:
  * QPDF >=9.1.1 (needed for [PDF functions](docs/Functions.md#Pdf))
```shell
sudo apt-get install qpdf
```

### The big one:
 * Lua + PHP Lua plugin
```shell
sudo apt-get install php-pear
sudo apt-get install php7-dev
sudo apt-get install lua5.3
sudo apt-get install liblua5.3-0
sudo apt-get install liblua5.3-dev

sudo cp /usr/include/lua5.3/lua.h /usr/include
sudo ln -s /usr/include/lua5.3/ /usr/include/lua
sudo cp /usr/lib/x86_64-linux-gnu/liblua5.3.a /usr/lib/liblua.a
sudo cp /usr/lib/x86_64-linux-gnu/liblua5.3.so /usr/lib/liblua.so

sudo pecl install lua-2.0.7
```

## Documentation

The [Admin documentation](docs/Admin.md) contains information about how to create new actions and some precautions that should be taken when writing one.


## Contributing

Contributions to the app are most welcome, the main areas where help is needed are:
  * 🌍 **Translations**  
The app can be translated through the [Nextcloud community](https://www.transifex.com/nextcloud/nextcloud/content/) in Transifex
  * 🛠 **Scripting API**  
If there is something you need that is currently not possible (or complex) to do with scripting API, please open a GitHub ticket, or even better, a pull request! 
  * 📃 **Documentation**  
The documentation of this project is still quite lacking. If you have any suggestions or improvements please do help out!
