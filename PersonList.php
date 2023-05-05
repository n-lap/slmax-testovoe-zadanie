<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
    if (!class_exists($class_name, false)) {
        throw new LogicException("Unable to load class: $class_name");
    }
});

class PersonList
{
    private $idPersonArray = [];
    public function __construct($id, $sign = '>')
    {
        if (!class_exists(Person::class)) {
            throw new LogicException("Class Person doesn't exist");
        }

        global $db;
        if (!strcmp($sign, '>')) {
            $personQuery = "SELECT `id` FROM `people_info` WHERE `people_info`.`id` >'$id'";
        } else if (!strcmp($sign, '<')) {
            $personQuery = "SELECT `id` FROM `people_info` WHERE `people_info`.`id` < '$id'";
        } else if (!strcmp($sign, '!=')) {
            $personQuery = "SELECT `id` FROM `people_info` WHERE `people_info`.`id` != '$id'";
        }

        $result = mysqli_fetch_all($db->connect->query($personQuery));
        foreach ($result as $key) {
            $this->idPersonArray[] = $key[0];
        }
    }

    public function getPeopleById()
    {
        global $db;
        $result = [];
        foreach ($this->idPersonArray as $personId) {
            $personQuery = "SELECT * FROM `people_info` WHERE `people_info`.`id` ='$personId'";
            $result[] = mysqli_fetch_assoc($db->connect->query($personQuery));
        }
        $people = [];
        foreach ($result as $key) {
            $people[] = new Person($key["id"]);
        }
        return $people;
    }

    public function deletePeopleFromDataBase()
    {
        foreach($this->getPeopleById() as $key)
        {
            $key->deleteFromDatabase();
        }
    }
}
