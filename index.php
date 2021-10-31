<?php
define("DB_HOST", "localhost");
define("DB_NAME", "hskpbcz_vez");
define("DB_USER", "hskpbcz_vez");
define("DB_PASS", "8kM1WB4L");

# Clean date
function cleanDate($date)
{
    $date = str_replace(' ', '', $date);
    return $date;
}

# Convert date format
function dateToIso($date)
{
    $date = cleanDate($date);
    $timestamp = strtotime($date);
    $date = date("Y-m-d", $timestamp);
    return $date;
}

function dateFromIso($date)
{
    $date = cleanDate($date);
    $date = preg_replace('~^([0-9]+)-0?([0-9]+)-0?([0-9]+)~', '\\3.&nbsp;\\2.&nbsp;\\1', $date);
    return $date;
}

function register()
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, "utf8");

    if (!$conn) {
        die("<div class='mt-3 text-white shadow card'><div class='card-header bg-info'>Chyba!</div><div class='card-body text-body'><p class='card-text'>Připojení k databázi selhalo: " . mysqli_connect_error() . "</p></div></div>");
    }

    $jmeno = mysqli_real_escape_string($conn, $_POST['jmeno']);
    $prijmeni = mysqli_real_escape_string($conn, $_POST['prijmeni']);
    $datum = dateToIso(mysqli_real_escape_string($conn, $_POST['datum']));
    $druzstvo = mysqli_real_escape_string($conn, $_POST['druzstvo']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "INSERT INTO registrace (id, jmeno, prijmeni, datum, druzstvo, email)
          	VALUES (NULL, '$jmeno', '$prijmeni', '$datum', '$druzstvo', '$email')";

    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success' role='alert'>
            Registrace proběhla úspěšně.
          </div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>
            <h4>Chyba!</h4>
            " . $sql . "<br>" . mysqli_error($conn) . "
          </div>";
    }

}

