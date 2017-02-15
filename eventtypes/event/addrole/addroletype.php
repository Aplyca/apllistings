<?php
//
// Definition of AddType class
//


class AddRoleType extends eZWorkflowEventType
{
	const WORKFLOW_TYPE_STRING = "addrole";

	function AddRoleType()
	{
		$this -> eZWorkflowEventType(AddRoleType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'kernel/workflow/event', 'Add Role' ) );
		$this -> setTriggerTypes(array('content' => array('publish' => array('after'))));
	}
	
	function execute( $process, $event )
	{
		$parameters = $process->attribute( 'parameter_list');	
		$objectID = $parameters[object_id];	
		$object = eZContentObject::fetch( $objectID );
						
		if ($object instanceof eZContentObject and self::validateEvent($object))
		{
			$ini_workflow = eZINI::instance('workflow.ini');				
			$role_id = $ini_workflow -> variable('Event-addrole', 'RoleID');
			$assign_type = $ini_workflow -> variable('Event-addrole', 'AssignType');			
			$user_node_id = $object -> mainNodeID();  

		    $role = eZRole::fetch( $role_id );
		    if ($role instanceof eZRole)
		    {	
		        $db = eZDB::instance();
		    	$db->begin();
		        $role->assignToUser( $objectID, $assign_type, $user_node_id );
		        $db->commit();
		    }
		    else 
		    {
		        return eZWorkflowType::STATUS_REJECTED;
		    }             
		}
				
		return eZWorkflowType::STATUS_ACCEPTED;				
	}
	
	function validateEvent($object)
	{
		$ini = eZINI::instance();		
		$user_class_id = $ini -> variable('UserSettings', 'UserClassID');
		$parent_user_node_id = $ini -> variable('UserSettings', 'DefaultUserPlacement');
		$content_class = $object -> contentClass(); 
		
		if( ($content_class -> ID == $user_class_id) and ($object -> mainParentNodeID() == $parent_user_node_id))
		{
			return true;
		}
		return false;
	}
}

eZWorkflowEventType::registerEventType( AddRoleType::WORKFLOW_TYPE_STRING, 'AddRoleType' );

?>
