<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
    if (!class_exists($class_name, false)) {
        throw new LogicException("Unable to load class: $class_name");
    }
});

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new DatabaseConnection;

class Person
{
    private $id;
    private $name;
    private $surname;
    private $dateOfBirth;
    private $gender;
    private $cityOfBirth;

    public function __construct($id)
    {
        global $db;
        if (func_num_args() > 1) {
            if ($this->isPersonValid(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3))) {
                $dateFormat = 'Y-m-d';
                $this->name = mysqli_real_escape_string($db->connect, func_get_arg(0));
                $this->surname = mysqli_real_escape_string($db->connect, func_get_arg(1));
                $dateOfBirth = date_format(date_create(mysqli_real_escape_string($db->connect, func_get_arg(2))), $dateFormat);
                $this->dateOfBirth = $dateOfBirth;
                $this->gender = mysqli_real_escape_string($db->connect, func_get_arg(3));
                $this->cityOfBirth = mysqli_real_escape_string($db->connect, func_get_arg(4));
                $this->saveToDatabase();
                $this->id = $db->connect->insert_id;
            }
        } else {
            $this->id = mysqli_real_escape_string($db->connect, $id);
            $personQuery = "SELECT * FROM people_info WHERE id =$id";
            $array = mysqli_fetch_array($db->connect->query($personQuery));
            if(!$array){
                throw new LogicException("There is no person with such id: $id");
            }
            $this->name = $array['name'];
            $this->surname = $array['surname'];
            $this->dateOfBirth = $array['date_of_birth'];
            $this->gender = $array['gender'];
            $this->cityOfBirth = $array['city_of_birth'];
        }
    }

    public function saveToDatabase()
    {
        global $db;
        $personQuery = "INSERT INTO `people_info` (`id`, `name`, `surname`, `date_of_birth`, `gender`, `city_of_birth`) VALUES (NULL, '$this->name', '$this->surname', '$this->dateOfBirth', '$this->gender', '$this->cityOfBirth')";
        return $db->connect->query($personQuery);
    }

    public function deleteFromDatabase()
    {
        global $db;
        $personQuery = "DELETE FROM `people_info` WHERE `people_info`.`id` ='$this->id'";
        return $db->connect->query($personQuery);
    }

    public static function dateOfBirthToAge($dateOfBirth)
    {
        $bday = new DateTime($dateOfBirth);
        $today = new Datetime(date('m.d.y'));
        $diff = $today->diff($bday);
        return $diff->y;
    }

    public static function genderFromBinaryToText($gender)
    {
        if ($gender == 1) {
            return 'man';
        } else if ($gender == 0) {
            return 'woman';
        } else {
            throw new LogicException("Gender must be 1 or 0");
        }
    }

    public function formattingAgeAndGenderOfPerson($IsFormattingAge = false, $IsFormattingGender = false)
    {
        $formattedPeson = new stdClass;
        $formattedPeson->id = $this->id;
        $formattedPeson->name = $this->name;
        $formattedPeson->surname = $this->surname;
        if ($IsFormattingAge) {
            $formattedPeson->dateOfBirth = Person::dateOfBirthToAge($this->dateOfBirth);
        } else {
            $formattedPeson->dateOfBirth = $this->dateOfBirth;
        }
        if ($IsFormattingGender) {
            $formattedPeson->gender = Person::genderFromBinaryToText($this->gender);
        } else {
            $formattedPeson->gender = $this->gender;
        }
        return $formattedPeson;
    }

    private function isPersonValid($name, $surname, $dateOfBirth, $gender)
    {
        $lettersOnly = "/^[a-zA-Z]+$/";
        if (!preg_match($lettersOnly, $name)) {
            throw new LogicException("Name must contain only letters");
        }
        if (!preg_match($lettersOnly, $surname)) {
            throw new LogicException("Surname must contain only letters");
        }
        if (!($gender == 1 or $gender == 0)) {
            throw new LogicException("Gender must be 1 or 0");
        }
        if (!date_create($dateOfBirth)) {
            throw new LogicException("DateOfBirth must be in date format");
        }
        return true;
    }
}
