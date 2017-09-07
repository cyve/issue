# bitbucket-issue
Command to create issue in a Bitbucket repository

## Create Phar archive
```
composer install
php -d phar.readonly=off make.php
```

## Configuration (optionnal)
Create a configuration file named `issue.json` in the same directory than the PHAR.
```
{
	"repository": "https://github.com/owner/repository.git",
	"auth": {
		"username": "username",
		"password": "password"
	}
}
```

## Usage
```
php issue.phar [options] [--] <title>

Arguments:
  title                          Issue title

Options:
  -c, --content[=CONTENT]        Issue description [default: ""]
  -p, --priority[=PRIORITY]      Issue priority (trivial, minor, major, critical, blocker) [default: "major"]
  -t, --type[=TYPE]              Issue kind (bug, proposal, enhancement, task) [default: "bug"]
  -r, --repository[=REPOSITORY]  Repository
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
