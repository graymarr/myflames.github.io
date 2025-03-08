<?php

// Declaring variables and arrays
$letterArray = [];
$name1 = $_POST['name1'] ?? '';
$lname1 = $_POST['lname1'] ?? '';
$yourBirthdate = $_POST['yourBirthdate'] ?? '';

$name2 = $_POST['name2'] ?? '';
$lname2 = $_POST['lname2'] ?? '';
$theirBirthdate = $_POST['theirBirthdate'] ?? '';

if (empty($name1) || empty($lname1) || empty($yourBirthdate) || empty($name2) || empty($lname2) || empty($theirBirthdate)) {
    die("Error: Please fill in all fields.");
}

$yourFullname = $name1 . $lname1;
$theirFullname = $name2 . $lname2;

// FLAMES score mappings
$scoreMap = [1 => "F", 2 => "L", 3 => "A", 4 => "M", 5 => "E", 0 => "S"];
$definitionMap = [
    "F" => "FRIENDS",
    "L" => "LOVERS",
    "A" => "ANGER",
    "M" => "MARRIED",
    "E" => "ENGAGED",
    "S" => "SOULMATES",
];

// Functions
function removeSpace($name) {
    return str_replace(" ", "", $name);
}

function isHere(array $CurrentArray, array $ComparedArray) {
    return count(array_intersect($CurrentArray, $ComparedArray));
}

function getLetters(array $CurrentArray, array $ComparedArray) {
    return array_unique(array_intersect($CurrentArray, $ComparedArray));
}

// Process names into characters
$nameChars1 = str_split(removeSpace($yourFullname));
$nameChars2 = str_split(removeSpace($theirFullname));

$totalScore = (isHere($nameChars1, $nameChars2) + isHere($nameChars2, $nameChars1)) % 6;

// Zodiac Class
class Zodiac {
    private $sign;
    private $symbol;
    private $startDate;
    private $endDate;

    public function __construct($birthdate) {
        $this->loadZodiacFromFile($birthdate);
    }

    private function loadZodiacFromFile($birthdate) {
        $file = fopen("Zodiac.txt", "r");
        if (!$file) {
            die("Error: Could not open Zodiac.txt");
        }

        $date = strtotime($birthdate);
        while (($line = fgets($file)) !== false) {
            $lineParts = explode(",", trim($line));
            if (count($lineParts) < 4) continue; // Skip malformed lines

            list($sign, $symbol, $start, $end) = $lineParts;
            $startTimestamp = strtotime($start);
            $endTimestamp = strtotime($end);

            if (($date >= $startTimestamp && $date <= $endTimestamp) ||
                (date('F', $date) == date('F', $startTimestamp) && date('d', $date) >= date('d', $startTimestamp)) ||
                (date('F', $date) == date('F', $endTimestamp) && date('d', $date) <= date('d', $endTimestamp))) {
                
                $this->sign = $sign;
                $this->symbol = $symbol;
                $this->startDate = $start;
                $this->endDate = $end;
                break;
            }
        }
        fclose($file);
    }

    public function getSign() {
        return $this->sign;
    }

    public function getSymbol() {
        return $this->symbol;
    }

    public static function ComputeZodiacCompatibility($sign1, $sign2) {
        $compatibilityChart = [
            "Aries" => ["Leo", "Sagittarius", "Gemini", "Aquarius"],
            "Taurus" => ["Virgo", "Capricorn", "Cancer", "Pisces"],
            "Gemini" => ["Libra", "Aquarius", "Aries", "Leo"],
            "Cancer" => ["Scorpio", "Pisces", "Taurus", "Virgo"],
            "Leo" => ["Aries", "Sagittarius", "Gemini", "Libra"],
            "Virgo" => ["Taurus", "Capricorn", "Cancer", "Scorpio"],
            "Libra" => ["Gemini", "Aquarius", "Leo", "Sagittarius"],
            "Scorpio" => ["Cancer", "Pisces", "Virgo", "Capricorn"],
            "Sagittarius" => ["Aries", "Leo", "Libra", "Aquarius"],
            "Capricorn" => ["Taurus", "Virgo", "Scorpio", "Pisces"],
            "Aquarius" => ["Gemini", "Libra", "Aries", "Sagittarius"],
            "Pisces" => ["Cancer", "Scorpio", "Taurus", "Capricorn"]
        ];

        return in_array($sign2, $compatibilityChart[$sign1] ?? []) ? "Compatible" : "Not Compatible";
    }
}

// Person Class
class Person {
    private $firstName;
    private $lastName;
    private $birthday;
    private $zodiac;

    public function __construct($firstName, $lastName, $birthday) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthday = $birthday;
        $this->zodiac = new Zodiac($birthday);
    }

    public function getFullName() {
        return "{$this->lastName}, {$this->firstName}";
    }

    public function getZodiacSign() {
        return $this->zodiac->getSign();
    }

    public function getZodiacSymbol() {
        return $this->zodiac->getSymbol();
    }
}

$person1 = new Person($name1, $lname1, $yourBirthdate);
$person2 = new Person($name2, $lname2, $theirBirthdate);
$zodiacCompatibility = Zodiac::ComputeZodiacCompatibility($person1->getZodiacSign(), $person2->getZodiacSign());

?>

<!DOCTYPE html>
<html>
<head>
    <title>Results</title>
    <link rel="stylesheet" type="text/css" href="zodiac.css">
    <link rel="icon" type="image/x-icon" href="assets/img/sparkly_heart.gif">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+15&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+AU+VIC+Guides&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</head>
    <body>
    <div class="flames-content">
        <div class="flames-title">
        <p style="font-family: Roboto Mono; color: rgb(72, 65, 65);">Results ~</p>
        </div>
        
        <div class="flames-heading" style="font-family: Roboto Mono; color: rgb(72, 65, 65);"><?php echo "<i>$name1</i>"; ?> &nbsp; and &nbsp; <?php echo "<i>$name2</i>"; ?></div>

        <div class="flames-inside">
            <img src="assets/img/pink_moon_gnight.gif" alt="cute moon and stars gif" class="form-image">
            
            <p style="font-family: Roboto Mono; color: rgb(72, 65, 65);">
            <?php
                echo "<br> Your common letters are: " . implode(", ", getLetters($nameChars1, $nameChars2));
                echo "<br> $name1 has " . isHere($nameChars1, $nameChars2) . " common letters, ";
                echo "$name2 has " . isHere($nameChars2, $nameChars1) . " common letters.";
                echo "<br> Total: $totalScore → " . $scoreMap[$totalScore] . " - " . $definitionMap[$scoreMap[$totalScore]];

                echo "<br><br> <strong>Your full name:</strong> " . $person1->getFullName();
                echo "<br> <strong>Their full name:</strong> " . $person2->getFullName();
                echo "<br> <strong>Zodiac Compatibility:</strong> $zodiacCompatibility";
            ?>
             <br/><br/>
             <button class="styled-button" onclick="goBack()">Try Again</button>
            </p>
           
        </div>
        
    </div>
    
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </body>
</html>
