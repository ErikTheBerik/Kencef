<?php 

	$team = [];
	
	$team[] = 
    [
        "Name" => "Cuong Vo Ta",
        "Age" => 23,
        "Job" => "Programmierer in Leipzig",
        "Course" => "",
        "Image" => "cuong.jpg"
    ];

    $team[] = 
    [
        "Name" => "Erik Ahlund Helguera",
        "Age" => 22,
        "Job" => "Programmierer in Leipzig",
        "Course" => "",
        "Image" => "erik.jpg"
    ];

    $team[] = 
    [
        "Name" => "Lukas Drechsler",
        "Age" => 23,
        "Job" => "Student in Leipzig",
        "Course" => "Lehramt",
        "Image" => "lukas.jpg"
    ];

    $team[] = 
    [
        "Name" => "Linda Leibhold",
        "Age" => 22,
        "Job" => "Studentin in Freiburg",
        "Course" => "Umweltwissenschaften",
        "Image" => "linda.jpg"
    ];

    $team[] = 
    [
        "Name" => "Anna-Lena Louis",
        "Age" => 23,
        "Job" => "Studentin in Leipzig",
        "Course" => "Sonderpädagogik",
        "Image" => "anna.jpg"
    ];

    $team[] = 
    [
        "Name" => "Gerda Arlt",
        "Age" => 23,
        "Job" => "Studentin in Leipzig",
        "Course" => "Lehramt",
        "Image" => "gerda.jpg"
    ];

    $team[] = 
    [
        "Name" => "Tanja Spindler",
        "Age" => 23,
        "Job" => "Auszubildende",
        "Course" => "Krankenschwester",
        "Image" => "tanja.jpg"
    ];

    $team[] = 
    [
        "Name" => "David Obiero Wachara",
        "Age" => 28,
        "Job" => "Lehrer in Kisumu",
        "Course" => "Kencef's Kontaktperson in Kenia",
        "Image" => "david.jpg"
    ];


 ?>

 <link rel="stylesheet" href="./Info/css/team.css?date=100619_3">
 <div class="col-12" style="padding: 0 0.05vw; text-align: center;">
 	KENCEF e.V. steht für "Kenyan Rural Needy Child Educational Foundation" <br>
    (auf Deutsch: Bildungsförderung für hilfsbedürftige Kinder im ländlichen Kenia)
 </div>
    <div class="container-fluid" style="margin-bottom: 5%;">
        <div class="row">

<?php
    shuffle($team);
    foreach ($team as $index => $team_member)
    {
        $extraClass = "";
        
        $extraClass = ($index - 4) % 7 == 0 ? $extraClass . " LeftMarginBig" : $extraClass; 
        $extraClass = ($index - 6) % 7 == 0 ? $extraClass . " RightMarginBig" : $extraClass;

        $extraClass = ($index - 3) % 5 == 0 ? $extraClass . " LeftMarginSmall" : $extraClass; 
        $extraClass = ($index - 4) % 5 == 0 ? $extraClass . " RightMarginSmall" : $extraClass;

        $img = $team_member['Image'];

?>
        <div class="col-lg-3 col-4 <?php echo $extraClass; ?>" style="padding: 0;">
            <div class="imageWrapper" style="padding: 10%;">
                <div class="dropdown" style="border-radius: 50%;">
                    <img src="./Assets/Team/<?php echo $img; ?>?date=090619_2" class="img-fluid" style="width: 100%; height: 100%; border-radius: 50%; border: 4px solid #e28824;" >
                    <div class="dropdown-content">
                        <?php echo strtoupper($team_member['Name']); ?>
                        <br>
                        <?php echo $team_member['Age'] . " Jahre" ?>
                        <br>
                        <br>
                        <?php echo $team_member['Job']; ?>
                        <br>
                        <?php if ($team_member['Course'] != '') 
                        {
                            echo "(" .  $team_member['Course'] . ")"; 
                        }?>
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

?>
    </div>
</div>

 <div class="col-12" style="padding: 0 0.05vw; text-align: justify;">
    Gegründet wurde der Verein 2017 von dem kenianischen Lehrer David Okeyo Wachara und der deutschen Studentin Linda Leibhold.
    David hatte bereits in seiner Jugend begonnen, sich für seine Gemeinde – vor allem die Witwen – einzusetzen und in diesem Rahmen
    u.a. eine öffentliche Bibliothek eingerichtet. Linda hat für drei Monate auf der Insel gelebt und gemeinsam mit David an der Realisierung
    verschiedener Projekte gearbeitet. Mit der Idee für KENCEF im Gepäck reiste sie zurück nach Deutschland und wird seitdem
    von ihren VereinskollegInnen Anna-Lena Louis, Lukas Drechsler, Gerda Arlt, Cuong Vo Ta und Tanja Spindler unterstützt.
 </div>