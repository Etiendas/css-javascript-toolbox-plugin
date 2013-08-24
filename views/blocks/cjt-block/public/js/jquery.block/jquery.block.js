/**
* 
*/

/**
* 
*/
(function($) {
	
	/**
	* Override CJTBlockPlugin class.
	* 
	* @param node
	* @param args
	*/
	CJTBlockPlugin = function(node, args) {			
		
		/**
		* 
		*/
		this.pagesPanel = null;
		
		/**
		*
		*
		*
		*
		*/		
		this._ontogglepagespanel = function(event, initialState) {
			var toggler = $(event.target);
			var block = this.block;
			var tabs = block.box.find('.cjt-pages-tab');
			var pagesBlock = block.box.find('.cjpageblock');
			var codeBlock = block.box.find('.cjcodeblock');
			var aceEditor = block.aceEditor;
			var newState = '';
			// Hide pages panel when:
			if (tabs.css('display') != 'none') {
				// Hide if initial value == undefined or initial value == closed.
				if ((initialState != '') && (initialState != 'undefined')) {
					// Hide elements.
					tabs.hide();
					pagesBlock.css('width', '0px');
					codeBlock.animate({'margin-right' : 0}, undefined, undefined, 
						function() {
							toggler.addClass('closed');
							// Refresh editor.
							aceEditor.resize();
						}
					);
					// Save state.
					newState = 'closed';
				}
			}
			else {
				// Show elements.
				codeBlock.animate({'margin-right' : 320}, undefined, undefined,
					function() {
						// Use CSS class margin not inline style!
						codeBlock.css('margin-right', '');
						// Show panel!
						pagesBlock.css('width', '');
						tabs.show();
						toggler.removeClass('closed');
						// Refresh editor.
						aceEditor.resize();
					}
				);
			}
			// Set title based on the new STATE!
			toggler.attr('title', CJT_CJT_BLOCKJqueryBlockI18N['assigmentPanel_' + newState + 'Title']);
			// Save state.
			block.set('pagesPanelToggleState', newState);
			// For link to behave inactive.
			return false;
		}
		// Initialize parent class.
		this.initCJTPluginBase(node, args);
		// Plug the assigment panel, get the jQuery ELement for it
		var assigmentPanelElement = this.block.box.find('#tabs-' + this.block.get('id'));
		this.pagesPanel = assigmentPanelElement.CJTBlockAssignmentPanel({block : this});
		// Add toolbox button.
		var tbIconsGroup = this.block.box.find('.editor-toolbox .icons-group')
		tbIconsGroup.children().first().after('<a href="#" class="cjt-tb-link cjttbl-toggle-objects-panel"></a>')
		var toggler = this.editorToolbox.add('toggle-objects-panel', {callback : this._ontogglepagespanel});
		// Close it if it were closed.
		this._ontogglepagespanel({target : toggler.jButton}, this.block.get('pagesPanelToggleState', ''));
		
		// More to Dock with Fullscreen mode!
		this.extraDocks = [
			{element : assigmentPanelElement.find('.ui-tabs-panel'), pixels : 89},
			{element : assigmentPanelElement.find('.ui-tabs-panel .pagelist'), pixels : 132},
			{element : assigmentPanelElement.find('.custom-post-list.ui-accordion-content'), pixels : 122},
			{element : assigmentPanelElement.find('.custom-post-list.ui-accordion-content .pagelist'), pixels : 160},
			{element : assigmentPanelElement.find('.advanced-accordion .ui-accordion-content'), pixels : 169},
			{element : assigmentPanelElement.find('.advanced-accordion .ui-accordion-content textarea'), pixels : 177}
		];
	} // End class.
	
	// Extend CJTBLockPluginBase.
	CJTBlockPlugin.prototype = new CJTBlockPluginBase();
})(jQuery);