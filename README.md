### rsyncer
------------------

#### A lightweight rsync wrapper for PHP without dependencies

##### Requirements
------------------
PHP 5.4+, rsync

##### Installation
	composer require codeplayr/rsyncer dev-master

##### Usage
------------------

Generate following rsync command and options:

```shell
rsync
	-ltrvzc
	--human-readable
	--files-from="/path/to/rules.txt"
	--exclude-from="/path/to/exclude-rules.txt"
	--log-file="/path/to/logs/2016-10-29.log"
	-e="ssh"
	root@1.2.3.4:"/path/to/src/" "/path/to/backup/"
```

Code:

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

//add additional options
$option->addOption('human-readable');

//optional ssh connection to remote host
$ssh = new SSH([
	SSH::USERNAME	=> 'root',
	SSH::HOST		=> '1.2.3.4',
	SSH::PORT		=> 22,
]);

//configuration options
$conf = [
	Rsync::SHOW_OUTPUT	=> true,
];

$rsnyc = new Rsync( $option, $ssh, $conf );

//assemble and show command
echo $rsnyc->getCommand( $source, $destination );

//start sync directories
if( ! $rsnyc->sync( $source, $destination ) ){
	//print error message
	echo $rsnyc->getMessage()->toString();

	//...
}
```
