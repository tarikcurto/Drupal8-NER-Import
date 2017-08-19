<?php

namespace Drupal\ner_importer;

use Drupal\custom_content_type\CreateContentType;
use Drupal\ner\DefinitionEntity;
use Drupal\ner\PropertyDefinitionEntity;
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
     * @var DefinitionEntity
     */
    protected $definitionEntity;

    /**
     * @var PropertyDefinitionEntity
     */
    protected $propertyDefinitionEntity;

    /**
     * All config nodes of current object
     * instance.
     *
     * [nodeId => node]
     *
     * @var CreateContentType[]
     */
    protected $contentTypeMap;

    /**
     * Current ContentType instance
     *
     * @var CreateContentType
     */
    protected $contentType;

    /**
     * Memory of processed fields by
     * by CreateContentType instances.
     *
     * [nodeId => [fieldId, ...]]
     *
     * @var array[]
     */
    protected $contentFieldListMap;

    public function __construct()
    {
        $this->contentTypeMap = [];
        $this->contentFieldListMap = [];
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
        $this->objectEntity = $objectEntity;
        $this->nodeTypeConfig();

        $this->contentTypeMap[] = $this->contentType;
        $this->contentFieldListMap[$this->contentType->getNodeTypeId()] = [];

        if(is_array($this->objectEntity->getSubObjectMap()))
            foreach ($this->objectEntity->getSubObjectMap() as $subObject)
                $this->nodeFieldListConfigByDefinitionEntityList($subObject->getDefinitionMap());

        if(is_array($this->objectEntity->getDefinitionMap()))
            $this->nodeFieldListConfigByDefinitionEntityList($this->objectEntity->getDefinitionMap());
    }

    /**
     * Build node type config using current
     * instance of ner\ObjectEntity
     *
     * @return void
     */
    protected function nodeTypeConfig(){

        $nodeType = [];
        $nodeId = TransformImport::idByString($this->objectEntity->getType());

        if(isset($this->contentTypeMap[$nodeId]))
            return;

        $nodeType['type'] = $nodeId;
        $nodeType['name'] = TransformImport::nameByString($this->objectEntity->getType());

        $this->contentType = new CreateContentType($nodeType);
    }

    /**
     *
     * @param DefinitionEntity[] $definitionEntityList
     * @return void
     */
    protected function nodeFieldListConfigByDefinitionEntityList(array $definitionEntityList){

        foreach ($definitionEntityList as $definitionEntity)
            $this->nodeFieldListConfigByDefinitionEntity($definitionEntity);
    }

    /**
     *
     * @param DefinitionEntity $definitionEntity
     * @return void
     */
    protected function nodeFieldListConfigByDefinitionEntity(DefinitionEntity $definitionEntity){

        $this->definitionEntity = $definitionEntity;
        $this->nodeFieldListConfigByPropertyDefinitionEntityList($definitionEntity->getPropertyDefinitionMap());
    }

    /**
     *
     * @param PropertyDefinitionEntity[] $propertyDefinitionEntityList
     * @return void
     */
    protected function nodeFieldListConfigByPropertyDefinitionEntityList(array $propertyDefinitionEntityList){

        foreach ($propertyDefinitionEntityList as $propertyDefinitionEntity)
            $this->nodeFieldConfigByPropertyDefinitionEntity($propertyDefinitionEntity);
    }

    /**
     *
     * @param PropertyDefinitionEntity $propertyDefinitionEntity
     * @return void
     */
    protected function nodeFieldConfigByPropertyDefinitionEntity(PropertyDefinitionEntity $propertyDefinitionEntity){

        $this->propertyDefinitionEntity = $propertyDefinitionEntity;

        $fieldName = TransformImport::idByString($this->definitionEntity->getSortId() . '.' . $this->propertyDefinitionEntity->getProperty());
        $fieldId =  'node.' . $this->nodeType['type'] . '.' . $fieldName;

        if(isset($this->contentFieldListMap[$fieldId]))
            return;

        $nodeField['field_name'] = $fieldName;
        $nodeField['id'] = $fieldId;
        $nodeField['label'] = TransformImport::nameByString($fieldName);
        $this->contentType->addNodeField($nodeField);

        $this->contentFieldListMap[$this->contentType->getNodeTypeId()][] = $fieldId;
    }
}