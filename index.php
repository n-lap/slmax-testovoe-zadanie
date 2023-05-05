<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    try {
        spl_autoload_register(function ($class_name) {
            include $class_name . '.php';
            if (!class_exists($class_name, false)) {
                throw new LogicException("Unable to load class: $class_name");
            }
        });
        $db = new DatabaseConnection;
        $person1 = new Person("Ivan", "Smirnov", "21.11.1995", 1, "Minsk");
        $person2 = new Person("Egor", "Pechkin", "01/11/1965", 1, "Minsk");
        $person3 = new Person("Petr", "Somoulov", "02-11-1995", 1, "Minsk");
        $person4 = new Person(1);
        $person5 = new Person(2);
        $person6 = new Person(3);
        $person1->deleteFromDatabase();
        $person2->formattingAgeAndGenderOfPerson();
        echo Person::dateOfBirthToAge("11.11.1998") . '<br>';
        echo Person::genderFromBinaryToText(1) . '<br>';
        echo Person::genderFromBinaryToText(0) . '<br>';
        $perArr1 = new PersonList(2, "!=");
        $perArr2 = new PersonList(1, ">");
        $perArr3 = new PersonList(3, "<");
        $perArr1->getPeopleById();
        $perArr2->getPeopleById();
        $perArr3->getPeopleById();
        $perArr1->deletePeopleFromDataBase();
    } catch (Throwable $t) {
        echo $t->getMessage();
    }

    ?>
</body>
</html>