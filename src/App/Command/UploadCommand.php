<?php

namespace App\Command;

use App\Exception\InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends Command
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progress;

    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('upload')
            ->setDescription('Upload file to Diawi.com')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->progress = null;
        $this->output   = $output;

        $path = $input->getArgument('path');

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf("'%s' is not a file or is not readable", $path));
        }

        if (empty($this->config['token'])) {
            $command = $this->getApplication()->find('configure');
            $command->run(new ArrayInput(['option' => 'token']), $this->output);

            exit(0);
        }

        $this->client = new Client([
            'base_uri' => $this->config['endpoint'],
            'progress' => [$this, 'uploadProgress']
        ]);

        $promise = $this->client->postAsync('/', [
            'multipart' => [
                [
                    'name'     => 'token',
                    'contents' => $this->config['token'],
                ],
                [
                    'name'     => 'find_by_udid',
                    'contents' => isset($this->config['find_by_udid']) ? $this->config['find_by_udid'] : 0,
                ],
                [
                    'name'     => 'wall_of_apps',
                    'contents' => isset($this->config['wall_of_apps']) ? $this->config['wall_of_apps'] : 0,
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($path, 'rb'),
                ],
            ]
        ]);

        $promise->then([$this, 'uploadCompleted'], [$this, 'uploadError']);

        $promise->wait();

        return 0;
    }

    public function uploadProgress($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes)
    {
        if ($this->progress === null && $uploadTotal) {
            $this->progress = new ProgressBar($this->output, $uploadTotal);
            $this->progress->setRedrawFrequency(1);
            $this->progress->setFormat("<info>Uploading file</info>\n [%bar%] %percent:3s%% (%remaining:-5s%)");
            $this->progress->start();
        }

        if ($this->progress !== null) {
            $this->progress->setProgress($uploadedBytes);
        }
    }

    public function uploadCompleted(ResponseInterface $res)
    {
        $this->output->writeln('');
        $this->output->writeln('done');

        if ($res->getStatusCode() === 200) {
            $body = json_decode($res->getBody()->getContents());
        }

        $command = $this->getApplication()->find('status');
        $command->run(new ArrayInput(['job' => $body->job]), $this->output);
    }

    public function uploadError(RequestException $e)
    {
        $this->output->writeln('');
        $this->output->writeln($e->getMessage());
    }
}
