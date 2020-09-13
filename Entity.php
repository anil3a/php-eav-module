<?php 
namespace EAV;

class Entity
{
    private $id;
    private $relType;
    private $relId;
    private $attributes;
    private $saved;

    private $db;

    /**
     * @param string $name
     */
    public function __construct()
    {
        if( empty( $this->db ) )
		{
            $this->db = new Database;
        }
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

    public function getRelyType()
    {
        return $this->relType;
    }

    public function setRelType($relType)
    {
        $this->relType = $relType;
        return $this;
    }

    public function getRelId()
    {
        return $this->relId;
    }

    public function setRelId($relId)
    {
        $this->relId = $relId;
        return $this;
    }

    public function getSaved()
    {
        return $this->saved;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    public function save()
    {
        $data_entity = [
            'rel_id' => $this->getRelId(),
            'rel_type' => $this->getRelyType(),
        ];

        if( empty( $this->id ) )
        {
            $this->db->insert( "eav_entity", $data_entity );
            $data_entity['id'] = $this->db->insert_id();
            $this->setId( $data_entity['id'] );
            $this->saved = "inserted";
        }
        else {
            $this->db->update( "eav_entity", $data_entity, [ "id" => $this->getId() ] );
            $this->saved = "updated";
        }

        if( !empty( $this->attributes ) )
        {
            foreach( $this->attributes  as $attribute )
            {
                $attribute->setEntityId( $this->getId() )->save();
            }
        }
    }

    public function __toString()
    {
        $text = [ $this->relId .' of '. $this->relType];
        foreach ($this->attributes as $value) {
            $text[] = (string) $value;
        }
        return join(', ', $text);
    }


}