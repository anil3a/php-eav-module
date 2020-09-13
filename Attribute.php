<?php 

namespace EAV;

use SplObjectStorage;

class Attribute
{
    private $id = 0;
    private $name = '';
    private $entityId = 0;
    private $valueType = '';
    private $values = [];
    private $saved = '';

    private $db;

    public function __construct($name, $existCheck = true)
    {
        if( empty( $this->db ) )
		{
            $this->db = new Database;
        }
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        $this->exists();
        return $this;
    }

    public function getValueType()
    {
        return $this->valueType;
    }

    public function setValueType($valueType)
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getSaved()
    {
        return $this->saved;
    }

    public function addValue(Value $value)
    {
        $this->values[] = $value;
        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function save()
    {
        $data_attribute = [
            'entity_id' => $this->getEntityId(),
            'value' => $this->name,
            'value_type' => $this->getValueType()
        ];

        $this->exists();

        if( empty( $this->id ) )
        {
            $this->db->insert( "eav_attribute", $data_attribute );
            $data_attribute['id'] = $this->db->insert_id();
            $this->setId( $data_attribute['id'] );
            $this->saved = "inserted";
        }
        else {
            // $data_attribute['id'] = $this->id;
            $this->db->update( "eav_attribute", $data_attribute, [ "id" => $this->getId() ] );
            $this->saved = "updated";
        }

        if( !empty( $this->values ) )
        {
            foreach( $this->values  as $value )
            {
                $value->setAttributeId( $this->getId() )->setEntityId( $this->getEntityId() )->save($this);
            }
        }
    }

    public function exists()
    {
        if( empty( $this->name ) || empty( $this->entityId ) )
        {
            return false;
        }
        if( !empty( $this->getId() ) )
        {
            return false;
        }
        $data_attribute = [
            'entity_id' => $this->getEntityId(),
            'value' => $this->name,
        ];

        $dbattribute = $this->db->prepareQuery(
            "eav_attribute", null, $data_attribute, "order by id desc limit 1"
        )->result_row();

        return $this->setObject( $dbattribute );
    }

    public function setObject( $dbResult ) 
    {
        foreach( $dbResult  as $k => $v )
        {
            if( $k === "value" )
            {
                $k = "name";
            }
            $fnc = str_replace( [ ' ', '_' ], '', ucwords( $k, "_" ) );
            $var = $fnc;
            $var[0] = strtolower($var[0]);

            if( isset( $this->$var ) )
            {
                $funName = "set". $fnc;
                $this->$funName( $v );
            }
        }
        return $this;
    }

    public function __toString()
    {
        $text = [];
        foreach( $this->values as $value )
        {
            $text[] = sprintf('%s: %s', $this->name, (string) $value );
        }
        return join(', ', $text);
    }



}