function listRegistered()
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, "utf8");

    if (!$conn) {
        die("<div class='mt-3 text-white shadow card'><div class='card-header bg-info'>Chyba!</div><div class='card-body text-body'><p class='card-text'>Připojení k databázi selhalo: " . mysqli_connect_error() . "</p></div></div>");
    }

    $sql = "SELECT jmeno, prijmeni, startc, postupuje, cas1, cas2, cas3, cas4, cas5
            FROM registrace
            ORDER BY pohlavi DESC, postupuje DESC, cas5 ASC, cas4 ASC, cas3 ASC, cas2 ASC, cas1 ASC, startc ASC, prijmeni ASC";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                  <td>" . htmlspecialchars($row['startc'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['prijmeni'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['jmeno'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['cas1'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['cas2'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['cas3'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['cas4'], ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($row['cas5'], ENT_QUOTES) . "</td>
                  </tr>";
        }
    }
    mysqli_close($conn);
}

function sendMessage()
{
    $zprava = htmlspecialchars($_POST["zprava"], ENT_QUOTES);
    $email = $_POST["email"];
    $jmeno = htmlspecialchars($_POST["jmeno"], ENT_QUOTES);

    require 'libs/phpmailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;

    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    #$mail->SMTPDebug = 3;                                  // Enable verbose debug output

    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'anguille.pocitacepribram.cz'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'info@hskpb.cz'; // SMTP username
    $mail->Password = 'YhrZXOsZ'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to

    $mail->setFrom($email, $jmeno);
    $mail->addAddress('info@hskpb.cz', 'Info HSKPB'); // Add a recipient
    $mail->addReplyTo($email, $jmeno);

    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = 'Zpráva z webových stránek HSKPB.cz';
    $mail->Body = $zprava;

    if (!$mail->send()) {
        echo 'Zpráva nemohla být odeslána.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo '<h3>
					Zpráva byla v pořádku odeslána. Děkuji.
				</h3>';
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Hasičský sportovní klub Příbram</title>
    <meta name="description" content="Hasičský sportovní klub Příbram - hskpb.cz" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.css" />
    <link rel="stylesheet" href="assets/css/vanilla-zoom.min.css" />
</head>

<body>
    <nav class="bg-white navbar navbar-light navbar-expand-lg fixed-top clean-navbar">
        <div class="container">
            <a class="navbar-brand logo" href="#"><img src="assets/img/logo.webp" alt="Logo - HSKPB.cz"
                    class="img-fluid w-50"></a><button data-bs-toggle="collapse" class="navbar-toggler"
                data-bs-target="#navcol-1">
                <span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#uvod">Úvod</a></li>
                    <li class="nav-item"><a class="nav-link" href="#propozice">Propozice</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="#registrace">Registrace</a></li>-->
                    <li class="nav-item"><a class="nav-link" href="#prihlaseni">Závodníci</a></li>
                    <li class="nav-item"><a class="nav-link" href="#galerie">Galerie</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontakt">Kontakt</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page landing-page" id="uvod">
        <section class="clean-block clean-hero"
            style="background-image: url('assets/img/tech/image4.jpg'); color: rgba(1, 51, 82, 0.85)">
            <div class="text">
                <h2>Hasičský sportovní klub Příbram</h2>
                <p>
                    Hasičský záchranný sbor Středočeského kraje ve spolupráci se Sdružením hasičů Čech, Moravy a
                    Slezska a Hasičským sportovním klubem Příbram z.s. si Vás dovolují pozvat na 4. ročník HALOVÉ
                    SOUTĚŽE VE VÝSTUPU NA CVIČNOU VĚŽ
                </p>
                <a class="btn btn-outline-light btn-lg" type="button" href="#propozice">Více</a>
            </div>
        </section>
        <section class="clean-block clean-faq dark" id="propozice">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Propozice</h2>
                </div>
                <div class="block-content">
                    <div class="faq-item">
                        <h4 class="question">Termín</h4>
                        <div class="answer">
                            <p>Sobota 30. října 2021</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <h4 class="question">Místo konání</h4>
                        <div class="answer">
                            <p>HZS Středočeského kraje, ÚO Příbram</p>
                            <p>Stanice HZS Příbram</p>
                            <p>Školní 70, 261 01 Příbram</p>
                            <p>GPS: 49.674109 N, 13.999516 E</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <h4 class="question">Časový rozvrh</h4>
                        <div class="answer">
                            <p>7:30 hodin - Zahájení prezence závodníků</p>
                            <p>8:00 hodin - Trénink závodníků na cvičné věži</p>
                            <p>9:00 hodin - Ukončení prezence závodníků</p>
                            <p>9:15 hodin - Slavnostní nástup</p>
                            <p>9:30 hodin - Výstup do 2. podlaží cvičné věže – I. až III. kolo ženy</p>
                            <p>11:00 hodin - Finálové rozběhy žen</p>
                            <p>11:30 hodin - Výstup do 4. podlaží cvičné věže – I. až III. kolo muži</p>
                            <p>13:30 hodin - Finálové rozběhy mužů Po skončení</p>
                            <p><strong>soutěže bude provedeno slavnostní vyhodnocení a předání cen.</strong></p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <h4 class="question">Přihlášky</h4>
                        <div class="answer">
                            <p>
                                Přihlášky budou akceptovány pouze pomocí online přihlášení na webu
                                http://www.hskpb.cz/. Přihlašování bude zahájeno v pondělí 4. 10. 2021!
                            </p>
                            <p>
                                Počet závodníků je omezen na 40 mužů a 20 žen, záleží jen na rychlosti přihlašování
                                na online webové aplikaci; pořadatel si vyhrazuje právo udělit divokou kartu (1–2
                                ks).
                            </p>
                            <p>
                                Startovní pořadí bude seřazeno podle nejlepšího času v soutěžích Českého poháru ve
                                dvojboji a MČR v PS 2021-věž (v obráceném pořadí). Startovní listina bude zveřejněna
                                od středy 27. 10. 2021 na webu Hasičského sportovního klubu Příbram z.s..
                                http://www.hskpb.cz/
                            </p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <h4 class="question">Pravidla soutěže</h4>
                        <div class="answer">
                            <p>
                                Soutěže se mohou zúčastnit příslušníci a zaměstnanci HZS ČR, zaměstnanci HZS
                                podniků, členové SDH obcí, členové občanských sdružení a sportovních klubů. Soutěží
                                se podle platných pravidel PS, vyjma specifik Příbramské věže:
                            </p>
                            <ul>
                                <li>
                                    Obuv musí být čistá, bez plastových či kovových hrotů a nesmí zanechávat barevné
                                    šmouhy (tzv. non-marking úprava podrážky).
                                </li>
                                <li>Výstup na cvičnou věž je bez provedení zápichu!</li>
                                <li>Šíře oken na cvičné věži je 100 cm.</li>
                            </ul>
                            <p>
                                Finálová část proběhne v každé kategorii formou vyřazovacích závodů (systém pavouk),
                                kam postoupí 16 mužů a 8 žen na základě nejlepšího času ze všech pokusů.
                            </p>
                            <p>
                                <strong>Pořadatel si vyhrazuje právo zrušit III. kolo pokusů v případě časové
                                    tísně.</strong>
                            </p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <h4 class="question">Organizační pokyny</h4>
                        <div class="answer">
                            <p>
                                Soutěž proběhne v tělocvičně stanice HZS Příbram. Povrch tělocvičny je umělý –
                                linoleum (nutné přezutí). Občerstvení bude zajištěno organizátorem za úplatu. Za
                                zdravotní stav soutěžících zodpovídá přihlašovatel. Zdravotnické zabezpečení bude
                                poskytovat ZZS Středočeského kraje – oblastní středisko Příbram.
                            </p>
                            <p>
                                <strong>Startovné: 100 Kč/závodník; může být realizováno bankovním převodem (do popisu
                                    pro příjemce uvést jméno přihlášeného závodníka) na účet 887556586/2010
                                    Hasičského sportovního klubu Příbram z.s., případně na místě
                                    v hotovosti.</strong>
                            </p>
                            <p>
                                <strong>Zapůjčení hákového žebříku (2 ks) bude zdarma.</strong>
                            </p>
                            <p>
                                <strong>Akce proběhne v souladu s platnými epidemickými opatřeními nařízeními
                                    Ministerstvem zdravotnictví. Při vstupu se prosím prokažte systémem O-T-N a
                                    dodržujte platná protiepidemická opatření.</strong>
                            </p>
                            <p>
                                Podrobnější informace poskytne: <br />
                                mjr. Bc. Pavel Maňas <br />
                                E-mail:     pavel.manas@sck.izscr.cz <br />
                                Telefon: +420 950 831 160 <br />
                                Mobil:     +420 721 401 045
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--        <section class="clean-block clean-form dark" id="registrace">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Registrace</h2>
                </div>
                <?php
if ($_POST) {
    /*if ($_POST['registrace']) {
    register();
    }*/
    echo "Registrace byla uzavřena";
}
?>
                <form action="" method="post">
                    <div class="mb-3">
                        <label class="form-label" for="jmeno">Jméno</label><input class="form-control item" type="text"
                            name="jmeno" id="jmeno" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="prijmeni">Příjmení</label><input class="form-control item"
                            type="text" name="prijmeni" id="prijmeni" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="datum">Datum narození</label><input class="form-control item"
                            type="date" name="datum" id="datun" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="druzstvo">Družstvo</label><input class="form-control item"
                            type="text" name="druzstvo" id="druzstvo" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label><input class="form-control item" type="text"
                            name="email" id="email" required />
                    </div>
                    <input type="hidden" name="registrace" value="true">
                    <input type="submit" class="btn btn-primary" name="registrace" value="Přihlásit" />
                </form>
            </div>
        </section> -->
        <section class="clean-block clean-form dark" id="prihlaseni">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Přihlášení závodníci</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless">
                        <caption>Seznam přihlášených závodníků</caption>
                        <thead>
                            <tr>
                                <th>Start. č.</th>
                                <th>Příjmení</th>
                                <th>Jméno</th>
                                <th>Čas 1</th>
                                <th>Čas 2</th>
                                <th>Čas 3</th>
                                <th>Čas 4</th>
                                <th>Čas 5</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
listRegistered();
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="clean-block clean-gallery dark" id="galerie">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Galerie</h2>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/001.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/001.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/002.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/002.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/003.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/003.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/004.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/004.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/005.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/005.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/006.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/006.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/007.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/007.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/008.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/008.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/009.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/009.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/010.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/010.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/011.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/011.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/012.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/012.jpg"></a></div>
                    <div class="col-md-6 col-lg-4 item"><a class="lightbox" href="assets/img/2020/013.jpg"><img
                                class="img-thumbnail img-fluid image" src="assets/img/2020/013.jpg"></a></div>
                </div>
            </div>
        </section>
        <section class="clean-block clean-form dark" id="kontakt">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Kontakt</h2>
                </div>

                <form action="" method="post">
                    <div class="mb-3">
                        <?php
if ($_POST) {
    if ($_POST['message'] == "true") {
        sendMessage();
    }
}
?>
                        <label class="form-label" for="jmeno">Jméno</label><input class="form-control" type="text"
                            id="jmeno" name="jmeno" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label><input class="form-control" type="email"
                            id="email" name="email" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="zprava">Zpráva</label><textarea class="form-control" id="zprava"
                            name="zprava" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="message" value="true">
                        <input type="submit" class="btn btn-primary" name="odeslat" value="Odeslat" />
                    </div>
                </form>
            </div>
        </section>
    </main>
    <footer class="page-footer dark">
        <div class="container">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h5>Mapa webu</h5>
                    <ul>
                        <li><a href="#uvod">Úvod</a></li>
                        <li><a href="#propozice">Propozice</a></li>
                        <li><a href="#registrace">Registrace</a></li>
                        <li><a href="#galerie">Galerie</a></li>
                        <li><a href="#kontakt">Kontakt</a></li>
                    </ul>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>hskpb.cz 2021</p>
        </div>
    </footer>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.js"></script>
    <script src="assets/js/vanilla-zoom.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>
