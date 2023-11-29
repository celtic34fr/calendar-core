<?php

declare(strict_types=1);

namespace Celtic34fr\CalendarCore;

use Bolt\Extension\BaseExtension;
use Symfony\Component\Filesystem\Filesystem;

/** classe de déclaration et initialisation de l'extension Bolt CMS */
class Extension extends BaseExtension
{
    /**
     * Return the full name of the extension
     */
    public function getName(): string
    {
        return 'Bolt Celtic34fr Calendar Task Extension';
    }

    /**
     * Ran automatically, if the current request is in a browser.
     * You can use this method to set up things in your extension.
     *
     * Note: This runs on every request. Make sure what happens here is quick
     * and efficient.
     */
    public function initialize($cli = false): void
    {
        /** ajout de l'espace de nommage pour accès aux templates de l'extension */
        $this->addTwigNamespace("calendar-core", dirname(__DIR__)."/templates");
    }

    /**
     * Ran automatically, if the current request is from the command line (CLI).
     * You can use this method to set up things in your extension.
     *
     * Note: This runs on every request. Make sure what happens here is quick
     * and efficient.
     */
    public function initializeCli(): void
    {
        // Nothing
    }

    public function install(): void
    {
        $filesystem = new Filesystem();
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        /** test existance calendar-assets/css */
        $source = dirname(__DIR__) . '/public';
        $destination = $projectDir . '/public/calendar-assets';
        if (!$filesystem->exists($destination)) {
            $filesystem->mkdir($destination);
            $filesystem->chgrp($destination, 'www-data', true);
            $filesystem->chmod($destination, 0777);
        }
        $this->doCopy($source, $destination, $filesystem);
    }

    /**
     * @param string $source
     * @param string $destination
     */
    private function doCopy(string $source, string $destination, Filesystem $filesystem): void
    {
        $filesystem->mirror($source, $destination);
    }
}
