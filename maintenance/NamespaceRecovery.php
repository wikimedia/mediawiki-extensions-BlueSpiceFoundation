<?php
/**
 * Maintenance script to recover pages and revisions deleted by namespacemanager
 *
 * @file
 * @ingroup Maintenance
 * @author Patric Wirth
 * @license GNU General Public Licence 3.0
 */

//TODO: check if namespace (newns) exists
//TODO: write log
//TODO: add some logic
$options = array( 'help', 'execute', 'oldns', 'newns' );
require_once( 'BSMaintenance.php' );
print_r( $options );

$bDry = true;
if( isset( $options['execute'] ) ) { 
	$bDry = false;
}

if( isset( $options['help'] ) ) {
	showHelp();
}
else {
	if( isset( $options['oldns'] ) && $options['oldns'] > 100 ) {
		NSRecoveryController( $bDry, $options );
	}
	else {
		showHelp();
	}
}

function showHelp() {
	echo( "Recover Pages/Revisions/Texts form backuptables\n" );
	echo( "Usage: php ModifyUserProperties.php [<option>=<>]\n" );
	echo( " --help : displays description\n\n" );
	echo( " --oldns : get revs and pages with this ns from bakuptable\n" );
	echo( " --newns : rename ns (if changed)\n" );
	echo( " --execute : Actually executes the script\n" );
	echo( "\n" );
}

function NSRecoveryController( $bDry, $options ) {
       
	$aPages = getDataFromNSBackup( 'page', array( 'page_namespace' => $options['oldns'] ) );
	
        if( empty( $aPages ) ) { 
            die("backup for namespace ".$options['oldns']." not found");
        }
        $aRevisions = array();
        $aTexts     = array();
        for($i = 0; $i < count( $aPages ); $i++) {
            $aRevisions[$i] = getDataFromNSBackup( 'revision', array( 'rev_page' => $aPages[$i]['page_id'] ) );
            
            for($ir = 0; $ir < count( $aRevisions[$i] ); $ir++) {
                $aTexts[$i][$ir] = getDataFromNSBackup( 'text', array( 'old_id' => $aRevisions[$i][$ir]['rev_text_id'] ) );
            }
        }
        //var_dump($aRevisions);
        setDataFromNSBackup($aPages, $aRevisions, $aTexts, $bDry, $options);
}

function getDataFromNSBackup( $sTable, $aConditions = array(), $aReturn = array() ) {
    $oDbr = wfGetDB( DB_REPLICA );

	$sTable = 'bs_namespacemanager_backup_'.$sTable;

    $rRes = $oDbr->select( $sTable, '*', $aConditions ) ;
    var_dump($oDbr->lastQuery());
    if( empty($rRes) ) return array();
    
    foreach( $rRes as $row ) {
        $aReturn[] = (array)$row;
    }
    
    return $aReturn;
}

function setDataFromNSBackup($aPages, $aRevisions, $aTexts, $bDry, $options) {
    $oDbr = wfGetDB( DB_MASTER );
    //die();
    for( $iP = 0; $iP < count( $aPages ); $iP++ ) {
        
        /*if( empty( $aRevisions[$iP] ) ) {
            echo "error: ".$aPages[$iP]['page_title']."  no revision found\n";
            continue;
        }
        $rRes = $oDbr->select('page', 'page_id' , array('page_id' => $aPages[$iP]['page_id']) );
        if( $rRes->fetchRow() ) {
            echo "error: ".$aPages[$iP]['page_title']."  already exists\n";
            continue;
        } 
        */
        for( $iR = 0; $iR < count( $aRevisions[$iP] ); $iR++ ) {
			echo 'Revision';
            /*if( empty( $aTexts[$iP][$iR] ) ) {
                echo "error: ".$aPages[$iP]['page_id']." -> ".$aRevisions[$iP][$iR]." - no text found !not recovered\n";
                continue;
            }
            
            $rRes = $oDbr->select('revision', 'rev_id' , array('rev_id' => $aRevisions[$iP][$iR]['rev_id']) );
            if( $rRes->fetchRow() ) {
                echo "error: ".$aPages[$iP]['page_title']."->".$aRevisions[$iP][$iR]['rev_id']." already exists\n";
                continue;
            }   
            
            $rRes = $oDbr->select('text', 'old_id' , array('old_id' => $aTexts[$iP][$iR][0]['old_id']) );
            if( $rRes->fetchRow() ) {
                echo "error: ".$aPages[$iP]['page_title']."->".$aRevisions[$iP][$iR]['rev_id']."->".$aTexts[$iP][$iR][0]['old_id']." already exists\n";
                continue;
            }
            */
            //var_dump($aTexts[$iP][$iR][0]);
            if( !$bDry && $options['execute'] ) {
                $oDbr->insert( 'text', $aTexts[$iP][$iR][0]);
                $oDbr->insert( 'revision', $aRevisions[$iP][$iR]);
				var_dump($aRevisions[$iP][$iR]);
            }
            
        }
        if( isset($options['newns']) ) {
            $aPages[$iP]['page_namespace'] = $options['newns'];
        }
        if( !$bDry && $options['execute'] ) {
            //$rRes = $oDbr->insert( 'page', $aPages[$iP]);
        }
        //if($rRes ){
            echo $aPages[$iP]['page_title']." recovered\n";
        //}
		//echo $aPages[$iP]['page_title']." recovered\n";
    }
}
