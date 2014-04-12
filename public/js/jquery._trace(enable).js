/*
* $._trace();
*
* The ActionScript 2 function as jQuery function.
*
* Allow you to use the ActionScript function trace(); 
* $._trace('some_text');
*
* Version 0.1.2
* www.labs.skengdon.com/_trace
* www.labs.skengdon.com/_trace/js/_trace.min.js
*/
;(function($){
	theTraceObjectIUseToTraceTextIntoAndMore = null;
	$._trace = function( s ) {
		
		var eO = theTraceObjectIUseToTraceTextIntoAndMore;
		
		var eN = document.createElement('div');
		var eT = document.createTextNode( s );
		$(eN).append( eT );
		
		$(eN).css({
			'height': '20px',
			'paddingLeft': '5px'
		});
		
		// hover on new text string
		$(eN).hover(function() {
			$(this).css({'backgroundColor': '#efefef'});
		},function() {
			$(this).css({'backgroundColor': '#ffffff'});
		});
		
		if ( !eO ) {
			
			// creating the traceholder
			eO = document.createElement('div');
			// appending to body
			$('body').append(eO);
			
			// creating clearButton
			var eB = document.createElement('button');
			var eBt = document.createTextNode('clear');
			$(eB).append(eBt);
			// appending clearButton to traceholder
			$(eO).append(eB);
			// style clearButton
			$(eB).css({
				'position': 'fixed',
				'bottom': '132px',
				'right': '25px'
			});
			
			// creating clearButton click function
			$(eB).click(function() {
				var eO = theTraceObjectIUseToTraceTextIntoAndMore;
				theTraceObjectIUseToTraceTextIntoAndMore = null;
				$(eO).remove();
			});
			
			// putting in traceholder variabel
			theTraceObjectIUseToTraceTextIntoAndMore = eO;
			
			// styling traceholder
			$(eO).css({
				'position': 'fixed',
				'zIndex': '1951125',
				'bottom': '0',
				'left': '0',
				'width': '100%',
				'height': '160px',
				'lineHeight': '20px',
				'overflow': 'auto',
				'overflowY': 'scroll',
				'backgroundColor': 'white',
				'borderTop': '1px solid #afafaf'
			});
		};
		
		// insert text string
		$(eO).prepend( eN );
		// scroll to top
		eO.scrollTop = 0;
	}
})(jQuery);