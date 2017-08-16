<?php

namespace Drupal\ner_importer;

use Drupal\ner\DefinitionEntity;
use Symfony\Component\Yaml\Yaml;
use Drupal\ner\ObjectEntity;

/**
 * Import NER processed data.
 *
 * @package Drupal\ner_import
 */
class Import
{
    /**
     * @var ObjectEntity[]
     */
    protected $objectEntityList;

    /**
     * @var ObjectEntity
     */
    protected $objectEntity;

    /**
     * Node type config YML structure.
     * Path: resources/node.type._node.yml
     *
     * @var array
     */
    protected $nodeType;

    /**
     * Field node config YML container.
     * All config fields of current nodeType.
     *
     * @var array
     */
    protected $nodeFieldList;

    /**
     * Field node config YML structure.
     * Path: resources/field.field.node._node._field.yml
     *
     * @var array
     */
    protected $nodeField;

    public function __construct()
    {
        $modulePath = drupal_get_path('module','ner_importer');
        $resourcesPath = $modulePath . '/resources';

        $this->nodeType = Yaml::parse($resourcesPath . '/node.type._node.yml');
        $this->nodeField = Yaml::parse($resourcesPath . '/field.field.node._node._field.yml');
    }

    /**
     * Create custom content type using
     * a instance of ner\ObjectEntity.
     *
     * @param ObjectEntity $objectEntity
     * @return array
     */
    public function contentTypeByObjectEntity(ObjectEntity $objectEntity)
    {
        //Define current object entity
        $this->objectEntity = $objectEntity;

        /* TODO: set here call to method for search if current object entity
           type exists on nodeTypeList, and set this node type as default for content type. */

        $this->nodeTypeConfig();

        if(is_array($this->objectEntity->getSubObjectMap()))
            // Definitions of sub-group object.
            foreach ($this->objectEntity->getSubObjectMap() as $subObject)
                $this->nodeFieldListConfig($subObject->getDefinitionMap());

        if(is_array($this->objectEntity->getDefinitionMap()))
            $this->nodeFieldListConfig($this->objectEntity->getDefiniti);
    }

    /**
     * Build node type config using current
     * instance of ner\ObjectEntity
     *
     * @return void
     */
    protected function nodeTypeConfig(){

        $this->nodeType['name'] = TransformImport::nameByString($this->objectEntity->getType());
        $this->nodeType['type'] = TransformImport::idByString($this->objectEntity->getType());
    }

    /**
     *
     * @param DefinitionEntity[] $definitionEntityList
     * @return void
     */
    protected function nodeFieldListConfig(array $definitionEntityList){

        foreach ($definitionEntityList as $definitionEntity)
            $this->nodeFieldConfig($definitionEntity);
    }

    /**
     *
     * @param DefinitionEntity $definitionEntity
     * @return void
     */
    protected function nodeFieldConfig(DefinitionEntity $definitionEntity){

    }
}