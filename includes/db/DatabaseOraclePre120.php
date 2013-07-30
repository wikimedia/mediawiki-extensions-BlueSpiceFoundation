<?php
class DatabaseOracle extends DatabaseOracleBase{
    
    function begin( $fname = 'DatabaseOracle::begin' ) {
        $this->mTrxLevel = 1;
    }

    function commit( $fname = 'DatabaseOracle::commit' ) {
        if ( $this->mTrxLevel ) {
            oci_commit( $this->mConn );
            $this->mTrxLevel = 0;
        }
    }

    function rollback( $fname = 'DatabaseOracle::rollback' ) {
        if ( $this->mTrxLevel ) {
            oci_rollback( $this->mConn );
            $this->mTrxLevel = 0;
        }
    }
}