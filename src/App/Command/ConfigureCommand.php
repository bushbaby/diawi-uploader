<?php

declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Zend\Stdlib\ArrayUtils;

class ConfigureCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('Configures this application')
            ->addArgument(
                'option',
                InputArgument::OPTIONAL,
                'Token'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = [];
        $helper   = $this->getHelper('question');

        switch ($input->getArgument('option')) {
            case 'token':
                $question = new Question('Enter API token: ');
                $options  = ['token' => $helper->ask($input, $output, $question)];

                break;
            case 'find_by_udid':
                $question = new ConfirmationQuestion('Should your apps be findable by udid? (n) ', false);
                $options  = ['find_by_udid' => $helper->ask($input, $output, $question)];
                break;
            case 'wall_of_apps':
                $question = new ConfirmationQuestion('Should your apps be placed on the wall of apps? (n) ', false);
                $options  = ['wall_of_apps' => $helper->ask($input, $output, $question)];
                break;
            default:
                $config = $this->readConfig()['diawi'];
                foreach($config as $key=>$value) {
                    $output->writeln(sprintf("%s : %s", $key, $value));
                }
                exit(0);
        }

        $config = ArrayUtils::merge($this->readConfig(), ['diawi' => $options]);

        $this->writeConfig($config);

        return 0;
    }

    protected function readConfig()
    {
        if (($home = $this->getHome()) && file_exists($home . '/.diawi-uploader.php')) {
            return include $home . '/.diawi-uploader.php';
        } else {
            return ['diawi' => []];
        }
    }

    private function writeConfig(array $config)
    {
        if ($home = $this->getHome()) {
            file_put_contents($home . '/.diawi-uploader.php', '<?php return ' . var_export($config, true) . ';');
        }
    }

    private function getHome()
    {
        if (isset($_SERVER['HOME'])) {
            return $_SERVER['HOME'];
        } elseif (isset($_SERVER['HOMEPATH'])) {
            return $_SERVER['HOMEPATH'];
        }

        return false;
    }
}
