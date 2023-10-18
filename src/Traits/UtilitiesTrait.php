<?php

namespace Celtic34fr\CalendarCore\Traits;

use Exception;

trait UtilitiesTrait
{

    /**
     * m"thode de test d'existance d'une table dans l'ORM Doctrine
     * @throws Exception
     */
    public function existsTable(string $table_name): bool
    {
        if ($this->schemaManager) {
            return $this->schemaManager->tablesExist([$table_name]);
        }
        throw new Exception("Can't obtain SchemaManager from EntityManager, verify the database parameters");
    }

    /**
     * méthode de test d'existance de tables dans l'ORM Doctrine
     * @throws Exception
     */
    public function existsTables(array $tables_name): bool
    {
        if ($this->schemaManager) {
        return $this->schemaManager->tablesExist($tables_name);
        }
        throw new Exception("Can't obtain SchemaManager from EntityManager, verify the database parameters");
    }

    /**
     * méthode visant à mettre la première lettre d'un prénom, composé ou non, en majuscule
     */
    public function ucfirstPrenom(string $field) : string
    {
        /** chaîne vide en entrée => erreur : retourne faux (false) */
        if (!$field) {
            return false;
        }

        $tab = explode('-', $field);
        $rlst = "";

        if (sizeof($tab) > 1) {
            foreach ($tab as $elt) {
                $rlst .= ucfirst(trim($elt)). "-";
            }
            $field = substr($rlst, 0, strlen($rlst - 1));
        }
        if (strpos($field, " ")) {
            $tab = explode(" ", $field);
            if (sizeof($tab) > 1) {
                foreach ($tab as $elt) {
                    $rlst .= ucfirst(trim($elt)). " ";
                }
                $field = substr($rlst, 0, strlen($rlst - 1));
            }
        }
        return $field;
    }

}
