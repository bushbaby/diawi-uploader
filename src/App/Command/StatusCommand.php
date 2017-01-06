<?php

declare(strict_types = 1);

namespace App\Command;

use App\Exception\InvalidArgumentException;
use App\Exception\RuntimeException;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Start to poll to get processing information on a job')
            ->addArgument(
                'job',
                InputArgument::REQUIRED,
                'id of job'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $input->getArgument('job');

        $output->writeln(sprintf("<info>Getting status of job '%s'</info>", $job));

        if (empty($this->config['token'])) {
            $command = $this->getApplication()->find('configure');
            $command->run(new ArrayInput(['option' => 'token']), $output);

            exit(0);
        }


        $result   = null;
        $attempts = 0;

        do {
            $output->write(sprintf('.'));
            try {
                $result = $this->pollStatus($job);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $output->writeln('');
                if ($e->hasResponse()) {
                    $payload = json_decode($e->getResponse()->getBody()->getContents(), true);
                    $output->writeln(sprintf('<error>%s</error>', $payload['message']));
                } else {
                    $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                }

                break;
            } catch (\Exception $e) {
                $output->writeln('');
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

                break;
            }

            if ($attempts > 15) {
                $output->writeln('');
                $output->writeln(sprintf('It is taking an unusual amount of time for diawi to process this job...'));
                $output->writeln(sprintf("We'll stop now, but you can restart by using this command: "));
                $output->writeln(sprintf("diawi-uploader status %s", $job));
                exit(1);
            }

            if (!$result) {
                sleep(1);
            }
        } while (!$result);

        $output->writeln('');

        if ($result) {
            $output->writeln(sprintf("done. You may visit %s", $result));

            exec(sprintf('open %s', escapeshellarg($result)));

            // linux notes
            // exec(sprintf('xdg-open %s', escapeshellarg($result)));
            // exec(sprintf('sensible-browser %s', escapeshellarg($result)));
        }

        return 0;
    }

    private function pollStatus(string $job)
    {
        $this->client = new Client([
            'base_uri' => $this->config['endpoint'],
        ]);

        $response = $this->client->get('/status', [
            'query' => [
                'token' => $this->config['token'],
                'job'   => $job
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $payload = json_decode($response->getBody()->getContents(), true);

            if ($payload['status'] === 2000) {
                return $payload['link'];
            } elseif ($payload['status'] === 2001) {
                return null;
            } else {
                throw new RuntimeException($payload['message']);
            }
        }

        return null;
    }
}
