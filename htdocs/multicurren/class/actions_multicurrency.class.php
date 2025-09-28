<?php
class ActionsYourModuleName 
{ 
  
  /** Overloading the doActions function : replacing the parent's function with the one below 
   *  @param      parameters  meta datas of the hook (context, etc...) 
   *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
   *  @param      action             current action (if set). Generally create or edit or null 
   *  @return       void 
   */ 
  function doActions($parameters, &$object, &$action, $hookmanager) 
  { 
    print_r($parameters); 
    echo "action: ".$action; 
    print_r($object); 
    
    if (in_array('somecontext',explode(':',$parameters['context']))) 
      { 
	// do something only for the context 'somecontext'
      }
    
    $this->results=array('myreturn'=>$myvalue)
      $this->resprints='A text to show';
    
    return 0;
  }
}
?>