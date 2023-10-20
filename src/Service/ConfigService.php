<?php

namespace Celtic34fr\CalendarCore\Service;

use Bolt\Extension\BaseExtension;
use Bolt\Extension\ExtensionRegistry;
use Celtic34fr\CalendarCore\Extension;

class ConfigService
{
    private ExtensionRegistry $registry;
    private array $config;

    public function __construct(ExtensionRegistry $registry)
    {
        $this->registry = $registry;
        $this->config = $this->getConfigArray();
    }

    protected function getConfigArray() : array
    {
        if(!empty($this->config)) {
            return $this->config;
        }

        /** @var BaseExtension $extension */
        $extension = $this->registry->getExtension(Extension::class);
        if($extension === null) {
            return [];
        }

        $configCollection = $extension->getConfig();
        $config = [];
        foreach ($configCollection as $key => $item) {
            foreach ($item as $param => $value) {
                $config[$key][$param] = $this->prepareValue($value);
            }
        }
        return $config;
    }

    protected function prepareValue($value)
    {
        if(preg_match('#%env\((.*)\)%#', $value, $matches)) {
            $value = $_ENV[$matches[1]] ?: $value;
        }
        return $value;
    }

    // Exemple de méthode d’accès aux paramètres ici clé d’accès ‘mailer’
    public function getConnexionParams()
    {
        $this->config = $this->getConfigArray();
        return $this->config['mailer'];
    }
}