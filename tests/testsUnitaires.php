<?php
/**
 * Tests unitaires
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;

require '../includes/fct.inc.php';

echo utf8_encode('Tests des fonctions présentes dans includes/ftc.inc.php <br>');

$lesTests = array() ;
$nbTestOk = 0;
$nbTestErreur = 0;

function testeLaFonction($nomFonction, $true){
    global $nbTestOk, $nbTestErreur, $lesTests;
    if ($true){
        $resultat = 'ok';
        $nbTestOk++;
    } else {
        $resultat = '<b><font color ="red">ERREUR</font></b>';
        $nbTestErreur++;        
    }
    $lesTests[] = array(
        'fonction' => $nomFonction,
        'resultat' => $resultat
        );
}

testeLaFonction('dateFrancaisVersAnglais', 
    dateFrancaisVersAnglais('27/02/2019') == '2019-02-27');

testeLaFonction('dateAnglaisVersFrancais',
    dateAnglaisVersFrancais('2019-02-27') == '27/02/2019');

testeLaFonction('getMois',
    getMois('27/02/2019') == '201902');

testeLaFonction('getMoisSuivant',
    getMoisSuivant('201902') == '201903' &&
    getMoisSuivant('201912') == '202001' &&
    getMoisSuivant('201909') == '201910');

testeLaFonction('getMoisPrecedent', 
    getMoisPrecedent('201902') == '201901' &&
    getMoisPrecedent('201901') == ('201812') &&
    getMoisPrecedent('201910') == '201909');

testeLaFonction('estEntierPositif', 
    estEntierPositif(5) && estEntierPositif(0)
    && !estEntierPositif(-3) && !estEntierPositif(2.5));

testeLaFonction('estTableauEntiers', 
    estTableauEntiers(array(1,12,123)) &&
    estTableauEntiers(array(0,2)) &&
    !estTableauEntiers(array(1,-3)) &&
    !estTableauEntiers(array(4,2.5)));

testeLaFonction('estDateDepassee', 
    estDateDepassee('27/02/2018') && !estDateDepassee('28/02/2019'));

testeLaFonction('estDateValide', 
    !estDateValide(null) && estDateValide('28/02/2019')
    && !estDateValide('2019-02-28'));

testeLaFonction('lesQteFraisValides', 
    lesQteFraisValides(array(1,12,123)) &&
    lesQteFraisValides(array(0,2)) && 
    !lesQteFraisValides(array(1,-3)) &&
    !lesQteFraisValides(array(4,2.5)));

testeLaFonction('filtrerChainePourBD', 
    filtrerChainePourBD("l'amour") == "l\'amour");

testeLaFonction('getDateFormatTexte', 
    getDateFormatTexte("28/01/2019") == "28 Janvier 2019");

// fonction getLesMois : retourne tous les mois jusqu'a 1 an en arrière
$lesMoisAttendus = '201902,201901,201812,201811,201810,201809,'
    . '201808,201807,201806,201805,201804,201803,';
$lesMoisComplets = getLesMois();
$lesMoisRecuperes = '';
foreach ($lesMoisComplets as $unMois){
    //xdebug_break();
    $lesMoisRecuperes .= $unMois['mois'] . ',';
}
testeLaFonction('getLesMois', 
    $lesMoisRecuperes == $lesMoisAttendus);

testeLaFonction('getMoisFormatTexte', 
       getMoisFormatTexte('201901')    == 'Janvier 2019'
    && getMoisFormatTexte('201902') == 'Février 2019'
    && getMoisFormatTexte('201903') == 'Mars 2019'
    && getMoisFormatTexte('201904') == 'Avril 2019'
    && getMoisFormatTexte('201905') == 'Mai 2019'
    && getMoisFormatTexte('201906') == 'Juin 2019'
    && getMoisFormatTexte('201907') == 'Juillet 2019'
    && getMoisFormatTexte('201908') == 'Août 2019'
    && getMoisFormatTexte('201909') == 'Septembre 2019'
    && getMoisFormatTexte('201910') == 'Octobre 2019'
    && getMoisFormatTexte('201911') == 'Novembre 2019'
    && getMoisFormatTexte('201912') == 'Décembre 2019'
    );


foreach ($lesTests as $unTest){
    echo 'Test fonction ' . $unTest['fonction'] . ' : ' . 
    $unTest['resultat'] . '<br>';
}
echo utf8_encode('<br>Nombre de tests réussis : ' . $nbTestOk . '<br>'
    . 'Nombre de tests échoués : ' . $nbTestErreur);


