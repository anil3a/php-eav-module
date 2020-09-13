<?php 
namespace EAV;

class Value
{
    private $id = 0;
    private $name = '';
    private $attributeId = 0;
    private $entityId = 0;
    private $saved = '';

    private $db;

    public function __construct(Attribute $attribute, $name)
    {
        if( empty( $this->db ) )
		{
            $this->db = new Database;
        }
        $this->name = $name;
    
        $valueType = "varchar";
        if( $attribute->getName() == "active" )
        {
            $valueType = "int";
        }
        $attribute->addValue($this)->setValueType( $valueType );
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

    public function getAttributeId()
    {
        return $this->attributeId;
    }

    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
        return $this;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getSaved()
    {
        return $this->saved;
    }

    public function save(Attribute $attribute)
    {
        $data_value = [
            'entity_id' => $this->getEntityId(),
            'attribute_id' => $this->getAttributeId(),
            'value' => $this->name,
        ];

        $this->exists( $attribute );

        if( empty( $this->id ) )
        {
            $this->db->insert( "eav_value_". $attribute->getValueType(), $data_value );
            $data_value['id'] = $this->db->insert_id();
            $this->setId( $data_value['id'] );
            $this->saved = "inserted";
        }
        else {
            // $data_value['id'] = $this->id;
            $this->db->update( "eav_value_". $attribute->getValueType(), $data_value, [ "id" => $this->getId() ] );
            $this->saved = "updated";
        }
    }

    public function exists( Attribute $attribute )
    {
        if( empty( $this->attributeId ) || empty( $this->entityId ) )
        {
            return false;
        }
        $data_value = [
            'entity_id' => $this->getEntityId(),
            'attribute_id' => $this->getAttributeId(),
        ];

        $dbvalue = $this->db->prepareQuery(
            "eav_value_". $attribute->getValueType(), null, $data_value, "order by id desc limit 1"
        )->result_row();

        if( empty( $dbvalue ) )
        {
            return false;
        }

        return $this->setObject( $dbvalue );
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
        return (string) $this->name;
        // return sprintf('%s: %s', (string) $this->attribute, $this->name);
    }



}