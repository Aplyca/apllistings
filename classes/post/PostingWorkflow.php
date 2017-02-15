<?php
abstract class PostingWorkflow
{


	private $workflowStepViews;
	
	abstract function getNextStep($currentView);
	abstract function getPostInterfaceData($currentView, $objectID);

	public function __construct()
	{		
		$wfIni = eZINI::instance("apllistings.ini");
		$workflowStepViews = $wfIni -> variable('Settings', 'StepViews');		
	}
	

	public function checkRequiredAttributes($dataMap)
	{									
		$confirmCompleted = true;
		foreach($dataMap as $attribute)
		{		
			if ($attribute->contentClassAttributeIsRequired())
			{
				if(!$attribute->hasContent())
					$confirmCompleted = false;
			}
		}
		return $confirmCompleted;
	}
	
	
	static function registerObjectId($id)
	{
		$http = eZHTTPTool::instance();
		if($http->hasSessionVariable("posting_drafts"))
		{
			$postingDraftsID = $http->sessionVariable("posting_drafts");
			$postingDraftsID[] = $id;
			$http->setSessionVariable("posting_drafts", $postingDraftsID);
		}
		else
		{
			$postingDraftsID = array();
			$postingDraftsID[] = $id;
			$http->setSessionVariable("posting_drafts", $postingDraftsID);
		}
	}
		
	 
	static function resetWorkflow()
	{
		$http = eZHTTPTool::instance();
		$http->removeSessionVariable("posting_drafts");
	}

	static function finishWorkflow()
	{
		$http = eZHTTPTool::instance();
		$http->removeSessionVariable("posting_drafts");
	}


	static function cleanDraftIfExists()
	{
		$http = eZHTTPTool::instance();
		$postingDraftsID = $http->sessionVariable("posting_drafts");
		foreach($postingDraftsID as $objectID)
		{
			if($objectID != "")
			{
				AplContentHandler::removeDraftContentObject($objectID);
			}	
		}
	}
	
	



}

?>