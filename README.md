The File Actions app is a scripting tool, which administrators can employ to automate workflows, and to give users of the Nextcloud instance additional custom file actions. The actions are accessible straight from the Files app!

**🌕** Scripting in Lua, its simple and has plenty of online resources.  
**⚡** Make hard tasks easy, straight from the Files app.   
**🙋** Are you missing a function in the scripting API? Open an issue on Github!  
**🤖** Integration with Nextcloud Flow allows you to fully automate workflows

![Screenshot of Files Scripts Version 1.0.0](https://raw.githubusercontent.com/Raudius/files_scripts/master/screenshots/1.png)


## Installation & Configuration

### Required:
  * Nextcloud >=23
  * PHP >=7.4

### Recommended:
The app includes a Lua interpreter which runs on PHP, this interpreter is still [under development](https://github.com/Raudius/Luar) and its use is **not** recommended. For a faster and more reliable solution install the Lua PHP extension.

* Lua + PHP Lua plugin
```shell
sudo apt-get install lua5.3
sudo apt-get install liblua5.3-0
sudo apt-get install liblua5.3-dev
```

<details>
<summary>Lua plugin for PHP 7</summary>

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
<summary>Lua plugin for PHP 8</summary>
Since the Lua plugin is not yet officially supported for PHP8, we need to build it.

```shell
sudo apt-get install php-pear
sudo apt-get install php-dev

cd ~
git clone https://github.com/singlecomm/php-lua.git
cd php-lua
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

### Optional:

<details>
<summary>Some functions require additional packages to be installed on the server</summary>

* QPDF >=9.1.1 (needed for [PDF functions](docs/Functions.md#Pdf))
```shell
sudo apt-get install qpdf
```

* FFmpeg (needed for [FFmpeg](docs/Functions.md#ffmpeg) and [FFprobe](docs/Functions.md#ffprobe))
```shell
sudo apt install ffmpeg
```

</details>

## Documentation

The [Admin documentation](docs/Admin.md) contains information about how to create new actions and some precautions that should be taken when writing one.

The [Scripting API documentation](https://github.com/Raudius/files_scripts/blob/master/docs/Functions.md) contains descriptions for all the API functions as well as some snippets of how they can be used.

## Contributing

Contributions to the app are most welcome!
  * 🌍 **Translations**  
The app can be translated through the [Nextcloud community](https://www.transifex.com/nextcloud/nextcloud/content/) in Transifex
  * 🛠 **Scripting API**  
If there is something you need that is currently not possible (or complex) to do with scripting API, please open a GitHub issue, or even better, a pull request! 
  * 📃 **Documentation**  
The project is still relatively new, and the documentation can still use some work. If you have any suggestions or improvements please do help out!
  * 💸 **Donations**  
Last and least, you can donate your hard-earned cash with [Liberapay](https://liberapay.com/Raudius/donate)
