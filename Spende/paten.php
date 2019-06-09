<?php 

	$kids = [];
	
	$kids[] = 
    [
        "Name" => "Alexander Robert",
        "Age" => 7,
        "School" => "Sena Primary School",
        "Class" => 1,
        "Subject" => "Christian Religious Education",
        "Career" => "Teacher",
        "Info" => 
        [
        	"Lives with his mother (not employed)", 
        	"They support themselves by collecting stones (Gravels, Bauschutt, Kies)",
        	"Grandmother is disabled so they have to support her aswell",
        	"Father is there but left school at grade 2, not working",
        	"Only child",
        	"Mutter wurde unfruchtbar nach erster Geburt"
        ],
        "Image" => "Alexander_Robert.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Elvis Luise",
        "Age" => 6,
        "School" => "Misori Primary School",
        "Class" => 1,
        "Subject" => "Maths",
        "Career" => "Er will einfach nur arbeiten",
        "Info" => 
        [
        	"Lives with his mother (smale-scale seller of small fish)", 
        	"5 children",
        	"No father (left the island)",
        	"Live with their grandmother (support her), during raining season she is growing Erbsen and collecting"
        ],
        "Image" => "Elvis_Luise.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Gay Brenda",
        "Age" => 9,
        "School" => "Sena Primary School",
        "Class" => 3,
        "Subject" => "Christian Religious Education",
        "Career" => "Nurse",
        "Info" => 
        [
        	"Father and mother are both unable to work", 
        	"Father (HIV) → chronic illnesses, he also lost his first wife",
        	"She lives with her step mother (HIV)",
        	"Father was a small-scale farmer but he can’t work that hard anymore",
        	"5 children"
        ],
        "Image" => "Gay_Brenda.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Liunicole Omondi",
        "Age" => 10,
        "School" => "Sena Primary School",
        "Class" => 4,
        "Subject" => "English",
        "Career" => "Engineer",
        "Info" => 
        [
        	"lives with his mother (left school after class 7)", 
        	"Father serious drunk head (violent, beats mother and children, never paid for school fees), left school after class 3",
        	"Children have to pull the net (fishing) → to support the family",
        	"Mother is caring but can’t be employed",
        	"7 children"
        ],
        "Image" => "Liunicole_Omondi.jpg"
    ];

    $kids[] = 
    [
        "Name" => "John Lewis",
        "Age" => 11,
        "School" => "Sena Primary School",
        "Class" => 4,
        "Subject" => "Science",
        "Career" => "Doctor",
        "Info" => 
        [
        	"Lives with his mother and father", 
        	"They are both unable to support the children",
        	"They are very old",
        	"Burning charcoal at the hill to support the family (but it’s actually illegal and not healthy)",
        	"3 children"
        ],
        "Image" => "John_Lewis.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Richard Mofesto",
        "Age" => 8,
        "School" => "Sena Primary School",
        "Class" => 2,
        "Subject" => "Maths",
        "Career" => "Engineer",
        "Info" => 
        [
        	"Brother of Liunicole"
        ],
        "Image" => "Richard_Mofesto.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Jimy Vera",
        "Age" => 11,
        "School" => "Sena Primary School",
        "Class" => 5,
        "Subject" => "Science",
        "Career" => "Engineer",
        "Info" => 
        [
        	"Lives with the mother (collecting stones)", 
        	"7 children + grandmother",
        	"Mother supports the whole family",
        	"Father left the island (5 years ago)",
        	"Very clever and obedient in school"
        ],
        "Image" => "Jimy_Vera.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Clinton Ogweno",
        "Age" => 7,
        "School" => "Sena Primary School",
        "Class" => 2,
        "Subject" => "Maths",
        "Career" => "Engineer",
        "Info" => 
        [
        	"Father is very old and sick", 
        	"Mother is burning charcoal to support the family",
        	"14 children",
        	"LRS (slow learner)",
        	"None of the kids went to secondary school",
        	"Older siblings dropped at early grades of primary school (keener hat einen Schulabschluss)",
        	"Very poor family, bad house"
        ],
        "Image" => "Clinton_Ogweno.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Nickolas Odhiambo",
        "Age" => 6,
        "School" => "Nursery School",
        "Class" => "Vorschulklasse",
        "Subject" => "Writing",
        "Career" => "Author",
        "Info" => 
        [
        	"LRS (slow learner)", 
        	"Brother of Clinton Ogweno (Der darueber)"
        ],
        "Image" => "Nickolas_Odhiambo.jpg"
    ];

    $kids[] = 
    [
        "Name" => "Ann Ocholla",
        "Age" => 12,
        "School" => "Misori Primary School",
        "Class" => 6,
        "Subject" => "Science",
        "Career" => "Engineer",
        "Info" => 
        [
        	"Lives in Sena with her mother (smale-scale seller of small fish)", 
        	"Mother has HIV",
        	"Father passed away",
        	"4 children"
        ],
        "Image" => ""
    ];

    $kids[] = 
    [
        "Name" => "Safin Lwero",
        "Age" => 4,
        "School" => "Mauta Primary",
        "Class" => "Vorschulklasse",
        "Subject" => "?",
        "Career" => "?",
        "Info" => 
        [
        	"Lives with grandmother (has HIV)", 
        	"Grandmother has no job (helps other families to earn some little money)",
        	"Parents passed away",
        	"Only 1 child"
        ],
        "Image" => ""
    ];

 ?>
 <div class="col-12" style="padding: 0 0.05vw; text-align: center;">
 	Für eines dieser Kinder kann eine Patenschaft übernommen werden. Mithilfe von 60€ im Jahr kann ein komplettes
Schuljahr finanziert werden.
 </div>
	<div class="container-fluid">
		<div class="row">

<?php

    foreach ($kids as $index => $kid)
    {
    	$img = $kid['Image'] == "" ? "Kid_Template.png" : $kid['Image'];
    	?>
    	<div class="col-md-2 col-sm-6 ">
    		<img src="./Assets/Kids/<?php echo $img; ?>" style="width: 100%; image-orientation: from-image;">
    	</div>
    	<?php
    }

?>
		</div>
	</div>