<?php
class DatabaseOracle extends DatabaseOracleBase{
    
    function doBegin( $fname = 'DatabaseOracle::begin' ) {
        $this->mTrxLevel = 1;
    }

    function doCommit( $fname = 'DatabaseOracle::commit' ) {
        if ( $this->mTrxLevel ) {
            oci_commit( $this->mConn );
            $this->mTrxLevel = 0;
        }
    }

    function doRollback( $fname = 'DatabaseOracle::rollback' ) {
        if ( $this->mTrxLevel ) {
            oci_rollback( $this->mConn );
            $this->mTrxLevel = 0;
        }
    }
}