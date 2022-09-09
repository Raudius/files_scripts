The File actions app is a scripting tool, which administrators can employ to give users of the Nextcloud instance additional, customised file actions.The actions are accessible straight from the Files app!

**🌕** Scripting in Lua, its simple and has plenty of online resources.  
**⚡** Make hard tasks easy, straight from the Files app.   
**🙋** Are you missing a function in the scripting API? Open an issue on Github!

![Screenshot of Files Scripts Version 1.0.0](https://raw.githubusercontent.com/Raudius/files_scripts/master/screenshots/1.png)


## Installation & Configuration

### The usual suspects:
  * Nextcloud >=23
  * PHP >=7.4

### Optional:
Some functions require additional packages to be installed on the server

* QPDF >=9.1.1 (needed for [PDF functions](docs/Functions.md#Pdf))
```shell
sudo apt-get install qpdf
```

* FFmpeg (needed for [FFmpeg](docs/Functions.md#ffmpeg) and [FFprobe](docs/Functions.md#ffprobe))
```shell
sudo apt install ffmpeg
```

### The big one:
 * Lua + PHP Lua plugin
```shell
sudo apt-get install lua5.3
sudo apt-get install liblua5.3-0
sudo apt-get install liblua5.3-dev
```

<details>
<summary>Lua plugin for PHP7</summary>

```shell
sudo apt-get install php-pear
sudo apt-get install php7-dev

sudo cp /usr/include/lua5.3/lua.h /usr/include
sudo ln -s /usr/include/lua5.3/ /usr/include/lua
sudo cp /usr/lib/x86_64-linux-gnu/liblua5.3.a /usr/lib/liblua.a
sudo cp /usr/lib/x86_64-linux-gnu/liblua5.3.so /usr/lib/liblua.so

sudo pecl install lua-2.0.7
```

</details>

<details>
<summary>Lua plugin for PHP8</summary>
Since the Lua plugin is not yet officially supported for PHP8, we need to build it.

```shell
sudo apt-get install php-pear
sudo apt-get install php-dev

cd ~
git clone https://github.com/singlecomm/php-lua.git
git checkout php8
phpize && ./configure --with-lua-version=5.3
make

# The destination path may change depending on your PHP version
# You can find your extension directory by using:
# php -i | grep extension_dir
sudo cp ./.libs/lua.so /usr/lib/php/20200930/
```

</details>


Don't forget to append `extension=lua.so` to your php.ini!

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
