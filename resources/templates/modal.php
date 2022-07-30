<?php defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); ?>

<div class="modal" tabindex="-1" role="dialog" id="the-modal" aria-hidden="true" :class="modalClass">

	<div class="modal-dialog" role="document" v-bind:class="dialogClass">
		<div class="modal-content">
			
			<!-- modal header -->
			<div class="modal-header">
				<h5 class="modal-title">{{ title }}</h5>
			</div>
			
			<!-- modal body -->
			<div class="modal-body" v-html="content"></div>
			
			<!-- modal footer -->
			<div class="modal-footer">
			
				<button type="button" class="btn btn-primary" data-promise="accept" data-dismiss="modal" data-bs-dismiss="modal" v-if="confirm">{{ accept }}</button>
				
				<button type="button" class="btn btn-secondary" data-promise="reject" data-dismiss="modal" data-bs-dismiss="modal">{{ reject }}</button>
				
			</div>
			
		</div>
	</div>
	
</div>