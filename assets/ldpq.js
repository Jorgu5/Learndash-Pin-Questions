jQuery( document ).ready( function( $ ) {
	// find element wpProQuiz_content and get ID
	const quiz_id = $( '.wpProQuiz_content' ).attr( 'ID' );
	const quiz_id_number = quiz_id.replace( 'wpProQuiz_', '' );

	$( 'input[name="startQuiz"]' ).on( 'click', function() {
		console.log('start quiz clicked');
		const observer = new MutationObserver( function( mutations ) {
			console.log('mutations', mutations);
			mutations.forEach( function( mutation ) {
				console.log('mutation', mutation);
				if ( mutation.type === 'childList' && document.querySelector('.wpProQuiz_quiz')) {
					console.log('quiz loaded');
					addPinQuestionButtons($( '.wpProQuiz_listItem' ) );
					addAjaxHandler( $( '.ldpq-save-question-button' ) );
				}
			} );
		} );
		// Configuration of the observer:
		const config = { childList: true };
		// Start observing the target node for configured mutations
		observer.observe( document.querySelector('.wpProQuiz_content'), config );
	});


	function addPinQuestionButtons(appendElement) {
		if ( ! appendElement.length ) {
			return;
		}
		console.log('addPinQuestionButtons');
		appendElement.each( function() {
			const $li = $(this),
				meta = $li.data('question-meta'),
				question_id = meta.question_post_id,
				$button = $('<button>', {
					class: 'ldpq-save-question-button wpProQuiz_button wpProQuiz_QuestionButton',
					'data-question-id': question_id,
					'data-quiz-id': quiz_id_number,
					text: 'Pin Question'
				});
			// Append the button to the li element
			$li.append( $button );
		} );
	}

	function addAjaxHandler(button) {
		if ( ! button.length ) {
			return;
		}
		console.log('addAjaxHandler');
		button.on( 'click', function() {
			const question_id = $(this).data('question-id');
			$.post( ldpq_ajax_object.ajax_url, {
				action: 'ldpq_save_question',
				question_id: question_id,
				quiz_id: $( this ).data( 'quiz-id' ),
				nonce: ldpq_ajax_object.nonce
			}, function( response ) {
				if ( response.success ) {
					console.log(response);
				} else {
					console.error(response.data.error);
				}
			} );
		} );
	}
} );
