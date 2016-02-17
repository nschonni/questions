<?php

class ElggAnswer extends ElggObject {
	
	const SUBTYPE = 'answer';
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::initializeAttributes()
	 */
	function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ElggEntity::getURL()
	 */
	public function getURL() {
		$container_entity = $this->getContainerEntity();
		
		$url = $container_entity->getURL() . "#elgg-object-{$this->getGUID()}";
		
		return $url;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::canComment()
	 */
	public function canComment($user_guid = 0) {
		
		return $this->getContainerEntity()->canComment($user_guid);
	}
	
	/**
	 * Get the metadata object for the correct answer
	 *
	 * @return false|ElggMetadata
	 */
	public function getCorrectAnswerMetadata() {
		$result = false;
		
		$options = [
			'metadata_name' => 'correct_answer',
			'guid' => $this->getGUID(),
		];
		
		$metadata = elgg_get_metadata($options);
		if ($metadata) {
			$result = $metadata[0];
		}
		
		return $result;
	}
	
	/**
	 * Mark an answer as the correct answer for this question
	 *
	 * @return void
	 */
	public function markAsCorrect() {
		// first set the mark
		$this->correct_answer = true;
		
		// depending of the plugin settings, we also need to close the question
		if (questions_close_on_marked_answer()) {
			$question = $this->getContainerEntity();
			
			$question->close();
		}
	}
	
	/**
	 * This answer is no longer the correct answer for this question
	 *
	 * @return void
	 */
	public function undoMarkAsCorrect() {
		unset($this->correct_answer);
		
		// don't forget to reopen the question
		$question = $this->getContainerEntity();
			
		$question->reopen();
	}
}
