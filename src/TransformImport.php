<?php

namespace Drupal\ner_importer;


/**
 * Utils for transform NER data
 * to Drupal data.
 *
 * @package Drupal\ner_importer
 */
class TransformImport
{

    /**
     *
     * @param string $string
     * @return string
     */
    public static function idByString(string $string) : string{

        $id = str_replace([' ', '-'], ['_', '_'], $string);
        $id = strtolower($id);

        return $id;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public static function nameByString(string $string) : string{

        $name = str_replace(['_'], [' '], $string);
        $name = strtolower($name);
        $name = ucfirst($name);

        return $name;
    }
}