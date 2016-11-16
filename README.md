### rsyncer
------------------

#### A thin rsync wrapper class for PHP without any dependencies

##### Requirements
------------------
PHP 5.4+, rsync, composer

##### Installation
------------------
- Use [Composer](https://getcomposer.org/doc/01-basic-usage.md) to install the package

- From project root directory execute

	```composer install```

	or

	```composer require codeplayr/rsyncer```

- [Composer](https://getcomposer.org/doc/01-basic-usage.md) will take care of autoloading. Just include the autoloader at the top of the file

	```require_once __DIR__ . '/vendor/autoload.php';```


##### Usage
------------------

See following example:

```php
use \Codeplayr\Rsyncer\Option;
use \Codeplayr\Rsyncer\SSH;
use \Codeplayr\Rsyncer\Rsync;

$source = __DIR__ . '/src/';
$destination = __DIR__ . '/backup/';

$date = date('Y-m-d', time());

//rsync options
$option = new Option([
	Option::FILES_FROM	=> __DIR__ . '/rules.txt',
	Option::EXCLUDE_FROM=> __DIR__ . '/exclude-rules.txt',
	Option::LOG_FILE	=> __DIR__ . "/logs/{$date}.log",
	Option::ARCHIVE		=> false,
	Option::LINKS		=> true,
	Option::TIMES		=> true,
	Option::RECURSIVE	=> true,
	Option::VERBOSE		=> true,
	Option::COMPRESS	=> true,
	Option::CHECKSUM	=> true,
	Option::DRY_RUN		=> false,
]);

//add additional flags
$option->addFlag('human-readable')
		->addArgument('exclude', '/path/to/exclude')
		->addArgument('include', '*.html')
		->addArgument('include', '*.php')
		->addArgument('include', '*/')
		->addArgument('exclude', '*');		

//optional ssh connection to remote host
$ssh = new SSH([
	SSH::USERNAME		=> 'root',
	SSH::HOST			=> '1.2.3.4',
	SSH::PORT			=> 22,
	SSH::IDENTITY_FILE	=> '/path/to/private/key',
]);

//configuration options
$conf = [
	Rsync::SHOW_OUTPUT	=> true,
];

$rsnyc = new Rsync( $option, $ssh, $conf );

//assemble and show Command
echo $rsnyc->getCommand( $source, $destination );

//start syncing directories
if( ! $rsnyc->sync( $source, $destination ) ){
	echo $rsnyc->getMessage()->toString();
}
```

Running the script generates following rsync command and options:

```shell
rsync
	-ltrvzc
	--human-readable 		
	--files-from="/path/to/rules.txt"
	--exclude-from="/path/to/exclude-rules.txt"
	--log-file="/path/to/logs/2016-10-29.log"
	--exclude='/path/to/exclude'
	--include="*.html"
	--include="*.php"
	--include="*/"
	--exclude="*"
	-e="ssh -i /path/to/your/private/key"
	root@1.2.3.4:"/path/to/src/" "/path/to/backup/"

```

##### Run Tests:
----------

- All tests are inside `tests` folder.
- Execute `composer install --dev phpunit/phpunit` to install phpunit
- Run `phpunit` from inside the tests directory to execute testcase
- Set `--coverage-text` option to show code coverage report in terminal

##### Notes:
----------

If you like this script or have some features to add: contact me, fork this project, send pull requests, you know how it works.
