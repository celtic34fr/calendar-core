<?php

namespace Celtic34fr\CalendarCore\Traits;

trait FormErrorsTrait
{

    private function getErrors($form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $childIdx = $child->getName();
                /**
                if (!is_numeric($childIdx)) {
                    $childIdx = $child->getConfig()->getOption('label');
                }
                */
                $errors[$childIdx] = $this->getErrors($child);
            }
        }
        return $errors;
    }

}
