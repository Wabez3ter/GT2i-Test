<?php
require("MagicParser.php");

function addToDatabase($record){
    //Test
    //var_dump($record);
    $PRODUIT_POCLEUNIK = $record["PRODUIT_POCLEUNIK"];
    $PRODUIT_REF = $record["PRODUIT_REF"];
    $REFCIALE_ARCLEUNIK = $record["REFCIALE_ARCLEUNIK"];
    $REFCIALE_REFART = $record["REFCIALE_REFART"];
    $REFCIALE_REFCAT = $record["REFCIALE_REFCAT"];
    $POTRAD_DESI = $record["POTRAD_DESI"];
    $REFCIALE_CTVA = $record["REFCIALE_CTVA"];
    $FICTECH_MEMOCAT = $record["FICTECH_MEMOCAT"];
    $FICTECH_MEMONET = $record["FICTECH_MEMONET"];
    $PRODUIT_MARQUE = $record["PRODUIT_MARQUE"];
    $PRODUIT_CLEP01 = $record["PRODUIT_CLEP01"];
    $PRODUIT_CLEP02 = $record["PRODUIT_CLEP02"];
    $PRODUIT_CLEP03 = $record["PRODUIT_CLEP03"];
    $PRODUIT_CLEP04 = $record["PRODUIT_CLEP04"];
    $PRODUIT_CLEP06 = $record["PRODUIT_CLEP06"];
    $PRODUIT_CLEP07 = $record["PRODUIT_CLEP07"];
    $PRODUIT_GCOLORIS = $record["PRODUIT_GCOLORIS"];
    $PRODUIT_GTAILLE = $record["PRODUIT_GTAILLE"];
    $PRODUIT_CLEP12 = $record["PRODUIT_CLEP12"];
    $REFCIALE_FICHEINA = $record["REFCIALE_FICHEINA"];
    $REFCIALE_MODTE = $record["REFCIALE_MODTE"];
    $PRODUIT_MODTE = $record["PRODUIT_MODTE"];
    $ARTICLE_POIDS = $record["ARTICLE_POIDS"];
    $ARTICLE_HNORMEL = $record["ARTICLE_HNORMEL"];
    $ARTICLE_CATEG = $record["ARTICLE_CATEG"];



    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
    $con = mysqli_connect("localhost", "root", "", "gt2i");
    if ($con->connect_errno) {
        die("Connexion Error " . $con->connect_error);
    }

    if ($stmt = $con->prepare("INSERT INTO catalogue(PRODUIT_POCLEUNIK, PRODUIT_REF, REFCIALE_ARCLEUNIK, REFCIALE_REFART, REFCIALE_REFCAT, POTRAD_DESI, REFCIALE_CTVA, FICTECH_MEMOCAT, FICTECH_MEMONET, PRODUIT_MARQUE, PRODUIT_CLEP01, PRODUIT_CLEP02, PRODUIT_CLEP03, PRODUIT_CLEP04, PRODUIT_CLEP06, PRODUIT_CLEP07, PRODUIT_GCOLORIS, PRODUIT_GTAILLE, PRODUIT_CLEP12, REFCIALE_FICHEINA, REFCIALE_MODTE, PRODUIT_MODTE, ARTICLE_POIDS, ARTICLE_HNORMEL, ARTICLE_CATEG) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")) {
        $stmt->bind_param("isisssisisssssssiisiiidii", $PRODUIT_POCLEUNIK, $PRODUIT_REF,$REFCIALE_ARCLEUNIK,$REFCIALE_REFART,$REFCIALE_REFCAT,$POTRAD_DESI,$REFCIALE_CTVA,$FICTECH_MEMOCAT,$FICTECH_MEMONET,$PRODUIT_MARQUE, $PRODUIT_CLEP01,$PRODUIT_CLEP02,$PRODUIT_CLEP03,$PRODUIT_CLEP04,$PRODUIT_CLEP06,$PRODUIT_CLEP07,$PRODUIT_GCOLORIS,$PRODUIT_GTAILLE,$PRODUIT_CLEP12,$REFCIALE_FICHEINA,$REFCIALE_MODTE,$PRODUIT_MODTE,$ARTICLE_POIDS,$ARTICLE_HNORMEL,$ARTICLE_CATEG);

        $stmt->execute();
        $stmt->close();
    }

    $con->close();
}


MagicParser_parse("catalogue.XML","addToDatabase","xml|HF_DOCUMENT/FLIGNE/");
?>