<?php 

class post_gl_journal extends baseclass{
	function post_gl_recs($args=array()){
      	$gl_ids = $args[0];
       	try{
      	$gl_journal = $this->getTable('gl_journal_line');
      	$gl_journal->setFilter('id','IN',$gl_ids);
      	$gl_journal_records = $gl_journal->fetchAll();
      
		$transactions = $this->getTable('transactionjournal');
      
      	$this->beginTransaction();
      
      	
        	$transactions->init();
          	$transaction_id = $transactions->insert(false);
          	
          	$gl_entries=$this->getTable('gl_entries');
          
          	foreach($gl_journal_records as $rec){
            	$gl_entries->init();
              	$gl_entries->set_Posting_date($rec['posting_date']);
              	$gl_entries->set_Document_no($rec['document_no']);
              	$gl_entries->set_Gl_account($rec['account_no']);
            	$gl_entries->set_Description($rec['account_description']);
              	$gl_entries->set_Transaction_no($transaction_id);
              	$gl_entries->set_Amount($rec['amount']);
              	$debit_amount=($gl_entries->get_Amount()>=0)?abs($gl_entries->get_Amount()):null;
              	$credit_amount=($gl_entries->get_Amount()<0)?abs($gl_entries->get_Amount()):null;
              	$gl_entries->set_Debit_amount($debit_amount);
              	$gl_entries->set_Credit_amount($credit_amount);
              	$gl_entries->set_Bal_account_no($rec['bal_account_no']);
              	$gl_entries->insert(false);
              	
              	if((!is_null($rec['bal_account_no']))&&($rec['bal_account_type'])==1){
              	$gl_entries->init();
              	$gl_entries->set_Posting_date($rec['posting_date']);
              	$gl_entries->set_Document_no($rec['document_no']);
              	$gl_entries->set_Gl_account($rec['bal_account_no']);
            	$gl_entries->set_Description($rec['bal_account_description']);
              	$gl_entries->set_Transaction_no($transaction_id);
              	$gl_entries->set_Amount(-$rec['amount']);
              	$debit_amount=($gl_entries->get_Amount()>=0)?abs($gl_entries->get_Amount()):null;
              	$credit_amount=($gl_entries->get_Amount()<0)?abs($gl_entries->get_Amount()):null;
              	$gl_entries->set_Debit_amount($debit_amount);
              	$gl_entries->set_Credit_amount($credit_amount);
              	$gl_entries->set_Bal_account_no($rec['account_no']);
              	$gl_entries->insert(false);
                }
              
              	$gl_journal->deleteById($rec['id'],false);
              
            }
          	
          	$this->commitTransaction();
          	echo '{"Success":"Document posted"}';
        }
      	catch(Exception $e){
        	echo '{"Error":"'.$e->getMessage().'"}';
          	$this->rollbackTransaction();
        }
      
      	
      	
    }
}
?>
