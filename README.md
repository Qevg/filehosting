# Filehosting

## Used technologies
1. [Twitter Bootstrap]
2. [Slim] micro framework
3. [Redis] key-value NoSQL database
4. [Pimple] dependency injection container
5. [Twig] template engine
6. [Sphinx] search engine
7. [jQuery] javascript library
8. [GetId3] media file parser
9. [Codeception] testing PHP framework
10. [Selenium] automates browsers

## Requirements
1. [PHP] >= 7.1
2. [PostgreSQL]
3. [Sphinx]
4. [Composer]

## Installation
1. Use the `git clone https://github.com/Qevg/filehosting.git` command to clone the repository
2. Use the `composer install` command to install dependencies
3. Change configuration in the `config/config_production.json` and `config/sphinx.conf`
4. Сonfigure the web server [as specified here]
5. Set `public` directory as a document root on your web server
6. Set `upload max file size` on your web server. The value must match the `maxFileSize` parameter in the `config/config_production.json`
7. Set `post max size` on your web server. The value must be greater than `upload max file size`
7. Import database `filehosting.sql` on your database
8. [Initialize search indexes] with the `indexer --config config/sphinx.conf --all` command
9. [Start sphinx service] with the `searchd --config config/sphinx.conf` command

## Additional Features
### XSendfile
If your server has `XSendFile` module installed and configured, then you can enable it in the `config/config_production.json` file, setting the `XSendFile` option to `on`. If you're using Nginx don't forget to set `storage` folder [as internal] in your config file.

Example:
```
location /storage {
    internal;
    root /path/to/project;
}
```

## Tests
1. Set environment to `testing` in the `config/config.json`
2. Create test database for tests
3. Change configuration in the `config/config_testing.json`, `config/sphinx_testing.conf`, `codeception.yml`, `tests/*.suite.yml`
    * [configure acceptance testing]
4. Stop sphinx service if it running
5. [Initialize search indexes] with the `indexer --config config/sphinx_testing.conf --all` command
6. [Start sphinx service] with the `searchd --config config/sphinx_testing.conf` command

##### Command to run the tests:
```
$ codecept run
```

## License
This application is licensed under the MIT license. For more information refer to [License file].

[Twitter Bootstrap]: <https://getbootstrap.com/>
[Slim]: <https://www.slimframework.com/>
[Redis]: <https://redis.io/>
[Pimple]: <https://pimple.symfony.com/>
[Twig]: <https://twig.symfony.com/>
[Sphinx]: <http://sphinxsearch.com/>
[jQuery]: <https://jquery.org/>
[GetId3]: <http://getid3.sourceforge.net/>
[Codeception]: <https://codeception.com/>
[Selenium]: <https://www.seleniumhq.org/>
[PHP]: <https://secure.php.net/>
[PostgreSQL]: <https://www.postgresql.org/>
[Composer]: <https://getcomposer.org/>
[as specified here]: <https://www.slimframework.com/docs/v3/start/web-servers.html>
[Initialize search indexes]: <http://sphinxsearch.com/docs/current/ref-indexer.html>
[Start sphinx service]: <http://sphinxsearch.com/docs/current/ref-searchd.html>
[as internal]: <https://nginx.org/en/docs/http/ngx_http_core_module.html#internal>
[configure acceptance testing]: <https://codeception.com/docs/03-AcceptanceTests#selenium-server>
[License file]: <https://github.com/Qevg/filehosting/blob/master/LICENSE>