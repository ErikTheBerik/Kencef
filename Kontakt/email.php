<link rel="stylesheet" href="./Kontakt/css/email.css">

<div class="container">
 <!-- <form action="action_page.php" method="POST"> -->
  <form action="https://formspree.io/mpzwewoq" method="POST">

    <label for="fname">Vorname</label>
    <input type="text" id="fname" name="firstname" placeholder="Maxi">

    <label for="lname">Nachname</label>
    <input type="text" id="lname" name="lastname" placeholder="Musterfrau">

        <label for="lname">E-Mail</label>
    <input type="text" id="email" name="email" placeholder="maxi.musterfrau@email.de">

    <label for="message">Nachricht</label>
    <textarea id="subject" name="subject" placeholder="Hier kannst du uns eine Nachricht schreiben :)" style="height:200px"></textarea>

    <input type="submit" value="Abschicken">

  </form>
</div>