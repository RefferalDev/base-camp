<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCustomPostType extends Command
{
    protected function configure()
    {
        $this->setName('make:custom-post-type')
             ->setDescription('Create a new Custom post type with boilerplate')
             ->addArgument('name', InputArgument::REQUIRED, 'Custom post type name')
             ->addOption('plural', null, InputOption::VALUE_OPTIONAL, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name              = strtolower($input->getArgument('name'));
        $filename          = "{$name}.php";
        $custom_post_types = new Local(__DIR__ . '/../config/wp/custom-post-types');
        $filesystem        = new Filesystem($custom_post_types);
        $template          = $filesystem->read('_custom-post-type-template.php');
        $plural            = empty($input->getOption('plural')) ? null : strtolower($input->getOption('plural'));

        // If file already exists
        if ($filesystem->has($filename)) {
            $output->writeln("<error>{$filename} already exists!</error>");

            return 0;
        }
        $content = build_custom_post_type($template, $name, $plural);


        $filesystem->write("{$name}.php", $content);
        $output->writeln("<info>Hello {$name}</info>");
    }
}
