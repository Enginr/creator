<?php

namespace Enginr;

use splitbrain\phpcli\{CLI, Options};

class Manager extends CLI {
    const ENGINR_VERSION = 'v1.0.1-alpha';

    protected function setup(Options $opt): void {
        $opt->setHelp('An Enginr project initializer');

        $opt->registerOption('version', 'Print version', 'v');

        $opt->registerCommand('create', 'Create a new project');
        $opt->registerArgument('name', 'The name of your project', TRUE, 'create');
    }

    protected function main(Options $opt): int {
        if ($opt->getOpt('version')) {
            $this->info('Enginr v1.0.0-alpha');
            return 0;
        }

        if ($opt->getCmd('create')) {
            $this->_create($opt);
            return 0;
        }

        echo $opt->help();
        return 0;
    }

    private function _create(Options $opt): void {
        $dir = $opt->getArgs('name')[0];

        if (!mkdir("./$dir"))
            die($this->error("Could not create project '$dir'. Maybe this directory already exists !"));

        $this->success("Directory $dir successfully created.");
        $this->info('Launching composer initialization ...');

        if (system('cd ./' . $dir . '&& composer init') === FALSE)
            die($this->error('Could not init composer'));

        $this->success('Composer successfully initialized.');
        $this->info('Trying to install Enginr ...');

        if (system('cd ./' . $dir . '&& composer require enginr/enginr ' . self::ENGINR_VERSION) === FALSE)
            die($this->error('Could not install enginr/enginr.'));

        $this->success('enginr/enginr successfully installed.');
        $this->info('Trying to install pug-php/pug ...');

        if (system('cd ./' . $dir . '&& composer require pug-php/pug') === FALSE)
            die($this->error('Could not install pug-php/pug.'));

        $this->success('pug-php/pug successfully installed.');
        $this->info('Generating the project template ...');

        $this->_rcopy(__DIR__ . '/../template', "./$dir");

        $this->success('Template successfully generated.');
        $this->info('Creating .gitignore ...');

        $content = "# Composer modules\n";
        $content .= "/vendor\n\n";
        $content .= "# Project config\n";
        $content .= "/env.json\n";

        if (!fwrite(fopen("./$dir/.gitignore", 'w'), $content, strlen($content)))
            die($this->error('Could not create .gitignore file.'));

        $this->success('.gitignore successfully created.');
        $this->info('Trying to init git ...');

        if (system('cd ./' . $dir . '&& git init') === FALSE)
            die($this->error('Could not init git.'));

        $this->success('Git was successfully initialized.');
        $this->info('Trying to commit project ...');

        if (system('cd ./' . $dir . '&& git add . && git commit -m "Initialized project structure"') === FALSE)
            die($this->error('Could not init git.'));

        $this->success('Commit successfully created.');
        $this->success('Project generating complete.');
        $this->notice("You can go to your project at ./$dir and run 'php app.php' !");
    }

    private function _rcopy(string $src, string $dst): void {
        if (is_dir($src)) {
            if (!file_exists($dst)) mkdir($dst, 0777, true);

            foreach (scandir($src) as $file) {
                if ($file == '.' || $file == '..') continue;
                $this->_rcopy("$src/$file", "$dst/$file");
            }
        } else copy($src, $dst);
    }
}