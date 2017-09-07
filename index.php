<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

$app = new Application();
$app->register('issue:create')
	->addArgument('title', InputArgument::REQUIRED, "Issue title")
	->addOption('content', 'c', InputOption::VALUE_OPTIONAL, "Issue description", '')
	->addOption('priority', 'p', InputOption::VALUE_OPTIONAL, "Issue priority (trivial, minor, major, critical, blocker)", 'major')
	->addOption('type', 't', InputOption::VALUE_OPTIONAL, "Issue kind (bug, proposal, enhancement, task)", 'bug')
	->addOption('repository', 'r', InputOption::VALUE_OPTIONAL, "Repository")
	->setCode(function(InputInterface $input, OutputInterface $output){
		try{
			$configFilepath = dirname(Phar::running(false)).'/issue.json';
			if(is_file($configFilepath)){
				$config = json_decode(file_get_contents($configFilepath));
				$username = $config->auth->username ?? null;
				$password = $config->auth->password ?? null;
				$repository = $config->repository ?? null;
			}
			$repository = $repository ?? $input->getOption('repository');
			
			$helper = $this->getHelper('question');
			
			if(empty($repository)){
				$question = new Question('Repository: ', '');
				$repository = $helper->ask($input, $output, $question);
			}
				
			if(empty($username)){
				$question = new Question('Username: ', '');
				$username = $helper->ask($input, $output, $question);
			}
			
			if(empty($password)){
				$question = new Question('Password: ', '');
				$question->setHidden(true);
				$password = $helper->ask($input, $output, $question);
			}
			
			if(preg_match("/bitbucket.org/i", $repository)) $url = 'https://api.bitbucket.org/2.0/repositories'.parse_url(rtrim($repository, '.git'), PHP_URL_PATH).'/issues';
			elseif(preg_match("/github.com/i", $repository)) $url = 'https://api.github.com/repos'.parse_url(rtrim($repository, '.git'), PHP_URL_PATH).'/issues';
			else throw new \Exception('Unknown repository');
			
			$data = [
				'title' => $input->getArgument('title'),
				'priority' => $input->getOption('priority'),
				'kind' => $input->getOption('type'),
				'content' => ['raw' => $input->getOption('content')],
			];
			
			$client = new Client();
			$response = $client->request('POST', $url, ['auth' => [$username, $password], 'json' => $data]);
			$issue = json_decode($response->getBody());
			$output->writeln('Issue #'.$issue->id.': "'.$issue->title.'" created');
		}
		catch(\Exception $e){
			$output->writeln($e->getMessage());
		}
	})
	->getApplication()
	->setDefaultCommand('issue:create', true)
	->run();
