<?php

namespace Drupal\ner_import;

use Drupal\content_type_tool\CreateContentType;
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
    protected $contentTypeList;

    /**
     * Current ContentType instance
     *
     * @var CreateContentType
     */
    protected $contentType;

    public function __construct()
    {
        $this->contentTypeList = [];
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
        $this->contentType = new CreateContentType();
        $this->contentTypeList[] = $this->contentType;

        $this->nodeTypeConfig();
        $this->nodeFieldBodyConfig();

        if(is_array($this->objectEntity->getSubObjectMap()))
            foreach ($this->objectEntity->getSubObjectMap() as $subObject)
                $this->nodeFieldListConfigByDefinitionEntityList($subObject->getDefinitionMap());

        if(is_array($this->objectEntity->getDefinitionMap()))
            $this->nodeFieldListConfigByDefinitionEntityList($this->objectEntity->getDefinitionMap());

        $this->contentType->setEntityDisplay();
        $this->contentType->save();
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

        // TODO: Update this
        /*if(isset($this->contentTypeList[$nodeId]))
            return;*/

        $nodeType['type'] = $nodeId;
        $nodeType['name'] = TransformImport::nameByString($this->objectEntity->getType());

        $this->contentType->setNodeType($nodeType);
    }

    /**
     * Build node type config using current
     * node type config
     *
     * @return void
     */
    protected function nodeFieldBodyConfig(){

        $this->contentType->addFieldBody();
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

        $nodeField['field_name'] = 'field_' . TransformImport::idByString($this->definitionEntity->getSortId() . '.' . $this->propertyDefinitionEntity->getProperty());
        $nodeField['label'] = TransformImport::nameByString($nodeField['field_name']);
        $fieldId = $this->contentType->addField($nodeField, 'string_textfield');

        // TODO: update this
        //$this->contentFieldListMap[$fieldId] = $fieldId;
    }
}