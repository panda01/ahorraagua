
( function( window, settings ) {
	function wpEmoji() {
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,

		// Compression and maintain local scope
		document = window.document,

		// Private
		twemoji, timer,
		loaded = false,
		count = 0,
		ie11 = window.navigator.userAgent.indexOf( 'Trident/7.0' ) > 0;

		/**
		 * Detect if the browser supports SVG.
		 *
		 * @since 4.6.0
		 *
		 * @return {Boolean} True if the browser supports svg, false if not.
		 */
		function browserSupportsSvgAsImage() {
			if ( !! document.implementation.hasFeature ) {
				// Source: Modernizr
				// https://github.com/Modernizr/Modernizr/blob/master/feature-detects/svg/asimg.js
				return document.implementation.hasFeature( 'http://www.w3.org/TR/SVG11/feature#Image', '1.1' );
			}

			// document.implementation.hasFeature is deprecated. It can be presumed
			// if future browsers remove it, the browser will support SVGs as images.
			return true;
		}

		/**
		 * Runs when the document load event is fired, so we can do our first parse of the page.
		 *
		 * @since 4.2.0
		 */
		function load() {
			if ( loaded ) {
				return;
			}

			if ( typeof window.twemoji === 'undefined' ) {
				// Break if waiting for longer than 30 sec.
				if ( count > 600 ) {
					return;
				}

				// Still waiting.
				window.clearTimeout( timer );
				timer = window.setTimeout( load, 50 );
				count++;

				return;
			}

			twemoji = window.twemoji;
			loaded = true;

			if ( MutationObserver ) {
				new MutationObserver( function( mutationRecords ) {
					var i = mutationRecords.length,
						addedNodes, removedNodes, ii, node;

					while ( i-- ) {
						addedNodes = mutationRecords[ i ].addedNodes;
						removedNodes = mutationRecords[ i ].removedNodes;
						ii = addedNodes.length;

						if (
							ii === 1 && removedNodes.length === 1 &&
							addedNodes[0].nodeType === 3 &&
							removedNodes[0].nodeName === 'IMG' &&
							addedNodes[0].data === removedNodes[0].alt &&
							'load-failed' === removedNodes[0].getAttribute( 'data-error' )
						) {
							return;
						}

						while ( ii-- ) {
							node = addedNodes[ ii ];

							if ( node.nodeType === 3 ) {
								if ( ! node.parentNode ) {
									continue;
								}

								if ( ie11 ) {
									/*
									 * IE 11's implementation of MutationObserver is buggy.
									 * It unnecessarily splits text nodes when it encounters a HTML
									 * template interpolation symbol ( "{{", for example ). So, we
									 * join the text nodes back together as a work-around.
									 */
									while( node.nextSibling && 3 === node.nextSibling.nodeType ) {
										node.nodeValue = node.nodeValue + node.nextSibling.nodeValue;
										node.parentNode.removeChild( node.nextSibling );
									}
								}

								node = node.parentNode;
							}

							if ( ! node || node.nodeType
